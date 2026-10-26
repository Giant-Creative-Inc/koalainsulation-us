'use strict';

const assert = require( 'node:assert/strict' );
const fs = require( 'node:fs' );
const path = require( 'node:path' );
const vm = require( 'node:vm' );

const attributionScript = fs.readFileSync(
	path.join( __dirname, '..', 'assets', 'js', 'attribution.js' ),
	'utf8'
);

function runAttribution( search, initialValue, options = {} ) {
	const listeners = {};
	const key = options.key || 'gclid';
	const on = ( name, fn ) => { ( listeners[ name ] ||= [] ).push( fn ); };
	const fire = ( name ) => ( listeners[ name ] || [] ).forEach( fn => fn() );
	const input = { value: initialValue };
	const form = {
		querySelector: ( selector ) => selector === 'input[name="input_7"]' ? input : null,
	};
	let cookie = '';

	const document = {
		body: { getAttribute: () => '' },
		referrer: '',
		readyState: 'complete',
		addEventListener: on,
		querySelector: () => null,
		querySelectorAll: ( selector ) => selector === '#gform_13' ? [ form ] : [],
	};

	Object.defineProperty( document, 'cookie', {
		get: () => cookie,
		set: ( value ) => {
			cookie = value;
		},
	} );

	const window = {
		afl_wc_utm_public: options.afl,
		addEventListener: on,
		location: {
			href: 'https://example.com/quote/' + search,
			search,
		},
		sessionStorage: {
			getItem: () => '',
			setItem: () => {},
		},
	};

	vm.runInNewContext( attributionScript, {
		URLSearchParams,
		Date,
		JSON,
		RegExp,
		decodeURIComponent,
		encodeURIComponent,
		document,
		window,
		kgiData: {
			trackingFieldIds: {
				13: { [ key ]: '7' },
			},
		},
	} );

	if ( options.after ) options.after( { input, window, fire } );
	return input.value;
}

const readyAfl = value => ( {
	attribution: { status: '1', utm_last_touch: { utm_source: value } },
} );

assert.equal(
	runAttribution( '?utm_source=KOALA', 'CACHED_OTHER_VISITOR', { key: 'UtmSource', afl: readyAfl( 'AFL_CURRENT_VISITOR' ) } ),
	'AFL_CURRENT_VISITOR',
	'current-browser AFL UTMs must take priority over Koala and cached markup'
);

for ( const afl of [ undefined, {}, { attribution: { status: '0' } }, readyAfl( '' ), readyAfl( '   ' ) ] ) {
	assert.equal(
		runAttribution( '?utm_source=KOALA', 'CACHED_OTHER_VISITOR', { key: 'UtmSource', afl } ),
		'KOALA',
		'Koala must fill missing UTMs when AFL is absent, pending, or empty'
	);
}

assert.equal(
	runAttribution( '?utm_source=KOALA', '', {
		key: 'UtmSource',
		after( { window, fire } ) {
			window.afl_wc_utm_public = readyAfl( 'LATE_AFL' );
			fire( 'afl_wc_utm_event_attribution_set' );
		},
	} ),
	'LATE_AFL',
	'AFL completing later must replace the temporary Koala fallback'
);

for ( const event of [ 'pageshow', 'gform/post_render', 'submit' ] ) {
	assert.equal(
		runAttribution( '?utm_source=KOALA', '', {
			key: 'UtmSource', afl: readyAfl( 'AFL' ),
			after( { input, fire } ) { input.value = 'STALE'; fire( event ); },
		} ),
		'AFL',
		'current visitor attribution must refresh on ' + event
	);
}

assert.equal(
	runAttribution( '?gclid=THIS_VISITOR', 'CACHED_OTHER_VISITOR' ),
	'THIS_VISITOR',
	'current visitor attribution must replace a cached field value'
);

assert.equal(
	runAttribution( '', 'CACHED_OTHER_VISITOR' ),
	'',
	'a stale cached attribution value must be cleared for a direct visitor'
);

for ( const [ key, param ] of Object.entries( {
	UtmSource: 'utm_source', UtmMedium: 'utm_medium', UtmCampaign: 'utm_campaign',
	UtmTerm: 'utm_term', UtmContent: 'utm_content',
} ) ) {
	assert.equal(
		runAttribution( '?' + param + '=KOALA', 'CACHED', {
			key, afl: { attribution: { status: '1', utm_last_touch: { [ param ]: ' AFL_VALUE ' } } },
		} ),
		'AFL_VALUE',
		'AFL must supply ' + param
	);
}

assert.equal(
	runAttribution( '?utm_medium=KOALA_MEDIUM', 'CACHED', { key: 'UtmMedium', afl: readyAfl( 'AFL_SOURCE' ) } ),
	'KOALA_MEDIUM',
	'an AFL value for one field must not suppress fallback for another missing field'
);

assert.equal(
	runAttribution( '?gclid=CURRENT_CLICK', 'CACHED', { afl: readyAfl( 'AFL_SOURCE' ) } ),
	'CURRENT_CLICK',
	'Koala must continue filling click IDs independently of AFL UTMs'
);

console.log( 'attribution tests passed' );

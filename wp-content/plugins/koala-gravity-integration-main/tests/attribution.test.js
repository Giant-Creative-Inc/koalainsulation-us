'use strict';

const assert = require( 'node:assert/strict' );
const fs = require( 'node:fs' );
const path = require( 'node:path' );
const vm = require( 'node:vm' );

const attributionScript = fs.readFileSync(
	path.join( __dirname, '..', 'assets', 'js', 'attribution.js' ),
	'utf8'
);

function runAttribution( search, initialValue ) {
	const input = { value: initialValue };
	const form = {
		querySelector: ( selector ) => selector === 'input[name="input_7"]' ? input : null,
	};
	let cookie = '';

	const document = {
		body: { getAttribute: () => '' },
		referrer: '',
		readyState: 'complete',
		addEventListener: () => {},
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
		addEventListener: () => {},
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
				13: { gclid: '7' },
			},
		},
	} );

	return input.value;
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

console.log( 'attribution tests passed' );

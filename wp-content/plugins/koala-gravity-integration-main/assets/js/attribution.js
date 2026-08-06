/* global kgiData */
/**
 * Attribution capture and hidden-field population.
 *
 * Loaded site-wide (see kgi_enqueue_attribution_capture()). Two jobs:
 *
 *   1. Capture — on the visitor's first page view, snapshot the marketing
 *      attribution available at that moment (UTMs, ad-platform click IDs, the
 *      landing page URL, the referrer, and a timestamp) into a first-party
 *      `kgi_attrib` cookie. This runs on every page so it works even when the
 *      landing page is not the page the form lives on.
 *
 *   2. Fill — on pages that carry one of this plugin's forms, write those
 *      values into the form's mapped hidden fields (kgiData.trackingFieldIds).
 *      For campaign params the current page URL wins (last touch) and the
 *      cookie is the fallback (first touch); referrer / landing page always
 *      come from the first-touch cookie.
 *
 * Everything is done client-side on purpose: the site serves forms from a
 * full-page cache, so server-side render injection would bake one visitor's
 * values into the shared cached HTML. This script runs in the browser after
 * the cached HTML loads, so each visitor gets their own values.
 */
( function () {
	'use strict';

	var COOKIE_NAME = 'kgi_attrib';
	var COOKIE_DAYS = 90;
	var CTA_STORAGE_KEY = 'kgi_cta';

	// Tracking payload key => URL query parameter it is read from. Keys not in
	// this map (landing_page, referrer, form_timestamp, service, cta_text,
	// form_id) are derived rather than read from a query parameter.
	var URL_PARAM_KEYS = {
		UtmSource: 'utm_source',
		UtmMedium: 'utm_medium',
		UtmCampaign: 'utm_campaign',
		UtmTerm: 'utm_term',
		UtmContent: 'utm_content',
		gclid: 'gclid',
		gbraid: 'gbraid',
		wbraid: 'wbraid',
		fbclid: 'fbclid',
		msclkid: 'msclkid'
	};

	/**
	 * Reads a cookie value by name.
	 *
	 * @param {string} name
	 * @returns {string} Decoded value, or '' if absent.
	 */
	function getCookie( name ) {
		var match = document.cookie.match( new RegExp( '(?:^|; )' + name + '=([^;]*)' ) );
		return match ? decodeURIComponent( match[ 1 ] ) : '';
	}

	/**
	 * Writes a cookie scoped to the whole site.
	 *
	 * @param {string} name
	 * @param {string} value
	 * @param {number} days Expiry in days.
	 */
	function setCookie( name, value, days ) {
		var expires = new Date( Date.now() + days * 864e5 ).toUTCString();
		document.cookie = name + '=' + encodeURIComponent( value ) +
			'; expires=' + expires + '; path=/; SameSite=Lax';
	}

	/**
	 * Returns a URL query parameter from the current page, trimmed.
	 *
	 * @param {string} param
	 * @returns {string}
	 */
	function getQueryParam( param ) {
		try {
			var value = new URLSearchParams( window.location.search ).get( param );
			return value ? value.trim() : '';
		} catch ( e ) {
			return '';
		}
	}

	/**
	 * Reads and parses the stored first-touch attribution cookie.
	 *
	 * @returns {Object} Parsed data, or an empty object if absent/invalid.
	 */
	function getStoredAttribution() {
		var raw = getCookie( COOKIE_NAME );

		if ( ! raw ) {
			return {};
		}

		try {
			var parsed = JSON.parse( raw );
			return parsed && typeof parsed === 'object' ? parsed : {};
		} catch ( e ) {
			return {};
		}
	}

	/**
	 * Snapshots first-touch attribution into the cookie, once.
	 *
	 * Skips entirely if the cookie already exists, so the stored values keep
	 * the visitor's *first* landing page, referrer, and campaign — later visits
	 * never overwrite them. Runs on every page load.
	 */
	function captureFirstTouch() {
		if ( getCookie( COOKIE_NAME ) ) {
			return;
		}

		var data = {
			landing_page: window.location.href,
			referrer: document.referrer || '',
			ts: new Date().toISOString()
		};

		Object.keys( URL_PARAM_KEYS ).forEach( function ( key ) {
			var value = getQueryParam( URL_PARAM_KEYS[ key ] );

			if ( value ) {
				data[ key ] = value;
			}
		} );

		setCookie( COOKIE_NAME, JSON.stringify( data ), COOKIE_DAYS );
	}

	/**
	 * Remembers the text of a clicked call-to-action for the next form fill.
	 *
	 * Only elements explicitly marked with `data-kgi-cta` are captured, so
	 * incidental link/button clicks don't pollute the value. The attribute's
	 * own value is used as the CTA label when set, otherwise the element's
	 * visible text. Stored in sessionStorage so it survives the navigation to
	 * a form on another page but not a new browsing session.
	 */
	function bindCtaCapture() {
		document.addEventListener( 'click', function ( event ) {
			var target = event.target;

			if ( ! target || ! target.closest ) {
				return;
			}

			var cta = target.closest( '[data-kgi-cta]' );

			if ( ! cta ) {
				return;
			}

			var label = cta.getAttribute( 'data-kgi-cta' ) || ( cta.textContent || '' ).trim();

			if ( label ) {
				try {
					window.sessionStorage.setItem( CTA_STORAGE_KEY, label.slice( 0, 250 ) );
				} catch ( e ) {
					// sessionStorage unavailable (private mode / disabled) — ignore.
				}
			}
		}, true );
	}

	/**
	 * Resolves the "service" value from page context.
	 *
	 * A page declares its service with `<body data-kgi-service="...">` or
	 * `<meta name="kgi-service" content="...">`. No URL-path fallback: on this
	 * site the first path segment is the franchise location slug, not a
	 * service, so guessing from the path would mislabel the field.
	 *
	 * @returns {string}
	 */
	function resolveService() {
		var fromBody = document.body ? ( document.body.getAttribute( 'data-kgi-service' ) || '' ) : '';

		if ( fromBody ) {
			return fromBody.trim();
		}

		var meta = document.querySelector( 'meta[name="kgi-service"]' );

		return meta && meta.content ? meta.content.trim() : '';
	}

	/**
	 * Resolves the CTA text: a clicked, remembered CTA first, then a page-level
	 * `<body data-kgi-cta="...">` default.
	 *
	 * @returns {string}
	 */
	function resolveCtaText() {
		var stored = '';

		try {
			stored = window.sessionStorage.getItem( CTA_STORAGE_KEY ) || '';
		} catch ( e ) {
			stored = '';
		}

		if ( stored ) {
			return stored;
		}

		return document.body ? ( document.body.getAttribute( 'data-kgi-cta' ) || '' ).trim() : '';
	}

	/**
	 * Computes the value for a single tracking key.
	 *
	 * @param {string} key    Tracking payload key.
	 * @param {Object} stored First-touch cookie data.
	 * @param {string|number} formId
	 * @returns {string}
	 */
	function resolveValue( key, stored, formId ) {
		if ( URL_PARAM_KEYS.hasOwnProperty( key ) ) {
			// Current URL (last touch) wins; first-touch cookie is the fallback.
			return getQueryParam( URL_PARAM_KEYS[ key ] ) || ( stored[ key ] || '' );
		}

		switch ( key ) {
			case 'landing_page':
				return stored.landing_page || window.location.href;
			case 'referrer':
				return stored.referrer || document.referrer || '';
			case 'form_timestamp':
				return new Date().toISOString();
			case 'service':
				return resolveService();
			case 'cta_text':
				return resolveCtaText();
			default:
				return '';
		}
	}

	/**
	 * Fills every mapped tracking hidden field on every form present.
	 *
	 * Reads kgiData.trackingFieldIds (form ID => { trackingKey => GF field ID })
	 * localized on form pages. Only writes into an input that is currently
	 * empty, so a value already supplied (e.g. by Gravity Forms' own dynamic
	 * population) is never clobbered. Uses querySelectorAll and walks each known
	 * form ID for the same duplicate-form / multi-form reasons as
	 * form-validation.js.
	 */
	function fillForms() {
		if ( typeof kgiData === 'undefined' || ! kgiData || ! kgiData.trackingFieldIds ) {
			return;
		}

		var stored = getStoredAttribution();
		var trackingFieldIds = kgiData.trackingFieldIds;

		Object.keys( trackingFieldIds ).forEach( function ( formId ) {
			var fieldMap = trackingFieldIds[ formId ];

			document.querySelectorAll( '#gform_' + formId ).forEach( function ( form ) {
				Object.keys( fieldMap ).forEach( function ( key ) {
					var fieldId = fieldMap[ key ];
					var input = form.querySelector( 'input[name="input_' + fieldId + '"]' );

					if ( ! input || input.value ) {
						return;
					}

					var value = resolveValue( key, stored, formId );

					if ( value ) {
						input.value = value;
					}
				} );
			} );
		} );
	}

	/**
	 * Runs a callback once the DOM is ready.
	 *
	 * @param {Function} fn
	 */
	function onReady( fn ) {
		if ( 'loading' !== document.readyState ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	captureFirstTouch();
	bindCtaCapture();
	onReady( fillForms );

	// Re-fill after Gravity Forms rebuilds the DOM on multi-page or AJAX forms.
	// gform_post_render is a jQuery-triggered event, so bind it via jQuery when
	// present (it always is on a page with a Gravity Form).
	if ( window.jQuery ) {
		window.jQuery( document ).on( 'gform_post_render', fillForms );
	}
} )();

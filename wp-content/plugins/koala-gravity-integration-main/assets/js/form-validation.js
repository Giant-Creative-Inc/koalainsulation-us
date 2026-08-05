/* global kgiData, jQuery */
( function ( $ ) {
	'use strict';

	/**
	 * Formats a digit string into a phone number display string.
	 *
	 * 10 digits → (999) 999-9999
	 * 11 digits → 9 (999) 999-9999
	 *
	 * @param {string} digits Raw digits only, max 11 characters.
	 * @returns {string} Formatted phone string.
	 */
	function formatPhone( digits ) {
		if ( ! digits.length ) {
			return '';
		}

		if ( digits.length <= 10 ) {
			if ( digits.length <= 3 ) {
				return '(' + digits;
			}
			if ( digits.length <= 6 ) {
				return '(' + digits.slice( 0, 3 ) + ') ' + digits.slice( 3 );
			}
			return '(' + digits.slice( 0, 3 ) + ') ' + digits.slice( 3, 6 ) + '-' + digits.slice( 6, 10 );
		}

		// 11 digits — single-digit country code
		return digits.charAt( 0 ) + ' (' + digits.slice( 1, 4 ) + ') ' + digits.slice( 4, 7 ) + '-' + digits.slice( 7, 11 );
	}

	/**
	 * Binds the phone mask to a single input element.
	 *
	 * Uses a data attribute to guard against double-binding on AJAX re-renders.
	 *
	 * @param {HTMLInputElement} input
	 */
	function applyMask( input ) {
		if ( input.dataset.kgiMasked ) {
			return;
		}
		input.dataset.kgiMasked = '1';

		// Re-format on every keystroke.
		input.addEventListener( 'input', function () {
			this.value = formatPhone( this.value.replace( /\D/g, '' ).slice( 0, 11 ) );
		} );

		// Block non-digit keys before they reach the input.
		input.addEventListener( 'keydown', function ( e ) {
			// Always allow: backspace, delete, tab, escape, enter, home, end, arrows.
			if ( [ 8, 9, 13, 27, 35, 36, 37, 38, 39, 40, 46 ].indexOf( e.keyCode ) !== -1 ) {
				return;
			}
			// Always allow: Ctrl/Cmd + A, C, V, X, Z.
			if ( ( e.ctrlKey || e.metaKey ) && [ 65, 67, 86, 88, 90 ].indexOf( e.keyCode ) !== -1 ) {
				return;
			}
			// Block anything that is not a digit (top-row 0–9 or numpad 0–9).
			if ( ( e.keyCode < 48 || e.keyCode > 57 ) && ( e.keyCode < 96 || e.keyCode > 105 ) ) {
				e.preventDefault();
			}
		} );

		// Strip non-digits from pasted content and re-format.
		input.addEventListener( 'paste', function ( e ) {
			e.preventDefault();
			var pasted = ( e.clipboardData || window.clipboardData ).getData( 'text' );
			this.value = formatPhone( pasted.replace( /\D/g, '' ).slice( 0, 11 ) );
		} );
	}

	/**
	 * Queries every form this plugin manages (kgiData.formIds — the main
	 * quote form, any additional quote forms, and any fixed-location forms;
	 * see kgi_enqueue_quote_form_assets()) for phone inputs and applies the
	 * mask to each.
	 *
	 * Uses querySelectorAll rather than getElementById for two reasons: a
	 * given form ID can appear more than once in the DOM (e.g. the same
	 * Gravity Form embedded both inline and in a popup) — getElementById
	 * only ever returns the first match — and a page can also have more
	 * than one *different* form ID present at once (e.g. an inline quote
	 * form plus a distinct popup form), which this loop covers by walking
	 * every known form ID rather than assuming just one.
	 */
	function initPhoneMasks() {
		( kgiData.formIds || [] ).forEach( function ( formId ) {
			document.querySelectorAll( '#gform_' + formId ).forEach( function ( form ) {
				form.querySelectorAll( 'input[type="tel"]' ).forEach( applyMask );
			} );
		} );
	}

	$( document ).ready( initPhoneMasks );

	// Re-run after GF rebuilds the DOM on multi-page or AJAX forms.
	$( document ).on( 'gform_post_render', initPhoneMasks );

	/**
	 * Cleans up a raw ZIP/postal code value as the user types.
	 *
	 * Uppercases letters (Canadian postal codes) and strips anything that
	 * isn't a letter, digit, space, or dash, so stray characters can't reach
	 * submission. Deliberately not a strict positional mask like the phone
	 * field — US ZIP (12345, 12345-6789) and Canadian postal codes (A1A 1A1)
	 * have different shapes, so this only normalizes, it doesn't reformat.
	 *
	 * @param {string} raw
	 * @returns {string} Cleaned value, max 10 characters.
	 */
	function formatZip( raw ) {
		return raw.toUpperCase().replace( /[^A-Z0-9 -]/g, '' ).slice( 0, 10 );
	}

	/**
	 * Binds ZIP/postal cleanup to a single input element.
	 *
	 * @param {HTMLInputElement} input
	 */
	function applyZipFormat( input ) {
		if ( input.dataset.kgiZipFormatted ) {
			return;
		}
		input.dataset.kgiZipFormatted = '1';

		input.addEventListener( 'input', function () {
			this.value = formatZip( this.value );
		} );
	}

	/**
	 * For every form this plugin manages that has a ZIP/postal field mapped
	 * (kgiData.zipFieldIds — form ID => that form's own zip field ID, since
	 * each form has its own field IDs), applies cleanup to each instance of
	 * that field. Same multi-form, multi-instance handling as
	 * initPhoneMasks() — see its comment for why querySelectorAll is used
	 * instead of getElementById.
	 */
	function initZipFormatting() {
		var zipFieldIds = kgiData.zipFieldIds || {};

		Object.keys( zipFieldIds ).forEach( function ( formId ) {
			var zipFieldId = zipFieldIds[ formId ];
			document.querySelectorAll( '#gform_' + formId ).forEach( function ( form ) {
				form.querySelectorAll( 'input[name="input_' + zipFieldId + '"]' ).forEach( applyZipFormat );
			} );
		} );
	}

	$( document ).ready( initZipFormatting );

	// Re-run after GF rebuilds the DOM on multi-page or AJAX forms.
	$( document ).on( 'gform_post_render', initZipFormatting );

	/**
	 * Works around a Gravity Forms limitation when the same form is embedded
	 * more than once on a page (e.g. an inline copy plus a popup copy).
	 *
	 * GF's AJAX handling resolves `#gform_wrapper_{id}` by ID at every step —
	 * the iframe it submits to, the load handler bound to that iframe, and
	 * the final `.html()` write that injects a validation-failure re-render.
	 * With a duplicate ID in the DOM, all of that always resolves to the
	 * *first* copy, so a validation error triggered by the second (e.g.
	 * popup) copy gets rendered into the first copy instead.
	 *
	 * Successful submissions aren't affected — this plugin's confirmations
	 * are always `redirect` type, and GF performs those via
	 * `document.location.href`, a full top-page navigation that isn't
	 * scoped to any particular wrapper.
	 *
	 * This only reacts after GF has already (mis)written the error markup —
	 * it doesn't touch GF's submission/transport — so it moves just that
	 * rendered output to the copy that was actually submitted, then puts
	 * the first copy back to its original state. A no-op for any form ID
	 * that only appears once on the page — which is the normal case now
	 * that a page can have several *different* forms (see initPhoneMasks()),
	 * each checked independently by its own form ID.
	 *
	 * @param {string|number} formId
	 */
	function initDuplicateFormErrorRoutingForForm( formId ) {
		var wrapperSelector = '[id="gform_wrapper_' + formId + '"]';
		var wrappers = document.querySelectorAll( wrapperSelector );

		if ( wrappers.length < 2 ) {
			return;
		}

		var firstWrapper = wrappers[ 0 ];
		var pristineHtml = firstWrapper.innerHTML;
		var pristineClassName = firstWrapper.className;
		var activeWrapper = firstWrapper;
		var activePristineClassName = pristineClassName;
		var isRestoring = false;

		// Snapshot each wrapper's own original class list up front, since a
		// popup copy's wrapper can legitimately carry different classes
		// (e.g. from the popup container) than the inline copy's.
		var pristineClassNames = new window.Map();
		wrappers.forEach( function ( wrapper ) {
			pristineClassNames.set( wrapper, wrapper.className );
		} );

		// Capture phase so this runs before GF's own submit handling, and
		// tracks the wrapper that actually owns the submitted <form> — not
		// necessarily the first one in DOM order.
		document.addEventListener(
			'submit',
			function ( e ) {
				if ( ! e.target || e.target.id !== 'gform_' + formId ) {
					return;
				}
				activeWrapper = e.target.closest( wrapperSelector ) || firstWrapper;
				activePristineClassName = pristineClassNames.get( activeWrapper ) || pristineClassName;
			},
			true
		);

		$( document ).on( 'gform_post_render', function ( event, eventFormId ) {
			if ( isRestoring || Number( eventFormId ) !== Number( formId ) || activeWrapper === firstWrapper ) {
				return;
			}

			var hasError = firstWrapper.querySelector( '.gfield_error, .validation_error, .gform_validation_errors' );
			if ( ! hasError ) {
				return;
			}

			activeWrapper.className = activePristineClassName + ( firstWrapper.classList.contains( 'gform_validation_error' ) ? ' gform_validation_error' : '' );
			activeWrapper.innerHTML = firstWrapper.innerHTML;

			isRestoring = true;
			firstWrapper.innerHTML = pristineHtml;
			firstWrapper.className = pristineClassName;
			isRestoring = false;

			initPhoneMasks();
			initZipFormatting();

			activeWrapper.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			var firstErrorField = activeWrapper.querySelector( '.gfield_error input, .gfield_error select, .gfield_error textarea' );
			if ( firstErrorField ) {
				firstErrorField.focus();
			}
		} );
	}

	function initDuplicateFormErrorRouting() {
		( kgiData.formIds || [] ).forEach( initDuplicateFormErrorRoutingForForm );
	}

	$( document ).ready( initDuplicateFormErrorRouting );

} )( jQuery );

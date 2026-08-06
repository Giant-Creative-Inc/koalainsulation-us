/* global kgiThankYouData */
( function () {
	'use strict';

	if ( ! kgiThankYouData || ! kgiThankYouData.entryId ) {
		return;
	}

	// Guards against a duplicate push on a refresh or repeat visit to the same
	// signed thank-you URL — sessionStorage so it only suppresses re-fires
	// within the same tab/session, not across different visitors.
	var storageKey = 'kgiDataLayerSent_' + kgiThankYouData.entryId;

	try {
		if ( window.sessionStorage.getItem( storageKey ) ) {
			return;
		}
	} catch ( e ) {
		// sessionStorage unavailable (private browsing, etc.) — proceed without
		// the dedupe guard rather than silently dropping the event.
	}

	var pushed = false;

	/**
	 * Pushes the event exactly once and records it so a refresh doesn't
	 * duplicate the conversion.
	 */
	function pushEvent() {
		if ( pushed ) {
			return;
		}
		pushed = true;

		try {
			window.sessionStorage.setItem( storageKey, '1' );
		} catch ( e ) {
			// sessionStorage unavailable — ignore, the in-page `pushed` flag
			// still prevents a double push within this load.
		}

		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push( kgiThankYouData.payload );
	}

	/**
	 * Whether the GTM container runtime has initialized on the page yet.
	 *
	 * The form's confirmation redirect can land here before the GTM snippet has
	 * finished loading, in which case a push made now is not captured as a live
	 * event and its triggers never evaluate. Detected via the container runtime
	 * object, with the `gtm.js` dataLayer event as a fallback signal.
	 *
	 * @returns {boolean}
	 */
	function gtmReady() {
		if ( window.google_tag_manager ) {
			return true;
		}

		return !! (
			window.dataLayer &&
			typeof window.dataLayer.some === 'function' &&
			window.dataLayer.some( function ( entry ) {
				return entry && entry.event === 'gtm.js';
			} )
		);
	}

	// Push as soon as GTM is ready so the event fires as a live event GTM can
	// trigger on. If GTM never appears within the timeout, push anyway so the
	// event is at least present in the dataLayer rather than lost entirely.
	if ( gtmReady() ) {
		pushEvent();
		return;
	}

	var attempts = 0;
	var maxAttempts = 100; // ~10s at 100ms intervals.
	var interval = window.setInterval( function () {
		attempts++;

		if ( gtmReady() || attempts >= maxAttempts ) {
			window.clearInterval( interval );
			pushEvent();
		}
	}, 100 );
} )();

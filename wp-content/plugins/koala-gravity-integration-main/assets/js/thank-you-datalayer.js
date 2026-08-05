/* global kgiThankYouData */
( function () {
	'use strict';

	if ( ! kgiThankYouData || ! kgiThankYouData.entryId ) {
		return;
	}

	// Guards against a duplicate push on a refresh or repeat visit to the
	// same signed thank-you URL — sessionStorage so it only suppresses
	// re-fires within the same tab/session, not across different visitors.
	var storageKey = 'kgiDataLayerSent_' + kgiThankYouData.entryId;

	try {
		if ( window.sessionStorage.getItem( storageKey ) ) {
			return;
		}
		window.sessionStorage.setItem( storageKey, '1' );
	} catch ( e ) {
		// sessionStorage unavailable (private browsing, etc.) — fall through
		// and push anyway rather than silently dropping the event.
	}

	window.dataLayer = window.dataLayer || [];
	window.dataLayer.push( kgiThankYouData.payload );
} )();

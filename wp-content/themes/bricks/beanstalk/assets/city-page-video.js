( function () {
	'use strict';

	document.addEventListener( 'click', function ( event ) {
		const trigger = event.target.closest( '.koala-city-why-koala__video-trigger' );
		if ( ! trigger ) {
			return;
		}

		const container = trigger.closest( '.koala-city-why-koala__media' );
		const iframe = container ? container.querySelector( '.koala-city-why-koala__video' ) : null;
		const source = container ? container.dataset.videoSrc : '';
		if ( ! iframe || ! source || iframe.src ) {
			return;
		}

		const videoUrl = new URL( source );
		videoUrl.searchParams.set( 'autoplay', '1' );
		iframe.src = videoUrl.toString();
		iframe.hidden = false;
		container.classList.add( 'is-playing' );
	} );
}() );

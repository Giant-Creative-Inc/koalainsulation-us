( function () {
	'use strict';

	var form = document.getElementById( 'city-page-quote' );
	var quoteLinks = document.querySelectorAll( '.koala-city-page__quote-cta[href="#city-page-quote"]' );
	var observer = null;
	var fallbackTimer = null;
	var reducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	if ( ! form || ! quoteLinks.length ) {
		return;
	}

	function formIsInView() {
		var rect = form.getBoundingClientRect();

		return rect.top < window.innerHeight * 0.85 && rect.bottom > 80;
	}

	function shakeForm() {
		if ( reducedMotion ) {
			return;
		}

		form.classList.remove( 'is-quote-attention' );
		void form.offsetWidth;
		form.classList.add( 'is-quote-attention' );
	}

	function shakeWhenVisible() {
		if ( reducedMotion ) {
			return;
		}

		if ( observer ) {
			observer.disconnect();
			observer = null;
		}

		window.clearTimeout( fallbackTimer );

		if ( formIsInView() ) {
			shakeForm();
			return;
		}

		if ( 'IntersectionObserver' in window ) {
			observer = new IntersectionObserver( function ( entries ) {
				if ( entries[ 0 ].isIntersecting ) {
					observer.disconnect();
					observer = null;
					window.clearTimeout( fallbackTimer );
					shakeForm();
				}
			}, {
				rootMargin: '-80px 0px -15% 0px',
				threshold: 0.2
			} );
			observer.observe( form );
		}

		fallbackTimer = window.setTimeout( function () {
			if ( formIsInView() ) {
				if ( observer ) {
					observer.disconnect();
					observer = null;
				}
				shakeForm();
			}
		}, 800 );
	}

	quoteLinks.forEach( function ( link ) {
		link.addEventListener( 'click', shakeWhenVisible );
	} );

	form.addEventListener( 'animationend', function ( event ) {
		if ( 'koala-city-form-shake' === event.animationName ) {
			form.classList.remove( 'is-quote-attention' );
		}
	} );
}() );

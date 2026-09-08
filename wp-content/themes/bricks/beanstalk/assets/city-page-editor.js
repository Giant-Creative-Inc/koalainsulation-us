( function ( blockEditor, blocks, element, i18n ) {
	'use strict';

	blocks.registerBlockType( 'koala/location-phone', {
		apiVersion: 3,
		title: i18n.__( 'Location Phone', 'koala' ),
		description: i18n.__( 'Displays the related location phone number on the frontend.', 'koala' ),
		category: 'widgets',
		icon: 'phone',
		attributes: {
			label: {
				type: 'string',
				default: '',
			},
		},
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-location-phone-editor-placeholder' },
				i18n.__( 'Related location phone number', 'koala' )
			);
		},
		save: function () {
			return null;
		},
	} );

	blocks.registerBlockType( 'koala/location-review-summary', {
		apiVersion: 3,
		title: i18n.__( 'Location Google Rating', 'koala' ),
		description: i18n.__( 'Displays the cached Google rating and review count for the related location.', 'koala' ),
		category: 'widgets',
		icon: 'star-filled',
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-location-review-summary-editor-placeholder' },
				i18n.__( 'Related location Google rating and verified review count', 'koala' )
			);
		},
		save: function () {
			return null;
		},
	} );

	blocks.registerBlockType( 'koala/location-gravity-form', {
		apiVersion: 3,
		title: i18n.__( 'Location Gravity Form', 'koala' ),
		description: i18n.__( 'Displays Gravity Form 13 or the form configured for the related location.', 'koala' ),
		category: 'widgets',
		icon: 'feedback',
		attributes: {
			formId: {
				type: 'integer',
				default: 13,
			},
		},
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-location-gravity-form-editor-placeholder' },
				element.createElement( 'strong', null, i18n.__( 'Location Gravity Form', 'koala' ) ),
				element.createElement( 'p', null, i18n.__( 'The real form is rendered on the frontend. Edit its submit label below.', 'koala' ) ),
				element.createElement( blockEditor.InnerBlocks, {
					allowedBlocks: [ 'core/paragraph' ],
					templateLock: 'all',
				} )
			);
		},
		save: function () {
			return element.createElement( blockEditor.InnerBlocks.Content );
		},
	} );

	blocks.registerBlockType( 'koala/location-service-card-link', {
		apiVersion: 3,
		title: i18n.__( 'Location Service Card Link', 'koala' ),
		description: i18n.__( 'Links an editable service card to the matching service for the related location.', 'koala' ),
		category: 'widgets',
		icon: 'admin-links',
		attributes: {
			serviceKey: {
				type: 'string',
				default: '',
			},
		},
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-location-service-card-link-editor' },
				element.createElement( blockEditor.InnerBlocks, {
					allowedBlocks: [ 'core/group' ],
					templateLock: 'all',
				} )
			);
		},
		save: function () {
			return element.createElement( blockEditor.InnerBlocks.Content );
		},
	} );

	blocks.registerBlockType( 'koala/location-services', {
		apiVersion: 3,
		title: i18n.__( 'Related Location Services', 'koala' ),
		description: i18n.__( 'Displays every published service related to this page’s location.', 'koala' ),
		category: 'widgets',
		icon: 'grid-view',
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-location-services-editor' },
				i18n.__( 'Service cards are loaded from the related location when the page is viewed.', 'koala' )
			);
		},
		save: function () {
			return null;
		},
	} );

	blocks.registerBlockType( 'koala/location-service-cta', {
		apiVersion: 3,
		title: i18n.__( 'Location Service CTA', 'koala' ),
		description: i18n.__( 'Links an editable CTA label to the matching service for the related location.', 'koala' ),
		category: 'widgets',
		icon: 'button',
		attributes: {
			serviceKey: {
				type: 'string',
				default: '',
			},
		},
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-location-service-cta-editor' },
				element.createElement( blockEditor.InnerBlocks, {
					allowedBlocks: [ 'core/paragraph' ],
					templateLock: 'all',
				} )
			);
		},
		save: function () {
			return element.createElement( blockEditor.InnerBlocks.Content );
		},
	} );

	blocks.registerBlockType( 'koala/city-page-quote-cta', {
		apiVersion: 3,
		title: i18n.__( 'City Page Quote CTA', 'koala' ),
		description: i18n.__( 'Links an editable CTA label to the quote form on this page.', 'koala' ),
		category: 'widgets',
		icon: 'button',
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-city-page-quote-cta-editor' },
				element.createElement( blockEditor.InnerBlocks, {
					allowedBlocks: [ 'core/paragraph' ],
					templateLock: 'all',
				} )
			);
		},
		save: function () {
			return element.createElement( blockEditor.InnerBlocks.Content );
		},
	} );

	blocks.registerBlockType( 'koala/why-koala-video', {
		apiVersion: 3,
		title: i18n.__( 'Why Koala Video', 'koala' ),
		description: i18n.__( 'Links the editable image to the approved What to Expect video.', 'koala' ),
		category: 'media',
		icon: 'controls-play',
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-why-koala-video-editor' },
				element.createElement( blockEditor.InnerBlocks, {
					allowedBlocks: [ 'core/image' ],
					templateLock: 'all',
				} )
			);
		},
		save: function () {
			return element.createElement( blockEditor.InnerBlocks.Content );
		},
	} );

	blocks.registerBlockType( 'koala/location-reviews', {
		apiVersion: 3,
		title: i18n.__( 'Location Reviews', 'koala' ),
		description: i18n.__( 'Loads the review widget configured for the related location on the frontend.', 'koala' ),
		category: 'widgets',
		icon: 'star-filled',
		edit: function () {
			return element.createElement(
				'div',
				{ className: 'koala-location-reviews-editor' },
				element.createElement( 'strong', null, i18n.__( 'Related location reviews', 'koala' ) ),
				element.createElement( 'p', null, i18n.__( 'The configured review provider loads near the viewport. Edit the fallback link label below.', 'koala' ) ),
				element.createElement( blockEditor.InnerBlocks, {
					allowedBlocks: [ 'core/paragraph' ],
					templateLock: 'all',
				} )
			);
		},
		save: function () {
			return element.createElement( blockEditor.InnerBlocks.Content );
		},
	} );
}( window.wp.blockEditor, window.wp.blocks, window.wp.element, window.wp.i18n ) );

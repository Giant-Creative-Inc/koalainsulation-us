<?php
/**
 * Gutenberg editing for the Beanstalk header and footer.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the editable content-part post type and dynamic blocks. */
function koala_beanstalk_register_content_parts(): void {
	register_post_type(
		'beanstalk-part',
		array(
			'labels'          => array(
				'name'          => 'Beanstalk Parts',
				'singular_name' => 'Beanstalk Part',
				'menu_name'     => 'Beanstalk Parts',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'themes.php',
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-layout',
			'supports'        => array( 'title', 'editor', 'revisions' ),
			'capability_type' => 'page',
			'map_meta_cap'    => true,
		)
	);

	$editor_script_path = __DIR__ . '/assets/parts-editor.js';

	wp_register_script(
		'koala-beanstalk-parts-editor',
		get_template_directory_uri() . '/beanstalk/assets/parts-editor.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		is_readable( $editor_script_path ) ? (string) filemtime( $editor_script_path ) : null,
		true
	);

	register_block_type(
		'beanstalk/header',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-parts-editor',
			'attributes'      => koala_beanstalk_get_header_block_attributes(),
			'render_callback' => 'koala_beanstalk_render_header_block',
		)
	);
	register_block_type(
		'beanstalk/footer',
		array(
			'api_version'     => 3,
			'editor_script'   => 'koala-beanstalk-parts-editor',
			'attributes'      => koala_beanstalk_get_footer_block_attributes(),
			'render_callback' => 'koala_beanstalk_render_footer_block',
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_content_parts' );

/**
 * Get the header block attribute schema.
 *
 * @return array<string, array<string, string>>
 */
function koala_beanstalk_get_header_block_attributes(): array {
	return array(
		'viewAllLocationsLabel' => array(
			'type'    => 'string',
			'default' => 'View All Locations',
		),
		'servicesLabel'         => array(
			'type'    => 'string',
			'default' => 'Services',
		),
		'whyKoalaLabel'         => array(
			'type'    => 'string',
			'default' => 'Why Koala?',
		),
		'whyReinsulateLabel'    => array(
			'type'    => 'string',
			'default' => 'Why Reinsulate?',
		),
		'resourcesLabel'        => array(
			'type'    => 'string',
			'default' => 'Resources',
		),
	);
}

/**
 * Get the footer block attribute schema.
 *
 * @return array<string, array<string, string>>
 */
function koala_beanstalk_get_footer_block_attributes(): array {
	$defaults = array(
		'homeLabel'          => 'Home',
		'whyKoalaLabel'      => 'Why Koala',
		'whyReinsulateLabel' => 'Why Reinsulate',
		'servicesLabel'      => 'Services',
		'resourcesLabel'     => 'Resources',
		'contactLabel'       => 'Contact Us',
		'factOne'            => '*90% of homes are underinsulated',
		'factTwo'            => '*44% of the homes energy is used for heating and cooling',
		'factThree'          => '*70% potential energy loss from poor insulation and air sealing',
		'financingText'      => 'All financing is subject to credit approval. Your terms may vary. Payment options through Wisetack are provided by our lending partners. For example, a $1,200 purchase could cost $104.89 a month for 12 months, based on an 8.9% APR, or $400 a month for 3 months, based on a 0% APR. Offers range from 0-35.9% APR based on creditworthiness. State interest rate caps may apply. No other financing charges or participation fees.',
		'financingLinkLabel' => 'See additional terms at https://www.wisetack.com/faqs.',
		'copyrightSuffix'    => 'Koala Insulation. All rights reserved.',
		'privacyLabel'       => 'Privacy Policy',
		'termsLabel'         => 'Terms and Conditions',
	);

	return array_map(
		static fn( $default_value ) => array(
			'type'    => 'string',
			'default' => $default_value,
		),
		$defaults
	);
}

/**
 * Render an allowed Beanstalk component file.
 *
 * @param string               $component Component slug.
 * @param array<string, mixed> $attributes Dynamic block attributes.
 * @return string
 */
function koala_beanstalk_render_component( string $component, array $attributes ): string {
	if ( ! in_array( $component, array( 'header', 'footer' ), true ) ) {
		return '';
	}

	$beanstalk_settings = $attributes;
	ob_start();
	require __DIR__ . '/' . $component . '.php';

	return (string) ob_get_clean();
}

/**
 * Render the dynamic header block.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return string
 */
function koala_beanstalk_render_header_block( array $attributes ): string {
	return koala_beanstalk_render_component( 'header', $attributes );
}

/**
 * Render the dynamic footer block.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return string
 */
function koala_beanstalk_render_footer_block( array $attributes ): string {
	return koala_beanstalk_render_component( 'footer', $attributes );
}

/**
 * Render an editable content part or its component fallback.
 *
 * @param string $slug Content-part slug.
 */
function koala_beanstalk_render_part( string $slug ): void {
	$part = get_page_by_path( $slug, OBJECT, 'beanstalk-part' );
	if ( $part instanceof WP_Post && 'publish' === $part->post_status ) {
		echo do_blocks( $part->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	echo koala_beanstalk_render_component( $slug, array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/** Create the two editable parts once, without touching existing copies. */
function koala_beanstalk_maybe_seed_content_parts(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( get_option( 'koala_beanstalk_parts_seeded' ) ) {
		return;
	}

	foreach ( array(
		'header' => 'Beanstalk Header',
		'footer' => 'Beanstalk Footer',
	) as $slug => $title ) {
		if ( get_page_by_path( $slug, OBJECT, 'beanstalk-part' ) ) {
			continue;
		}
		wp_insert_post(
			array(
				'post_type'    => 'beanstalk-part',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $title,
				'post_content' => '<!-- wp:beanstalk/' . $slug . ' /-->',
			)
		);
	}

	update_option( 'koala_beanstalk_parts_seeded', '1', false );
}
add_action( 'admin_init', 'koala_beanstalk_maybe_seed_content_parts' );

<?php
/**
 * Location service tabs and Who We Are section, backed by existing ACF fields.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read the same ordered location-service relationship used by Bricks.
 *
 * @param int $id Location ID.
 * @return array
 */
function koala_beanstalk_location_services_data( int $id ): array {
	$data = array(
		'title'       => (string) get_post_meta( $id, 'services_title', true ),
		'description' => (string) get_post_meta( $id, 'services_description', true ),
		'items'       => array(),
	);
	$ids  = get_post_meta( $id, 'location_service', true );
	foreach ( is_array( $ids ) ? $ids : array() as $service_id ) {
		$service_id = (int) $service_id;
		if ( 'publish' !== get_post_status( $service_id ) || 'location-service' !== get_post_type( $service_id ) ) {
			continue;
		}
		$data['items'][] = array(
			'id'          => $service_id,
			'name'        => (string) get_post_meta( $service_id, 'location_service_name', true ),
			'location'    => (string) get_post_meta( $service_id, 'related_location_name', true ),
			'description' => (string) get_post_meta( $service_id, 'location_service_short_description', true ),
			'image'       => (int) get_post_meta( $service_id, 'location_service_image', true ),
			'icon'        => (int) get_post_meta( $service_id, 'location_service_icon', true ),
		);
	}
	return $data;
}

/** Render the responsive services and about layout from existing field values. */
function koala_beanstalk_render_location_services(): string {
	$id     = (int) get_the_ID();
	$data   = koala_beanstalk_location_services_data( $id );
	$prefix = wp_unique_id( 'location-services-' );
	ob_start();
	?>
	<section class="koala-location-services" data-location-services>
		<div class="koala-location-services__layout">
			<div class="koala-location-services__menu">
				<?php if ( '' !== trim( $data['title'] ) ) : ?>
				<h2><?php echo esc_html( $data['title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== trim( $data['description'] ) ) : ?>
				<div class="koala-location-services__intro"><?php echo wp_kses_post( wpautop( $data['description'] ) ); ?></div>
				<?php endif; ?>
				<?php if ( $data['items'] ) : ?>
				<div class="koala-location-services__tabs" role="tablist" aria-label="Insulation services" aria-orientation="vertical">
					<?php foreach ( $data['items'] as $index => $item ) : ?>
					<button class="koala-location-services__tab" type="button" role="tab" id="<?php echo esc_attr( $prefix . '-tab-' . $index ); ?>" aria-controls="<?php echo esc_attr( $prefix . '-panel-' . $index ); ?>" aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $index ? '0' : '-1'; ?>">
						<?php echo $item['icon'] ? wp_get_attachment_image( $item['icon'], 'thumbnail', false, array( 'alt' => '' ) ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attachment markup. ?>
						<span><?php echo esc_html( $item['name'] ); ?></span>
					</button>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
			<?php if ( $data['items'] ) : ?>
			<div class="koala-location-services__panels">
				<?php foreach ( $data['items'] as $index => $item ) : ?>
				<div class="koala-location-services__panel" role="tabpanel" id="<?php echo esc_attr( $prefix . '-panel-' . $index ); ?>" aria-labelledby="<?php echo esc_attr( $prefix . '-tab-' . $index ); ?>" tabindex="0" <?php echo 0 === $index ? '' : 'hidden'; ?>>
					<?php if ( $item['image'] ) : ?>
					<div class="koala-location-services__image"><?php echo wp_get_attachment_image( $item['image'], 'large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attachment markup. ?></div>
					<?php endif; ?>
					<h3><?php echo esc_html( trim( $item['name'] . ' ' . $item['location'] ) ); ?></h3>
					<div class="koala-location-services__description"><?php echo wp_kses_post( wpautop( $item['description'] ) ); ?></div>
					<?php echo koala_beanstalk_render_location_hero_quote( array( 'label' => 'Get a FREE Quote' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared quote button. ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
		<div class="koala-location-about">
			<div class="koala-location-about__copy">
				<h2><?php echo esc_html( (string) get_post_meta( $id, 'who_we_are_title', true ) ); ?></h2>
				<div><?php echo wp_kses_post( wpautop( (string) get_post_meta( $id, 'who_we_are_description', true ) ) ); ?></div>
				<?php echo koala_beanstalk_render_location_hero_quote( array( 'label' => 'Get a FREE Quote' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared quote button. ?>
			</div>
			<div class="koala-location-about__video" data-location-about-media>
				<?php echo wp_get_attachment_image( 48162, 'large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Preserve the Bricks poster attachment. ?>
				<button type="button" data-location-about-play aria-label="Play Who We Are video" data-video="https://www.youtube-nocookie.com/embed/CsvHGsgMjkQ?autoplay=1&amp;controls=1">
					<?php echo wp_get_attachment_image( 8367, 'full', false, array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Existing Bricks play icon. ?>
				</button>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/** Register the reusable ACF-backed services and about pattern. */
function koala_beanstalk_register_location_services(): void {
	wp_register_script( 'koala-location-services-editor', get_template_directory_uri() . '/beanstalk/assets/location-services-editor.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-server-side-render' ), (string) filemtime( __DIR__ . '/assets/location-services-editor.js' ), true );
	register_block_type(
		'koala/location-services-section',
		array(
			'api_version'     => 3,
			'render_callback' => 'koala_beanstalk_render_location_services',
			'editor_script'   => 'koala-location-services-editor',
			'supports'        => array( 'html' => false ),
		)
	);
	register_block_pattern(
		'koala/location-services-section',
		array(
			'title'      => 'Koala Location Services and Who We Are',
			'categories' => array( 'featured' ),
			'postTypes'  => array( 'location' ),
			'content'    => '<!-- wp:koala/location-services-section /-->',
		)
	);
}
add_action( 'init', 'koala_beanstalk_register_location_services' );

/** Route migrated legacy service blocks without changing city-page service cards.
 *
 * @param array $block Parsed block data.
 * @return array
 */
function koala_beanstalk_location_services_block_data( array $block ): array {
	if ( 'koala/location-services' === ( $block['blockName'] ?? '' ) && 'location' === get_post_type() ) {
		$block['blockName'] = 'koala/location-services-section';
	}
	return $block;
}
add_filter( 'render_block_data', 'koala_beanstalk_location_services_block_data' );

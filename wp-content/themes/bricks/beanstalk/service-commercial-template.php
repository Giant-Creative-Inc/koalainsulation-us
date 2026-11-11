<?php
/**
 * Commercial insulation and service areas.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$commercial = $national_copy['sections']['finder'] ?? koala_beanstalk_service_commercial_data( (int) get_queried_object_id() );
if ( null === $commercial ) {
	return;
}

$commercial_heading_tags = array(
	'span'   => array( 'class' => true ),
	'br'     => array(),
	'strong' => array(),
	'em'     => array(),
);
?>
<section id="service-commercial" class="koala-location-lower koala-location-communities koala-service-commercial" aria-labelledby="service-commercial-heading">
	<div class="koala-location-lower__intro">
		<h2 id="service-commercial-heading"><?php echo wp_kses( $commercial['title'], $commercial_heading_tags ); ?></h2>
		<?php echo wp_kses_post( wpautop( $commercial['content'] ) ); ?>
		<?php echo koala_beanstalk_render_location_hero_quote( array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared popup trigger. ?>
	</div>
	<?php echo koala_beanstalk_location_communities_list( $commercial['cities'] ?? array(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Shared renderer escapes city titles and links. ?>
</section>

<?php
/**
 * Service financing banner using the location banner styles.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only Local design preview.
$financing_preview = koala_beanstalk_service_hero_preview() && '1' === ( $_GET['koala_preview_financing'] ?? '' );
$service_financing = koala_beanstalk_service_financing_data( (int) get_queried_object_id(), $financing_preview );
if ( null === $service_financing ) {
	return;
}

$service_financing_link = esc_url( $service_financing['link'] );
$service_financing_copy = wp_kses(
	$editorial['financing'] ?? $service_financing['content'],
	array(
		'span'   => array( 'class' => true ),
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
	)
);
?>
<section id="service-financing" class="koala-location-financing" aria-label="Financing">
	<div class="koala-location-financing__content">
		<p class="koala-location-financing__eyebrow">Financing</p>
		<h2>
		<?php
		echo $service_financing_copy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized with wp_kses above.
		?>
		<sup><a href="#footnote" aria-label="Financing terms">1</a></sup></h2>
		<?php if ( '' !== $service_financing_link ) : ?>
			<div class="koala-location-financing__apply">
				<a class="wp-block-button__link" href="<?php echo $service_financing_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped with esc_url above. ?>" target="_blank" rel="noopener noreferrer">Apply Now</a>
			</div>
		<?php endif; ?>
	</div>
</section>

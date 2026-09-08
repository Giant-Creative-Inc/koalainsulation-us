<?php
/**
 * Server-rendered Beanstalk footer.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$beanstalk_settings = isset( $beanstalk_settings ) && is_array( $beanstalk_settings ) ? $beanstalk_settings : array();
$copy               = wp_parse_args(
	$beanstalk_settings,
	array(
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
	)
);
$footer             = koala_beanstalk_get_navigation_data();
$footer_logo        = 'https://imagedelivery.net/uX8LTtWSHgeVJ6eG75QgxQ/koalainsulation.com/2024/06/Logo.png/w=115,h=80';
$resources          = array(
	array(
		'title' => 'Blog',
		'url'   => $footer['base'] . '/blog/',
	),
	array(
		'title' => 'Homeowner Incentives',
		'url'   => $footer['base'] . '/homeowner-incentives/',
	),
	array(
		'title' => 'FAQ',
		'url'   => $footer['base'] . '/faq/',
	),
	array(
		'title' => 'Testimonials',
		'url'   => $footer['base'] . '/testimonials/',
	),
);
?>
<footer class="beanstalk-footer">
	<div class="beanstalk-footer__inner">
		<div class="beanstalk-footer__content">
			<div class="beanstalk-footer__columns">
				<nav class="beanstalk-footer__column" aria-label="Footer navigation">
					<a class="beanstalk-footer__heading" href="<?php echo esc_url( $footer['base'] . '/' ); ?>"><?php echo esc_html( $copy['homeLabel'] ); ?></a>
					<a class="beanstalk-footer__heading" href="<?php echo esc_url( $footer['base'] . '/why-koala/' ); ?>"><?php echo esc_html( $copy['whyKoalaLabel'] ); ?></a>
					<a class="beanstalk-footer__heading" href="<?php echo esc_url( $footer['base'] . '/why-reinsulate/' ); ?>"><?php echo esc_html( $copy['whyReinsulateLabel'] ); ?></a>
				</nav>

				<nav class="beanstalk-footer__column beanstalk-footer__services" aria-label="Footer services">
					<a class="beanstalk-footer__heading" href="<?php echo esc_url( $footer['base'] . '/services/' ); ?>"><?php echo esc_html( $copy['servicesLabel'] ); ?></a>
					<?php foreach ( $footer['services'] as $service ) : ?>
						<a href="<?php echo esc_url( $service['url'] ); ?>"><?php echo esc_html( $service['title'] ); ?></a>
					<?php endforeach; ?>
				</nav>

				<nav class="beanstalk-footer__column" aria-label="Footer resources">
					<span class="beanstalk-footer__heading"><?php echo esc_html( $copy['resourcesLabel'] ); ?></span>
					<?php foreach ( $resources as $resource ) : ?>
						<a href="<?php echo esc_url( $resource['url'] ); ?>"><?php echo esc_html( $resource['title'] ); ?></a>
					<?php endforeach; ?>
				</nav>

				<div class="beanstalk-footer__column beanstalk-footer__contact">
					<span class="beanstalk-footer__heading"><?php echo esc_html( $copy['contactLabel'] ); ?></span>
					<?php
					if ( $footer['address'] ) :
						?>
						<address><?php echo nl2br( esc_html( $footer['address'] ) ); ?></address><?php endif; ?>
					<?php
					if ( $footer['phone'] ) :
						?>
						<a href="<?php echo esc_url( $footer['phone_url'] ); ?>"><?php echo esc_html( $footer['phone'] ); ?></a><?php endif; ?>
				</div>
			</div>

			<a class="beanstalk-footer__logo" href="<?php echo esc_url( $footer['base'] . '/' ); ?>" aria-label="<?php echo esc_attr( $footer['name'] . ' home' ); ?>">
				<img src="<?php echo esc_url( $footer_logo ); ?>" width="99" height="69" alt="Koala Insulation">
			</a>

			<div class="beanstalk-footer__facts">
				<p><?php echo esc_html( $copy['factOne'] ); ?></p>
				<p><?php echo esc_html( $copy['factTwo'] ); ?></p>
				<p><?php echo esc_html( $copy['factThree'] ); ?></p>
			</div>
		</div>

		<div class="beanstalk-footer__financing" id="footnote">
			<p><?php echo esc_html( $copy['financingText'] ); ?> <a href="https://www.wisetack.com/faqs/"><?php echo esc_html( $copy['financingLinkLabel'] ); ?></a></p>
		</div>

		<div class="beanstalk-footer__bottom">
			<div class="beanstalk-footer__legal">
				<span>© <?php echo esc_html( wp_date( 'Y' ) . ' ' . $copy['copyrightSuffix'] ); ?></span>
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php echo esc_html( $copy['privacyLabel'] ); ?></a>
				<a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php echo esc_html( $copy['termsLabel'] ); ?></a>
			</div>
		</div>
	</div>
</footer>

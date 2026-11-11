<?php
/**
 * Server-rendered Beanstalk footer.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$beanstalk_settings  = isset( $beanstalk_settings ) && is_array( $beanstalk_settings ) ? $beanstalk_settings : array();
$copy                = wp_parse_args(
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
$footer              = koala_beanstalk_get_navigation_data();
$location_preview    = function_exists( 'koala_beanstalk_sidebar_preview' ) && koala_beanstalk_sidebar_preview();
$financing_terms_url = 'https://www.wisetack.com/faqs/';
if ( koala_beanstalk_service_hero_preview() || 'koalainsulation.local' === wp_parse_url( home_url( '/' ), PHP_URL_HOST ) ) {
	$application_url = '';
	$financing_id    = (int) get_queried_object_id();
	$service_preview = koala_beanstalk_service_hero_preview();
	if ( $service_preview ) {
		$financing       = koala_beanstalk_service_financing_data( $financing_id );
		$application_url = $financing['link'] ?? '';
	} elseif ( $location_preview && koala_beanstalk_location_financing_enabled( $financing_id ) ) {
		$application_url = koala_beanstalk_location_financing_link( $financing_id );
	}

	if ( $service_preview || $location_preview ) {
		$provider = koala_beanstalk_financing_provider( $application_url );
		if ( 'enerbank' === $provider ) {
			$copy['financingText']      = 'EnerBank home improvement loans are provided by Regions Bank, Member FDIC. See the lender for loan terms and eligibility.';
			$copy['financingLinkLabel'] = 'See EnerBank financing details.';
			$financing_terms_url        = 'https://prequalify.enerbank.com/';
		} elseif ( 'synchrony' === $provider ) {
			$copy['financingText']      = 'Financing through Synchrony is subject to credit approval. See your offer for applicable terms.';
			$copy['financingLinkLabel'] = 'See Synchrony financing details.';
			$financing_terms_url        = 'https://www.synchrony.com/marketplace/home-improvement';
		} elseif ( 'wisetack' !== $provider ) {
			$copy['financingText'] = 'Financing terms and eligibility vary by lender. Ask your local Koala Insulation team for details.';
			$financing_terms_url   = '';
		}
	}
}
$footer_logo = 'https://imagedelivery.net/uX8LTtWSHgeVJ6eG75QgxQ/koalainsulation.com/2024/06/Logo.png/w=115,h=80';
$resources   = array(
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
					<?php if ( $location_preview ) : ?>
						<a class="beanstalk-footer__heading" href="<?php echo esc_url( home_url( '/locations/' ) ); ?>">Locations</a>
						<a class="beanstalk-footer__heading" href="https://koalainsulationfranchise.com/">Franchise</a>
						<a href="<?php echo esc_url( home_url( '/national-partnerships' ) ); ?>">National Partnerships</a>
					<?php endif; ?>
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

			<?php if ( $location_preview ) : ?>
				<nav class="beanstalk-footer__social" aria-label="Social media">
					<a href="https://www.facebook.com/KoalaInsulationHQ/">Facebook</a>
					<a href="https://www.instagram.com/koalainsulation/">Instagram</a>
					<a href="https://www.linkedin.com/company/koalainsulation">LinkedIn</a>
					<a href="https://www.youtube.com/channel/UCEYUbnu82sHwLIRzsY6qtPQ">YouTube</a>
				</nav>
			<?php endif; ?>

			<div class="beanstalk-footer__facts">
				<?php
				foreach ( array(
					'factOne'   => 'https://insulationinstitute.org/news-industry-issues/news-and-industry-issues/press-release/',
					'factTwo'   => 'https://www.insulate.org/homeowners-frequently-asked-questions/',
					'factThree' => 'https://www.energy.gov/sites/prod/files/2013/11/f5/insulation_guide.pdf',
				) as $fact_key => $source_url ) :
					?>
					<p>
						<?php if ( $location_preview ) : ?>
							<a href="<?php echo esc_url( $source_url ); ?>"><?php echo esc_html( $copy[ $fact_key ] ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $copy[ $fact_key ] ); ?>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="beanstalk-footer__financing" id="footnote">
			<p><?php echo esc_html( $copy['financingText'] ); ?>
			<?php
			if ( $financing_terms_url ) :
				?>
				<a href="<?php echo esc_url( $financing_terms_url ); ?>"><?php echo esc_html( $copy['financingLinkLabel'] ); ?></a><?php endif; ?></p>
		</div>

		<div class="beanstalk-footer__bottom">
			<div class="beanstalk-footer__legal">
				<span>© <?php echo esc_html( wp_date( 'Y' ) . ' ' . $copy['copyrightSuffix'] ); ?></span>
				<a href="<?php echo esc_url( $location_preview ? $footer['base'] . '/privacy-policy' : home_url( '/privacy-policy/' ) ); ?>"><?php echo esc_html( $copy['privacyLabel'] ); ?></a>
				<a href="<?php echo esc_url( $location_preview ? $footer['base'] . '/terms-and-conditions' : home_url( '/terms-and-conditions/' ) ); ?>"><?php echo esc_html( $copy['termsLabel'] ); ?></a>
			</div>
		</div>
	</div>
</footer>

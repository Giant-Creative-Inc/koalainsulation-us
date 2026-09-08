<?php
/**
 * Server-rendered Beanstalk header.
 *
 * @package Koala_Beanstalk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$beanstalk_settings = isset( $beanstalk_settings ) && is_array( $beanstalk_settings ) ? $beanstalk_settings : array();
$labels             = wp_parse_args(
	$beanstalk_settings,
	array(
		'viewAllLocationsLabel' => 'View All Locations',
		'servicesLabel'         => 'Services',
		'whyKoalaLabel'         => 'Why Koala?',
		'whyReinsulateLabel'    => 'Why Reinsulate?',
		'resourcesLabel'        => 'Resources',
	)
);
$header             = koala_beanstalk_get_navigation_data();
$logo               = 'https://imagedelivery.net/uX8LTtWSHgeVJ6eG75QgxQ/koalainsulation.com/2024/06/Logo.png/w=115,h=80';
$icons              = get_template_directory_uri() . '/beanstalk/assets/icons/';
?>
<header class="beanstalk-header" data-beanstalk-header>
	<div class="beanstalk-header__utility">
		<div class="beanstalk-header__inner">
			<a class="beanstalk-header__location" href="<?php echo esc_url( home_url( '/locations/' ) ); ?>">
				<img src="<?php echo esc_url( $icons . 'location-icon.svg' ); ?>" width="18" height="18" alt="">
				<span><?php echo esc_html( trim( $header['name'] . ( $header['state'] ? ', ' . $header['state'] : '' ) ) ); ?></span>
			</a>
			<a class="beanstalk-header__all-locations" href="<?php echo esc_url( home_url( '/locations/' ) ); ?>"><?php echo esc_html( $labels['viewAllLocationsLabel'] ); ?></a>
			<?php if ( $header['phone'] ) : ?>
				<a class="beanstalk-header__utility-phone" href="<?php echo esc_url( $header['phone_url'] ); ?>"><img src="<?php echo esc_url( $icons . 'phone-icon.svg' ); ?>" width="18" height="18" alt=""><?php echo esc_html( $header['phone'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>

	<div class="beanstalk-header__main">
		<div class="beanstalk-header__inner beanstalk-header__main-inner">
			<a class="beanstalk-header__logo" href="<?php echo esc_url( $header['base'] . '/' ); ?>" aria-label="<?php echo esc_attr( $header['name'] . ' home' ); ?>">
				<img src="<?php echo esc_url( $logo ); ?>" width="115" height="80" alt="Koala Insulation" fetchpriority="high">
			</a>

			<?php if ( $header['phone'] ) : ?>
				<a class="beanstalk-header__mobile-phone" href="<?php echo esc_url( $header['phone_url'] ); ?>"><?php echo esc_html( $header['phone'] ); ?></a>
			<?php endif; ?>

			<button class="beanstalk-header__toggle" type="button" aria-expanded="false" aria-controls="beanstalk-navigation" aria-label="Open menu">
				<span></span><span></span><span></span>
			</button>

			<nav id="beanstalk-navigation" class="beanstalk-header__nav" aria-label="Primary navigation">
				<button class="beanstalk-header__close" type="button" aria-label="Close menu">&times;</button>
				<ul class="beanstalk-header__menu">
					<li class="beanstalk-header__dropdown">
						<div class="beanstalk-header__menu-row">
							<a href="<?php echo esc_url( $header['base'] . '/services/' ); ?>"><?php echo esc_html( $labels['servicesLabel'] ); ?></a>
							<button type="button" aria-expanded="false" aria-label="<?php echo esc_attr( 'Toggle ' . $labels['servicesLabel'] . ' submenu' ); ?>"><img src="<?php echo esc_url( $icons . 'down-icon.svg' ); ?>" width="10" height="6" alt=""></button>
						</div>
						<ul class="beanstalk-header__submenu">
							<?php foreach ( $header['services'] as $menu_link ) : ?>
								<li><a href="<?php echo esc_url( $menu_link['url'] ); ?>"><?php echo esc_html( $menu_link['title'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</li>
					<li><a href="<?php echo esc_url( $header['base'] . '/why-koala/' ); ?>"><?php echo esc_html( $labels['whyKoalaLabel'] ); ?></a></li>
					<li><a href="<?php echo esc_url( $header['base'] . '/why-reinsulate/' ); ?>"><?php echo esc_html( $labels['whyReinsulateLabel'] ); ?></a></li>
					<li class="beanstalk-header__dropdown">
						<div class="beanstalk-header__menu-row">
							<span><?php echo esc_html( $labels['resourcesLabel'] ); ?></span>
							<button type="button" aria-expanded="false" aria-label="<?php echo esc_attr( 'Toggle ' . $labels['resourcesLabel'] . ' submenu' ); ?>"><img src="<?php echo esc_url( $icons . 'down-icon.svg' ); ?>" width="10" height="6" alt=""></button>
						</div>
						<ul class="beanstalk-header__submenu">
							<?php foreach ( $header['resources'] as $menu_link ) : ?>
								<li><a href="<?php echo esc_url( $menu_link['url'] ); ?>"><?php echo esc_html( $menu_link['title'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</div>
</header>

<?php
/**
 * Title: Koala City Page
 * Slug: koala/city-page
 * Categories: koala-city-pages
 * Post Types: resources-landing-pa
 * Description: Location-aware Koala City Page, implemented one approved section at a time.
 *
 * @package Koala_Beanstalk
 */

?>
<!-- wp:group {"align":"full","className":"koala-city-page","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull koala-city-page">
	<!-- wp:group {"align":"full","className":"koala-city-hero","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull koala-city-hero">
		<!-- wp:group {"className":"koala-city-hero__content","layout":{"type":"default"}} -->
		<div class="wp-block-group koala-city-hero__content">
			<!-- wp:group {"className":"koala-city-hero__copy","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-hero__copy">
				<!-- wp:heading {"level":1,"className":"koala-city-hero__heading","metadata":{"name":"koala-city-page-field-hero-heading"}} -->
				<h1 class="wp-block-heading koala-city-hero__heading">Professional Insulation Services in {{service_area_name}}, {{state_abbreviation}}</h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"koala-city-hero__introduction","metadata":{"name":"koala-city-page-field-hero-introduction"}} -->
				<p class="koala-city-hero__introduction">Lower your energy bills and enjoy year-round comfort. Get your FREE on-site evaluation from {{service_area_name}}'s trusted insulation experts.</p>
				<!-- /wp:paragraph -->

				<!-- wp:group {"className":"koala-city-hero__benefits","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-hero__benefits" role="list">
					<!-- wp:paragraph {"className":"koala-city-hero__benefit","metadata":{"name":"koala-city-page-field-hero-benefit-1"}} -->
					<p class="koala-city-hero__benefit" role="listitem">FREE in-home evaluation</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-hero__benefit","metadata":{"name":"koala-city-page-field-hero-benefit-2"}} -->
					<p class="koala-city-hero__benefit" role="listitem">Licensed &amp; insured pros</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-hero__benefit","metadata":{"name":"koala-city-page-field-hero-benefit-3"}} -->
					<p class="koala-city-hero__benefit" role="listitem">Clean, 1-day service</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-hero__benefit","metadata":{"name":"koala-city-page-field-hero-benefit-4"}} -->
					<p class="koala-city-hero__benefit" role="listitem">Same-week installation</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-hero__benefit","metadata":{"name":"koala-city-page-field-hero-benefit-5"}} -->
					<p class="koala-city-hero__benefit" role="listitem">Workmanship warranty</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-hero__benefit","metadata":{"name":"koala-city-page-field-hero-benefit-6"}} -->
					<p class="koala-city-hero__benefit" role="listitem">Utility rebate assistance</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"koala-city-hero__proof","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-group koala-city-hero__proof">
					<!-- wp:koala/location-phone /-->
					<!-- wp:koala/location-review-summary /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"anchor":"city-page-quote","className":"koala-city-hero__form","layout":{"type":"default"}} -->
			<div id="city-page-quote" class="wp-block-group koala-city-hero__form">
				<!-- wp:heading {"level":2,"className":"koala-city-hero__form-heading","metadata":{"name":"koala-city-page-field-hero-form-heading"}} -->
				<h2 class="wp-block-heading koala-city-hero__form-heading">Talk to Our {{service_area_name}} Insulation Experts Today</h2>
				<!-- /wp:heading -->

				<!-- wp:koala/location-gravity-form {"formId":13} -->
					<!-- wp:paragraph {"className":"koala-city-hero__submit-label-source","metadata":{"name":"koala-city-page-field-hero-form-submit-label"}} -->
					<p class="koala-city-hero__submit-label-source">Get a FREE Quote</p>
					<!-- /wp:paragraph -->
				<!-- /wp:koala/location-gravity-form -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"koala-city-residential-services","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull koala-city-residential-services">
		<!-- wp:group {"className":"koala-city-residential-services__content","layout":{"type":"default"}} -->
		<div class="wp-block-group koala-city-residential-services__content">
			<!-- wp:group {"className":"koala-city-residential-services__title-block","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-residential-services__title-block">
				<!-- wp:paragraph {"className":"koala-city-residential-services__introduction","metadata":{"name":"koala-city-page-field-residential-services-introduction"}} -->
				<p class="koala-city-residential-services__introduction">Residential Services</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":2,"className":"koala-city-residential-services__heading","metadata":{"name":"koala-city-page-field-residential-services-heading"}} -->
				<h2 class="wp-block-heading koala-city-residential-services__heading">Residential Insulation Services in {{service_area_name}}</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"koala-city-residential-services__grid","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-residential-services__grid">
				<!-- wp:koala/location-services /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"koala-city-commercial-services","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull koala-city-commercial-services">
		<!-- wp:group {"className":"koala-city-commercial-services__content","layout":{"type":"default"}} -->
		<div class="wp-block-group koala-city-commercial-services__content">
			<!-- wp:group {"className":"koala-city-commercial-services__copy","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-commercial-services__copy">
				<!-- wp:group {"className":"koala-city-commercial-services__title-block","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-commercial-services__title-block">
					<!-- wp:paragraph {"className":"koala-city-commercial-services__introduction","metadata":{"name":"koala-city-page-field-commercial-services-introduction"}} -->
					<p class="koala-city-commercial-services__introduction">Commercial Services</p>
					<!-- /wp:paragraph -->

					<!-- wp:heading {"level":2,"className":"koala-city-commercial-services__heading","metadata":{"name":"koala-city-page-field-commercial-services-heading"}} -->
					<h2 class="wp-block-heading koala-city-commercial-services__heading">Commercial Insulation Services in {{service_area_name}}</h2>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->

				<!-- wp:paragraph {"className":"koala-city-commercial-services__description","metadata":{"name":"koala-city-page-field-commercial-services-description"}} -->
				<p class="koala-city-commercial-services__description">Trusted by {{state_name}} builders, general contractors, and commercial property owners for large-scale insulation projects across {{location_name}}.</p>
				<!-- /wp:paragraph -->

				<!-- wp:group {"className":"koala-city-commercial-services__benefits","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-commercial-services__benefits" role="list">
					<!-- wp:group {"className":"koala-city-commercial-services__benefit","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-commercial-services__benefit" role="listitem">
						<!-- wp:paragraph {"className":"koala-city-commercial-services__benefit-title","metadata":{"name":"koala-city-page-field-commercial-services-benefit-1-title"}} -->
						<p class="koala-city-commercial-services__benefit-title">Energy Code Compliance</p>
						<!-- /wp:paragraph -->
						<!-- wp:paragraph {"className":"koala-city-commercial-services__benefit-description","metadata":{"name":"koala-city-page-field-commercial-services-benefit-1-description"}} -->
						<p class="koala-city-commercial-services__benefit-description">Energy Efficiency tailored to your project's R-value requirements</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->

					<!-- wp:group {"className":"koala-city-commercial-services__benefit","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-commercial-services__benefit" role="listitem">
						<!-- wp:paragraph {"className":"koala-city-commercial-services__benefit-title","metadata":{"name":"koala-city-page-field-commercial-services-benefit-2-title"}} -->
						<p class="koala-city-commercial-services__benefit-title">Large Project Experience</p>
						<!-- /wp:paragraph -->
						<!-- wp:paragraph {"className":"koala-city-commercial-services__benefit-description","metadata":{"name":"koala-city-page-field-commercial-services-benefit-2-description"}} -->
						<p class="koala-city-commercial-services__benefit-description">Large Project Experience across multifamily, commercial, and new builds</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->

					<!-- wp:group {"className":"koala-city-commercial-services__benefit","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-commercial-services__benefit" role="listitem">
						<!-- wp:paragraph {"className":"koala-city-commercial-services__benefit-title","metadata":{"name":"koala-city-page-field-commercial-services-benefit-3-title"}} -->
						<p class="koala-city-commercial-services__benefit-title">Inspection &amp; Code Compliance</p>
						<!-- /wp:paragraph -->
						<!-- wp:paragraph {"className":"koala-city-commercial-services__benefit-description","metadata":{"name":"koala-city-page-field-commercial-services-benefit-3-description"}} -->
						<p class="koala-city-commercial-services__benefit-description">Inspection &amp; Code Compliance from quote through final walkthrough</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"koala-city-commercial-services__visual","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-commercial-services__visual">
				<!-- wp:group {"className":"koala-city-commercial-services__gallery","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-commercial-services__gallery">
					<!-- wp:group {"className":"koala-city-commercial-services__gallery-item","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-commercial-services__gallery-item">
						<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"koala-city-commercial-services__gallery-image","metadata":{"name":"koala-city-page-field-commercial-services-image-1"}} -->
						<figure class="wp-block-image size-full koala-city-commercial-services__gallery-image"><img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/city-page/commercial-space.webp' ); ?>" alt="Commercial insulation installer applying material to an interior wall" width="1280" height="853" loading="lazy" decoding="async"/></figure>
						<!-- /wp:image -->
						<!-- wp:paragraph {"className":"koala-city-commercial-services__caption","metadata":{"name":"koala-city-page-field-commercial-services-caption-1"}} -->
						<p class="koala-city-commercial-services__caption">Commercial Spaces</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->

					<!-- wp:group {"className":"koala-city-commercial-services__gallery-item","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-commercial-services__gallery-item">
						<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"koala-city-commercial-services__gallery-image","metadata":{"name":"koala-city-page-field-commercial-services-image-2"}} -->
						<figure class="wp-block-image size-full koala-city-commercial-services__gallery-image"><img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/city-page/commercial-new-construction.webp' ); ?>" alt="New-construction building with exposed insulated exterior walls" width="750" height="502" loading="lazy" decoding="async"/></figure>
						<!-- /wp:image -->
						<!-- wp:paragraph {"className":"koala-city-commercial-services__caption","metadata":{"name":"koala-city-page-field-commercial-services-caption-2"}} -->
						<p class="koala-city-commercial-services__caption">New Construction</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->

				<!-- wp:koala/location-service-cta {"serviceKey":"commercial_insulation"} -->
					<!-- wp:paragraph {"className":"koala-city-commercial-services__cta-label-source","metadata":{"name":"koala-city-page-field-commercial-services-cta-label"}} -->
					<p class="koala-city-commercial-services__cta-label-source">Get Commercial Quote</p>
					<!-- /wp:paragraph -->
				<!-- /wp:koala/location-service-cta -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"koala-city-why-koala","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull koala-city-why-koala">
		<!-- wp:group {"className":"koala-city-why-koala__content","layout":{"type":"default"}} -->
		<div class="wp-block-group koala-city-why-koala__content">
			<!-- wp:group {"className":"koala-city-why-koala__visual","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-why-koala__visual">
				<!-- wp:koala/why-koala-video -->
					<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"koala-city-why-koala__image","metadata":{"name":"koala-city-page-field-why-koala-image"}} -->
					<figure class="wp-block-image size-full koala-city-why-koala__image"><img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/city-page/why-koala-image.webp' ); ?>" alt="Koala Insulation professional reviewing an insulation guide with a homeowner" width="1200" height="769" loading="lazy" decoding="async"/></figure>
					<!-- /wp:image -->
				<!-- /wp:koala/why-koala-video -->

				<!-- wp:koala/city-page-quote-cta -->
					<!-- wp:paragraph {"className":"koala-city-why-koala__cta-label-source","metadata":{"name":"koala-city-page-field-why-koala-cta-label"}} -->
					<p class="koala-city-why-koala__cta-label-source">Get a FREE Quote</p>
					<!-- /wp:paragraph -->
				<!-- /wp:koala/city-page-quote-cta -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"koala-city-why-koala__copy","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-why-koala__copy">
				<!-- wp:group {"className":"koala-city-why-koala__title-block","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-why-koala__title-block">
					<!-- wp:paragraph {"className":"koala-city-why-koala__introduction","metadata":{"name":"koala-city-page-field-why-koala-introduction"}} -->
					<p class="koala-city-why-koala__introduction">Why Choose</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":2,"className":"koala-city-why-koala__heading","metadata":{"name":"koala-city-page-field-why-koala-heading"}} -->
					<h2 class="wp-block-heading koala-city-why-koala__heading">Koala <strong>Insulation?</strong></h2>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"koala-city-why-koala__benefits","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-why-koala__benefits" role="list">
					<!-- wp:paragraph {"className":"koala-city-why-koala__benefit","metadata":{"name":"koala-city-page-field-why-koala-benefit-1"}} -->
					<p class="koala-city-why-koala__benefit" role="listitem"><strong>Workmanship warranty</strong> on every install</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-why-koala__benefit","metadata":{"name":"koala-city-page-field-why-koala-benefit-2"}} -->
					<p class="koala-city-why-koala__benefit" role="listitem"><strong>FREE</strong> on-site evaluations &amp; written quotes</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-why-koala__benefit","metadata":{"name":"koala-city-page-field-why-koala-benefit-3"}} -->
					<p class="koala-city-why-koala__benefit" role="listitem">NJ Clean Energy Program <strong>rebate-eligible</strong> services</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-why-koala__benefit","metadata":{"name":"koala-city-page-field-why-koala-benefit-4"}} -->
					<p class="koala-city-why-koala__benefit" role="listitem">Most jobs completed in <strong>just one day</strong></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"koala-city-why-koala__benefit","metadata":{"name":"koala-city-page-field-why-koala-benefit-5"}} -->
					<p class="koala-city-why-koala__benefit" role="listitem">Locally licensed, insured <strong>{{service_area_name}} team</strong></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"koala-city-why-koala__features","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-why-koala__features">
					<!-- wp:group {"className":"koala-city-why-koala__feature koala-city-why-koala__feature--comfort","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-why-koala__feature koala-city-why-koala__feature--comfort">
						<!-- wp:paragraph {"className":"koala-city-why-koala__feature-label","metadata":{"name":"koala-city-page-field-why-koala-feature-1"}} -->
						<p class="koala-city-why-koala__feature-label">Superior<br>Comfort</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
					<!-- wp:group {"className":"koala-city-why-koala__feature koala-city-why-koala__feature--energy","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-why-koala__feature koala-city-why-koala__feature--energy">
						<!-- wp:paragraph {"className":"koala-city-why-koala__feature-label","metadata":{"name":"koala-city-page-field-why-koala-feature-2"}} -->
						<p class="koala-city-why-koala__feature-label">Lower<br>Energy Bill</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
					<!-- wp:group {"className":"koala-city-why-koala__feature koala-city-why-koala__feature--eco","layout":{"type":"default"}} -->
					<div class="wp-block-group koala-city-why-koala__feature koala-city-why-koala__feature--eco">
						<!-- wp:paragraph {"className":"koala-city-why-koala__feature-label","metadata":{"name":"koala-city-page-field-why-koala-feature-3"}} -->
						<p class="koala-city-why-koala__feature-label">Eco<br>Friendly</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"koala-city-testimonials","layout":{"type":"default"}} -->
	<section class="wp-block-group alignfull koala-city-testimonials" aria-labelledby="koala-city-testimonials-heading">
		<!-- wp:group {"className":"koala-city-testimonials__content","layout":{"type":"default"}} -->
		<div class="wp-block-group koala-city-testimonials__content">
			<!-- wp:group {"className":"koala-city-testimonials__intro","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-testimonials__intro">
				<!-- wp:paragraph {"className":"koala-city-testimonials__eyebrow","metadata":{"name":"koala-city-page-field-testimonials-eyebrow"}} -->
				<p class="koala-city-testimonials__eyebrow">Your Satisfaction is</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2,"className":"koala-city-testimonials__heading","metadata":{"name":"koala-city-page-field-testimonials-heading"}} -->
				<h2 id="koala-city-testimonials-heading" class="wp-block-heading koala-city-testimonials__heading">Our Top Priority</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"koala-city-testimonials__description","metadata":{"name":"koala-city-page-field-testimonials-description"}} -->
				<p class="koala-city-testimonials__description">And don’t just take our word for it, hear from our happy clients about their experience with our professional insulation services.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:koala/location-reviews -->
				<!-- wp:paragraph {"className":"koala-city-testimonials__fallback-label-source","metadata":{"name":"koala-city-page-field-testimonials-fallback-link-label"}} -->
				<p class="koala-city-testimonials__fallback-label-source">Read all customer reviews</p>
				<!-- /wp:paragraph -->
			<!-- /wp:koala/location-reviews -->
		</div>
		<!-- /wp:group -->
	</section>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"koala-city-faq","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull koala-city-faq">
		<!-- wp:group {"className":"koala-city-faq__content","layout":{"type":"default"}} -->
		<div class="wp-block-group koala-city-faq__content">
			<!-- wp:group {"className":"koala-city-faq__intro","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-faq__intro">
				<!-- wp:group {"className":"koala-city-faq__title-block","layout":{"type":"default"}} -->
				<div class="wp-block-group koala-city-faq__title-block">
					<!-- wp:paragraph {"className":"koala-city-faq__heading-prefix","metadata":{"name":"koala-city-page-field-faq-heading-prefix"}} -->
					<p class="koala-city-faq__heading-prefix">Frequently Asked</p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":2,"className":"koala-city-faq__heading","metadata":{"name":"koala-city-page-field-faq-heading"}} -->
					<h2 class="wp-block-heading koala-city-faq__heading">Questions</h2>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->

				<!-- wp:paragraph {"className":"koala-city-faq__introduction","metadata":{"name":"koala-city-page-field-faq-introduction"}} -->
				<p class="koala-city-faq__introduction">Get all your questions answered by our local experts. If we didn’t address your question here, give us a call at {{location_phone}} or schedule a free quote today!</p>
				<!-- /wp:paragraph -->

				<!-- wp:koala/city-page-quote-cta -->
					<!-- wp:paragraph {"className":"koala-city-faq__cta-label-source","metadata":{"name":"koala-city-page-field-faq-cta-label"}} -->
					<p class="koala-city-faq__cta-label-source">Get a FREE Quote</p>
					<!-- /wp:paragraph -->
				<!-- /wp:koala/city-page-quote-cta -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"koala-city-faq__items","layout":{"type":"default"}} -->
			<div class="wp-block-group koala-city-faq__items">
				<!-- wp:details {"showContent":true,"className":"koala-city-faq__item","metadata":{"name":"koala-city-page-field-faq-1-question"}} -->
				<details class="wp-block-details koala-city-faq__item" open><summary>How much does insulation cost in {{service_area_name}}?</summary>
					<!-- wp:paragraph {"className":"koala-city-faq__answer","metadata":{"name":"koala-city-page-field-faq-1-answer"}} -->
					<p class="koala-city-faq__answer">Blown-in attic insulation in the {{service_area_name}} area typically starts at $1.60 per square foot, so a standard 1,500 sq ft attic usually lands in the $2,400 to $4,000 range. Spray foam costs more per square foot but seals air leaks at the same time. Final price depends on attic size, the R-value you’re starting from, and the insulation type. Every quote is written, itemised, and free.</p>
					<!-- /wp:paragraph -->
				</details>
				<!-- /wp:details -->

				<!-- wp:details {"showContent":false,"className":"koala-city-faq__item","metadata":{"name":"koala-city-page-field-faq-2-question"}} -->
				<details class="wp-block-details koala-city-faq__item"><summary>What type of insulation is best for a {{service_area_name}} attic?</summary>
					<!-- wp:paragraph {"className":"koala-city-faq__answer","metadata":{"name":"koala-city-page-field-faq-2-answer"}} -->
					<p class="koala-city-faq__answer">For most {{service_area_name}} homes, blown-in cellulose over the attic floor combined with air sealing gives the best result for the money — it fills gaps around joists and wiring that batts leave open. Spray foam is the better choice when you need to air seal the roof deck itself or convert the attic into conditioned space. We recommend a type after inspecting your attic, not before.</p>
					<!-- /wp:paragraph -->
				</details>
				<!-- /wp:details -->

				<!-- wp:details {"showContent":false,"className":"koala-city-faq__item","metadata":{"name":"koala-city-page-field-faq-3-question"}} -->
				<details class="wp-block-details koala-city-faq__item"><summary>What R-value do I need for an attic in {{service_area_name}}?</summary>
					<!-- wp:paragraph {"className":"koala-city-faq__answer","metadata":{"name":"koala-city-page-field-faq-3-answer"}} -->
					<p class="koala-city-faq__answer">{{service_area_name}} sits in climate zone 4A, where the Department of Energy recommends R-49 to R-60 for attics. Many local homes built before 2000 come in around R-19 to R-30, which is well short. R-49 is also the level required for the {{state_abbreviation}} Clean Energy Program rebate. We measure your current R-value during the free evaluation and tell you exactly where you stand.</p>
					<!-- /wp:paragraph -->
				</details>
				<!-- /wp:details -->

				<!-- wp:details {"showContent":false,"className":"koala-city-faq__item","metadata":{"name":"koala-city-page-field-faq-4-question"}} -->
				<details class="wp-block-details koala-city-faq__item"><summary>What areas around {{service_area_name}} do you serve?</summary>
					<!-- wp:paragraph {"className":"koala-city-faq__answer","metadata":{"name":"koala-city-page-field-faq-4-answer"}} -->
					<p class="koala-city-faq__answer">Koala Insulation of {{location_name}} serves {{service_area_name}} and surrounding {{state_name}} communities. Not sure if you’re in range? Call {{location_phone}} and we’ll confirm in a minute.</p>
					<!-- /wp:paragraph -->
				</details>
				<!-- /wp:details -->

				<!-- wp:details {"showContent":false,"className":"koala-city-faq__item","metadata":{"name":"koala-city-page-field-faq-5-question"}} -->
				<details class="wp-block-details koala-city-faq__item"><summary>How long does the job take, and do I need to leave the house?</summary>
					<!-- wp:paragraph {"className":"koala-city-faq__answer","metadata":{"name":"koala-city-page-field-faq-5-answer"}} -->
					<p class="koala-city-faq__answer">Most attic insulation jobs are finished in a single day, and you can stay home for blown-in work. Spray foam requires you to be out of the house for roughly 24 hours while it cures. We’ll tell you which applies before we book.</p>
					<!-- /wp:paragraph -->
				</details>
				<!-- /wp:details -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"koala-city-final-cta","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull koala-city-final-cta">
		<!-- wp:group {"className":"koala-city-final-cta__content","layout":{"type":"default"}} -->
		<div class="wp-block-group koala-city-final-cta__content">
			<!-- wp:heading {"level":2,"className":"koala-city-final-cta__heading","metadata":{"name":"koala-city-page-field-final-cta-heading"}} -->
			<h2 class="wp-block-heading koala-city-final-cta__heading">Ready to lower your energy bills?</h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"koala-city-final-cta__supporting-copy","metadata":{"name":"koala-city-page-field-final-cta-supporting-copy"}} -->
			<p class="koala-city-final-cta__supporting-copy">Book your free insulation evaluation now. Same-week availability across {{service_area_name}}.</p>
			<!-- /wp:paragraph -->

			<!-- wp:koala/city-page-quote-cta -->
				<!-- wp:paragraph {"className":"koala-city-final-cta__button-label-source","metadata":{"name":"koala-city-page-field-final-cta-button-label"}} -->
				<p class="koala-city-final-cta__button-label-source">Get a FREE Quote</p>
				<!-- /wp:paragraph -->
			<!-- /wp:koala/city-page-quote-cta -->
		</div>
		<!-- /wp:group -->

		<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"koala-city-final-cta__decoration","metadata":{"name":"koala-city-page-field-final-cta-decorative-image"}} -->
		<figure class="wp-block-image size-full koala-city-final-cta__decoration"><img src="<?php echo esc_url( get_template_directory_uri() . '/beanstalk/assets/city-page/final-cta-decoration.png' ); ?>" alt="" width="560" height="352" loading="lazy" decoding="async"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

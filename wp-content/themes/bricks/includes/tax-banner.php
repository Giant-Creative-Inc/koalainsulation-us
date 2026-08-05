<?php
/**
 * Location "tax banner" (top-nav promo bar) visibility + content.
 *
 * The banner element (#tax-banner) is hidden by default via Bricks custom CSS
 * (`#tax-banner { display:none }`). It should appear only when the active
 * location has a `location_nav_top_text` value, showing that text inside
 * #nav-top-text-us and linking it via `location_nav_top_link` when present.
 *
 * The active location is known server-side on a single location page, and only
 * client-side (sessionStorage, populated by the existing /location-data REST
 * call) on every other page. To avoid a layout jump, the show/hide decision is
 * made in <head>, before the body is parsed:
 *
 *   - On a location page we emit the meta value inline (no extra query — it is
 *     the current post), so the banner is correct on the very first paint and
 *     stale session values are cleared.
 *   - Elsewhere we read the value the location call already cached in
 *     sessionStorage, matching how the rest of the site personalises
 *     per-location content.
 *
 * No script is enqueued globally: the inline initialiser self-terminates when
 * there is no banner text and only ever touches #tax-banner / #nav-top-text-us
 * when they exist on the page.
 *
 * @package Koala
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the current location page's nav-top banner text/link.
 *
 * Only resolves on a singular `location` request, where the post meta is
 * already loaded — so this adds no query. Returns nulls elsewhere, which tells
 * the front-end initialiser to fall back to sessionStorage.
 *
 * @return array{authoritative: bool, text: ?string, link: ?string}
 */
function koala_tax_banner_server_values(): array {
	if ( ! is_singular( 'location' ) ) {
		return array(
			'authoritative' => false,
			'text'          => null,
			'link'          => null,
		);
	}

	$location_id = get_queried_object_id();
	$text        = trim( (string) get_post_meta( $location_id, 'location_nav_top_text', true ) );
	$link        = trim( (string) get_post_meta( $location_id, 'location_nav_top_link', true ) );

	return array(
		'authoritative' => true,
		'text'          => '' === $text ? null : $text,
		'link'          => '' === $link ? null : $link,
	);
}

/**
 * Prints the tax-banner critical CSS + inline initialiser into <head>.
 *
 * Hooked early on wp_head so it parses before the header markup, letting the
 * show/hide decision happen pre-paint (no jump). Skipped in wp-admin and the
 * Bricks builder/preview canvas.
 */
function koala_tax_banner_head(): void {
	if ( is_admin() ) {
		return;
	}

	// Don't run inside the Bricks builder or its preview iframe.
	if ( defined( 'BRICKS_BUILDER_PARAM' ) && isset( $_GET[ BRICKS_BUILDER_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( defined( 'BRICKS_BUILDER_IFRAME_PARAM' ) && isset( $_GET[ BRICKS_BUILDER_IFRAME_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$server        = koala_tax_banner_server_values();
	$text_literal  = null === $server['text'] ? 'null' : wp_json_encode( $server['text'] );
	$link_literal  = null === $server['link'] ? 'null' : wp_json_encode( $server['link'] );
	$authoritative = $server['authoritative'] ? 'true' : 'false';
	?>
	<style id="koala-tax-banner-css">html.koala-tax-banner-on #tax-banner{display:block!important}</style>
	<script id="koala-tax-banner-init">
	(function () {
		var text = <?php echo $text_literal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
		var link = <?php echo $link_literal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
		var authoritative = <?php echo $authoritative; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;

		try {
			if (authoritative) {
				// On a location page the post meta is the source of truth.
				if (text) {
					sessionStorage.setItem('nav_top_text', text);
					sessionStorage.setItem('nav_top_link', link || '');
				} else {
					sessionStorage.removeItem('nav_top_text');
					sessionStorage.removeItem('nav_top_link');
				}
			} else {
				// Elsewhere, reuse the value cached by the location call.
				text = sessionStorage.getItem('nav_top_text');
				link = sessionStorage.getItem('nav_top_link');
			}
		} catch (e) {}

		if (!text) {
			return;
		}

		// Reveal the banner before the body paints (no layout jump).
		document.documentElement.classList.add('koala-tax-banner-on');

		// Fill the text/link as the node is parsed (no content pop).
		function fill() {
			var el = document.getElementById('nav-top-text-us');
			if (!el) {
				return false;
			}
			el.textContent = text;
			if (link) {
				var anchor = el.closest('a');
				if (anchor) {
					anchor.href = link;
				}
			}
			return true;
		}

		if (fill()) {
			return;
		}

		if (typeof MutationObserver === 'function') {
			var observer = new MutationObserver(function () {
				if (fill()) {
					observer.disconnect();
				}
			});
			observer.observe(document.documentElement, { childList: true, subtree: true });
			document.addEventListener('DOMContentLoaded', function () {
				fill();
				observer.disconnect();
			});
		} else {
			document.addEventListener('DOMContentLoaded', fill);
		}
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'koala_tax_banner_head', 2 );

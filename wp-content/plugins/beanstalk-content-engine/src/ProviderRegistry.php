<?php
/**
 * Content provider registry.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/**
 * Stores validated content providers in deterministic priority order.
 */
final class ProviderRegistry {

	/**
	 * Registered providers.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $providers = array();

	/**
	 * Registers a provider.
	 *
	 * @param array $provider Provider configuration.
	 * @return true|WP_Error
	 */
	public function register( array $provider ) {
		$required = array( 'id', 'label', 'source_type', 'pattern_directory', 'manifest_directory', 'priority' );

		if ( array_diff( $required, array_keys( $provider ) ) ) {
			return new WP_Error( 'beanstalk_content_engine_invalid_provider', __( 'A content provider is missing required configuration.', 'beanstalk-content-engine' ) );
		}

		$id = sanitize_key( $provider['id'] );

		if ( '' === $id || $id !== $provider['id'] || isset( $this->providers[ $id ] ) ) {
			return new WP_Error( 'beanstalk_content_engine_invalid_provider', __( 'A content provider has an invalid or duplicate ID.', 'beanstalk-content-engine' ) );
		}

		if ( ! in_array( $provider['source_type'], array( 'child-theme', 'parent-theme', 'plugin' ), true ) ) {
			return new WP_Error( 'beanstalk_content_engine_invalid_provider', __( 'A content provider has an unsupported source type.', 'beanstalk-content-engine' ) );
		}

		$pattern_directory  = untrailingslashit( wp_normalize_path( $provider['pattern_directory'] ) );
		$manifest_directory = untrailingslashit( wp_normalize_path( $provider['manifest_directory'] ) );

		if ( ! is_dir( $pattern_directory ) || ! is_dir( $manifest_directory ) ) {
			return new WP_Error( 'beanstalk_content_engine_invalid_provider', __( 'A content provider directory is unavailable.', 'beanstalk-content-engine' ) );
		}

		$compatibility_callback = $provider['compatibility_callback'] ?? null;

		if ( null !== $compatibility_callback && ! is_callable( $compatibility_callback ) ) {
			return new WP_Error( 'beanstalk_content_engine_invalid_provider', __( 'A content provider compatibility callback is not callable.', 'beanstalk-content-engine' ) );
		}

		$this->providers[ $id ] = array(
			'id'                     => $id,
			'label'                  => sanitize_text_field( $provider['label'] ),
			'source_type'            => $provider['source_type'],
			'pattern_directory'      => $pattern_directory,
			'manifest_directory'     => $manifest_directory,
			'priority'               => (int) $provider['priority'],
			'compatibility_callback' => $compatibility_callback,
		);

		return true;
	}

	/**
	 * Returns compatible providers, highest priority first.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function all(): array {
		$providers = array_filter(
			$this->providers,
			static function ( array $provider ): bool {
				return null === $provider['compatibility_callback'] || (bool) call_user_func( $provider['compatibility_callback'], $provider );
			}
		);

		usort(
			$providers,
			static function ( array $first, array $second ): int {
				$priority = $second['priority'] <=> $first['priority'];

				return 0 !== $priority ? $priority : strcmp( $first['id'], $second['id'] );
			}
		);

		return array_values( $providers );
	}
}

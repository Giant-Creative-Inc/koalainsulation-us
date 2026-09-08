<?php
/**
 * Provider-aware Pattern registry.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Block_Patterns_Registry;
use WP_Error;

/**
 * Resolves manifests using child, parent, then plugin priority.
 */
final class PatternRegistry {

	/**
	 * Creates the Pattern registry.
	 *
	 * @param ProviderRegistry $providers Provider registry.
	 * @param PatternValidator $validator Manifest validator.
	 */
	public function __construct(
		private ProviderRegistry $providers,
		private PatternValidator $validator
	) {}

	/** Registers the fallback Pattern category when a theme has not. */
	public function register_pattern_category(): void {
		if ( ! \WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( 'beanstalk' ) ) {
			register_block_pattern_category(
				'beanstalk',
				array( 'label' => __( 'Beanstalk', 'beanstalk-content-engine' ) )
			);
		}
	}

	/**
	 * Returns all resolved manifests.
	 *
	 * A higher-priority file with the same basename is authoritative. If it is
	 * invalid, its error is returned instead of silently using a fallback.
	 *
	 * @return array<string, array>|WP_Error
	 */
	public function manifests() {
		$candidates = array();

		foreach ( $this->providers->all() as $provider ) {
			$paths = glob( $provider['manifest_directory'] . '/*.json' );
			if ( false === $paths ) {
				continue;
			}

			sort( $paths );
			foreach ( $paths as $path ) {
				$key = basename( $path, '.json' );
				if ( ! isset( $candidates[ $key ] ) ) {
					$candidates[ $key ] = array(
						'path'     => $path,
						'provider' => $provider,
					);
				}
			}
		}

		ksort( $candidates );
		$manifests = array();
		foreach ( $candidates as $candidate ) {
			$manifest = $this->load( $candidate['path'], $candidate['provider'] );
			if ( is_wp_error( $manifest ) ) {
				return $manifest;
			}
			if ( isset( $manifests[ $manifest['id'] ] ) ) {
				return new WP_Error( 'beanstalk_duplicate_manifest_id', __( 'Multiple resolved manifests use the same pattern ID.', 'beanstalk-content-engine' ) );
			}
			$manifests[ $manifest['id'] ] = $manifest;
		}

		return $manifests;
	}

	/**
	 * Returns one resolved manifest.
	 *
	 * @param string $pattern_id Pattern ID.
	 * @return array|WP_Error
	 */
	public function manifest( string $pattern_id ) {
		$manifests = $this->manifests();
		if ( is_wp_error( $manifests ) ) {
			return $manifests;
		}

		$pattern_id = sanitize_text_field( $pattern_id );
		return $manifests[ $pattern_id ] ?? new WP_Error( 'beanstalk_unknown_pattern', __( 'The requested Beanstalk pattern is not supported.', 'beanstalk-content-engine' ) );
	}

	/**
	 * Registers only plugin-owned fallback Patterns not already registered.
	 *
	 * @return void
	 */
	public function register_fallback_patterns(): void {
		$manifests = $this->manifests();
		if ( is_wp_error( $manifests ) ) {
			return;
		}

		$registry = WP_Block_Patterns_Registry::get_instance();
		foreach ( $manifests as $manifest ) {
			if ( 'plugin' !== $manifest['_provider']['source_type'] || $registry->is_registered( $manifest['id'] ) ) {
				continue;
			}

			$markup = $this->read_pattern( $manifest['_pattern_path'] );
			if ( is_wp_error( $markup ) ) {
				continue;
			}

			register_block_pattern(
				$manifest['id'],
				array(
					'title'       => $manifest['label'],
					'description' => $manifest['description'],
					'categories'  => array( 'beanstalk' ),
					'content'     => $markup,
				)
			);
		}
	}

	/**
	 * Loads and validates a manifest and its paired Pattern.
	 *
	 * @param string $path     Manifest path.
	 * @param array  $provider Provider data.
	 * @return array|WP_Error
	 */
	private function load( string $path, array $provider ) {
		if ( ! is_readable( $path ) ) {
			return new WP_Error( 'beanstalk_manifest_unavailable', __( 'A pattern manifest is unavailable.', 'beanstalk-content-engine' ) );
		}

		$manifest = json_decode( (string) file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads a local provider manifest.
		if ( ! is_array( $manifest ) || JSON_ERROR_NONE !== json_last_error() ) {
			return new WP_Error( 'beanstalk_invalid_manifest_json', __( 'A higher-priority pattern manifest contains invalid JSON.', 'beanstalk-content-engine' ) );
		}

		$validation = $this->validator->validate( $manifest );
		if ( is_wp_error( $validation ) ) {
			$validation->add_data(
				array(
					'provider_id'   => $provider['id'],
					'manifest_path' => $path,
				)
			);
			return $validation;
		}

		$pattern_path = $provider['pattern_directory'] . '/' . $manifest['template'];
		if ( ! is_readable( $pattern_path ) ) {
			return new WP_Error( 'beanstalk_pattern_unavailable', __( 'A resolved pattern template is unavailable.', 'beanstalk-content-engine' ), array( 'provider_id' => $provider['id'] ) );
		}

		$manifest['_provider']      = $provider;
		$manifest['_manifest_path'] = $path;
		$manifest['_pattern_path']  = $pattern_path;
		return $manifest;
	}

	/**
	 * Captures a PHP Pattern file's block markup.
	 *
	 * @param string $path Pattern path.
	 * @return string|WP_Error
	 */
	public function read_pattern( string $path ) {
		if ( ! is_readable( $path ) ) {
			return new WP_Error( 'beanstalk_pattern_unavailable', __( 'The requested pattern template is unavailable.', 'beanstalk-content-engine' ) );
		}

		ob_start();
		include $path;
		return (string) ob_get_clean();
	}
}

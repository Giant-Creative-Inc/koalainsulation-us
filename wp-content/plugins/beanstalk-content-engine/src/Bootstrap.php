<?php
/**
 * Plugin bootstrap.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

/**
 * Wires services and WordPress hooks without mutating roles or content.
 */
final class Bootstrap {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Provider registry.
	 *
	 * @var ProviderRegistry
	 */
	private ProviderRegistry $providers;

	/**
	 * Pattern registry.
	 *
	 * @var PatternRegistry
	 */
	private PatternRegistry $patterns;

	/**
	 * Ability registrar.
	 *
	 * @var AbilityRegistrar
	 */
	private AbilityRegistrar $abilities;

	/**
	 * Existing City Page bulk updater.
	 *
	 * @var CityPageUpdater
	 */
	private CityPageUpdater $city_page_updater;

	/**
	 * Whether hooks were registered.
	 *
	 * @var bool
	 */
	private bool $booted = false;

	/** Creates the service graph. */
	private function __construct() {
		$this->providers         = new ProviderRegistry();
		$validator               = new PatternValidator();
		$this->patterns          = new PatternRegistry( $this->providers, $validator );
		$content                 = new ContentValidator();
		$builder                 = new ContentBuilder( $this->patterns );
		$city_context            = new CityPageContext();
		$drafts                  = new DraftManager( $this->patterns, $content, $builder, $city_context );
		$this->city_page_updater = new CityPageUpdater( $this->patterns, $content, $builder, $city_context );
		$this->abilities         = new AbilityRegistrar( $this->patterns, $drafts );
	}

	/**
	 * Returns the shared bootstrap.
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Returns the provider registry.
	 *
	 * @return ProviderRegistry
	 */
	public function providers(): ProviderRegistry {
		return $this->providers;
	}

	/** Registers lifecycle hooks once. */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		add_action( 'after_setup_theme', array( $this, 'register_providers' ), 20 );
		add_action( 'init', array( $this->patterns, 'register_pattern_category' ), 19 );
		add_action( 'init', array( $this->patterns, 'register_fallback_patterns' ), 20 );
		add_action( 'admin_notices', array( $this, 'dependency_notice' ) );
		add_action( 'cli_init', array( $this, 'register_cli_commands' ) );
		( new ServiceAreaSchema() )->register();
	}

	/** Register the migration command only when WordPress is running under WP-CLI. */
	public function register_cli_commands(): void {
		if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'beanstalk city-pages update', new CityPageCliCommand( $this->city_page_updater ) );
		}
	}

	/** Registers the fallback and invites active themes to register providers. */
	public function register_providers(): void {
		$this->providers->register(
			array(
				'id'                 => 'plugin-fallback',
				'label'              => __( 'Plugin portable fallbacks', 'beanstalk-content-engine' ),
				'source_type'        => 'plugin',
				'pattern_directory'  => BEANSTALK_CONTENT_ENGINE_PATH . 'patterns',
				'manifest_directory' => BEANSTALK_CONTENT_ENGINE_PATH . 'pattern-manifests',
				'priority'           => 100,
			)
		);

		do_action( 'beanstalk_content_engine_register_providers' );

		if ( class_exists( 'WP_Ability' ) && function_exists( 'wp_register_ability' ) ) {
			add_action( 'wp_abilities_api_categories_init', array( $this->abilities, 'register_category' ) );
			add_action( 'wp_abilities_api_init', array( $this->abilities, 'register' ) );
		}
	}

	/** Shows only actionable dependency information to administrators. */
	public function dependency_notice(): void {
		if ( ! current_user_can( 'update_core' ) ) {
			return;
		}

		if ( ! class_exists( 'WP_Ability' ) || ! function_exists( 'wp_register_ability' ) ) {
			wp_admin_notice(
				esc_html__( 'Beanstalk Content Engine requires WordPress 6.9 or newer. Public content remains available, but content abilities are disabled.', 'beanstalk-content-engine' ),
				array( 'type' => 'error' )
			);
			return;
		}

		if ( ! class_exists( '\\WP\\MCP\\Core\\McpAdapter' ) && ! defined( 'WP_MCP_VERSION' ) ) {
			wp_admin_notice(
				esc_html__( 'The official WordPress MCP Adapter is not active. Beanstalk content abilities remain available inside WordPress, but MCP clients cannot access them.', 'beanstalk-content-engine' ),
				array(
					'type'        => 'warning',
					'dismissible' => false,
				)
			);
		}
	}
}

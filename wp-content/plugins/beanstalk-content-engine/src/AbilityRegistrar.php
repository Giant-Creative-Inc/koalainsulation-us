<?php
/**
 * WordPress Abilities API registration.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/**
 * Preserves the public Beanstalk MCP ability contract.
 */
final class AbilityRegistrar {

	/**
	 * Creates the registrar.
	 *
	 * @param PatternRegistry $patterns Pattern registry.
	 * @param DraftManager    $drafts   Draft service.
	 */
	public function __construct(
		private PatternRegistry $patterns,
		private DraftManager $drafts
	) {}

	/** Registers the stable category. */
	public function register_category(): void {
		wp_register_ability_category(
			'beanstalk',
			array(
				'label'       => __( 'Beanstalk', 'beanstalk-content-engine' ),
				'description' => __( 'Abilities for creating structured Beanstalk content.', 'beanstalk-content-engine' ),
			)
		);
	}

	/** Registers the four stable abilities. */
	public function register(): void {
		wp_register_ability(
			'beanstalk/list-destinations',
			array(
				'label'               => __( 'List Beanstalk Destinations', 'beanstalk-content-engine' ),
				'description'         => __( 'Lists WordPress post types approved for structured draft creation.', 'beanstalk-content-engine' ),
				'category'            => 'beanstalk',
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'request' => array(
							'type' => 'string',
							'enum' => array( 'catalog' ),
						),
					),
					'required'             => array( 'request' ),
					'additionalProperties' => false,
				),
				'output_schema'       => $this->destination_list_output_schema(),
				'execute_callback'    => array( $this, 'list_destinations' ),
				'permission_callback' => array( $this, 'content_permission' ),
				'meta'                => $this->ability_meta( true, true ),
			)
		);

		wp_register_ability(
			'beanstalk/list-patterns',
			array(
				'label'               => __( 'List Beanstalk Patterns', 'beanstalk-content-engine' ),
				'description'         => __( 'Lists patterns approved for structured draft creation.', 'beanstalk-content-engine' ),
				'category'            => 'beanstalk',
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'request' => array(
							'type' => 'string',
							'enum' => array( 'catalog' ),
						),
					),
					'required'             => array( 'request' ),
					'additionalProperties' => false,
				),
				'output_schema'       => $this->pattern_list_output_schema(),
				'execute_callback'    => array( $this, 'list_patterns' ),
				'permission_callback' => array( $this, 'content_permission' ),
				'meta'                => $this->ability_meta( true, true ),
			)
		);

		wp_register_ability(
			'beanstalk/get-pattern-schema',
			array(
				'label'               => __( 'Get Beanstalk Pattern Schema', 'beanstalk-content-engine' ),
				'description'         => __( 'Returns the structured content schema for an approved pattern.', 'beanstalk-content-engine' ),
				'category'            => 'beanstalk',
				'input_schema'        => $this->pattern_id_input_schema(),
				'output_schema'       => $this->pattern_schema_output_schema(),
				'execute_callback'    => array( $this, 'get_pattern_schema' ),
				'permission_callback' => array( $this, 'content_permission' ),
				'meta'                => $this->ability_meta( true, true ),
			)
		);

		wp_register_ability(
			'beanstalk/create-draft',
			array(
				'label'               => __( 'Create Beanstalk Draft', 'beanstalk-content-engine' ),
				'description'         => __( 'Creates a WordPress draft from an approved pattern and structured content.', 'beanstalk-content-engine' ),
				'category'            => 'beanstalk',
				'input_schema'        => $this->create_draft_input_schema(),
				'output_schema'       => $this->create_draft_output_schema(),
				'execute_callback'    => array( $this, 'create_draft' ),
				'permission_callback' => array( $this, 'draft_permission' ),
				'meta'                => $this->ability_meta( false, false ),
			)
		);

		wp_register_ability(
			'beanstalk/find-draft',
			array(
				'label'               => __( 'Find Beanstalk Draft', 'beanstalk-content-engine' ),
				'description'         => __( 'Finds an unpublished Beanstalk draft by external ID without changing WordPress.', 'beanstalk-content-engine' ),
				'category'            => 'beanstalk',
				'input_schema'        => $this->find_draft_input_schema(),
				'output_schema'       => $this->find_draft_output_schema(),
				'execute_callback'    => array( $this->drafts, 'find' ),
				'permission_callback' => array( $this, 'content_permission' ),
				'meta'                => $this->ability_meta( true, true ),
			)
		);
	}

	/**
	 * Creates a draft while preserving a stable error code across MCP Adapter.
	 *
	 * MCP Adapter currently serializes only the WP_Error message returned by an
	 * ability. Prefixing that message with the bounded WordPress error code lets
	 * trusted callers classify the failure without exposing raw diagnostics.
	 *
	 * @param array $input Validated draft input.
	 * @return array|\WP_Error
	 */
	public function create_draft( array $input ) {
		$result = $this->drafts->create( $input );
		if ( ! is_wp_error( $result ) ) {
			return $result;
		}

		$code = sanitize_key( $result->get_error_code() );
		return new \WP_Error(
			$code,
			$code . ': ' . $result->get_error_message(),
			$result->get_error_data()
		);
	}

	/**
	 * Lists safe WordPress destinations supported by resolved manifests.
	 *
	 * A post type must be explicitly allowlisted, registered for public editing,
	 * creatable by the current integration identity, and supported by at least
	 * one valid Beanstalk manifest.
	 *
	 * @return array|WP_Error
	 */
	public function list_destinations() {
		$manifests = $this->patterns->manifests();
		if ( is_wp_error( $manifests ) ) {
			return $manifests;
		}

		/**
		 * Filters post types that may be exposed as Beanstalk draft destinations.
		 *
		 * Adding a post type here does not make it available by itself. A resolved
		 * manifest must also support it and the integration user must be permitted
		 * to create it.
		 *
		 * @param string[] $post_types Allowed post-type names.
		 */
		$allowed = apply_filters( 'beanstalk_content_engine_allowed_post_types', array( 'page', 'post', 'resources-landing-pa' ) );
		$allowed = is_array( $allowed ) ? array_filter( $allowed, 'is_string' ) : array();
		$allowed = array_slice( array_values( array_unique( array_filter( array_map( 'sanitize_key', $allowed ) ) ) ), 0, 50 );

		$patterns_by_type = array();
		foreach ( $manifests as $manifest ) {
			foreach ( $manifest['postTypes'] as $post_type ) {
				if ( in_array( $post_type, $allowed, true ) ) {
					$patterns_by_type[ $post_type ][] = $manifest['id'];
				}
			}
		}

		$destinations = array();
		foreach ( $allowed as $post_type ) {
			$object = get_post_type_object( $post_type );
			if ( ! $object || ! $object->public || ! $object->show_ui || ! post_type_supports( $post_type, 'editor' ) || empty( $patterns_by_type[ $post_type ] ) || ! current_user_can( $object->cap->create_posts ) ) {
				continue;
			}

			$destinations[] = array(
				'post_type'      => $post_type,
				'label'          => (string) $object->labels->name,
				'singular_label' => (string) $object->labels->singular_name,
				'hierarchical'   => (bool) $object->hierarchical,
				'show_in_rest'   => (bool) $object->show_in_rest,
				'patterns'       => array_values( array_unique( $patterns_by_type[ $post_type ] ) ),
			);
		}

		return array( 'destinations' => $destinations );
	}

	/**
	 * Checks the dedicated content capability.
	 *
	 * @return bool
	 */
	public function content_permission(): bool {
		return current_user_can( 'beanstalk_create_ai_content' );
	}

	/**
	 * Checks content and normal draft-creation capabilities.
	 *
	 * @return bool
	 */
	public function draft_permission(): bool {
		return $this->content_permission();
	}

	/**
	 * Lists resolved content definitions.
	 *
	 * @return array|WP_Error
	 */
	public function list_patterns() {
		$manifests = $this->patterns->manifests();
		if ( is_wp_error( $manifests ) ) {
			return $manifests;
		}

		$patterns = array();
		foreach ( $manifests as $manifest ) {
			$patterns[] = array(
				'id'               => $manifest['id'],
				'label'            => $manifest['label'],
				'description'      => $manifest['description'],
				'post_type'        => $manifest['postTypes'][0],
				'post_types'       => $manifest['postTypes'],
				'manifest_version' => $manifest['version'],
			);
		}
		return array( 'patterns' => $patterns );
	}

	/**
	 * Returns one resolved definition's field schema.
	 *
	 * @param array $input Validated input.
	 * @return array|WP_Error
	 */
	public function get_pattern_schema( array $input ) {
		$manifest = $this->patterns->manifest( $input['pattern_id'] );
		if ( is_wp_error( $manifest ) ) {
			return $manifest;
		}

		$required = array();
		$optional = array();
		foreach ( $manifest['fields'] as $field_id => $field ) {
			$schema                          = array(
				'id'    => $field_id,
				'label' => $field['label'],
				'type'  => $field['type'],
			);
			$field['required'] ? $required[] = $schema : $optional[] = $schema;
		}

		$output = array(
			'pattern_id'       => $manifest['id'],
			'manifest_version' => $manifest['version'],
			'required_fields'  => $required,
			'optional_fields'  => $optional,
		);
		if ( isset( $manifest['editorLayout'] ) ) {
			$output['editor_layout'] = $manifest['editorLayout'];
		}
		if ( isset( $manifest['structuredData'] ) ) {
			$contract = $manifest['structuredData'];
			$output['structured_data'] = array(
				'contract_version'  => $contract['contractVersion'],
				'profile'           => $contract['profile'],
				'label'             => $contract['label'],
				'produces'          => array_values( $contract['produces'] ),
				'field_definitions' => array_map( static fn( $id, $field ) => array( 'id' => $id ) + $field, array_keys( $contract['fields'] ), array_values( $contract['fields'] ) ),
			);
		}

		if ( 'koala/city-page' === $manifest['id'] && in_array( 'resources-landing-pa', $manifest['postTypes'], true ) ) {
			$locations = get_posts(
				array(
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
					'post_type'      => 'location',
					'posts_per_page' => 200,
				)
			);
			$output['draft_context'] = array(
				'required' => true,
				'fields'   => array(
					array(
						'id'      => 'related_location_id',
						'label'   => 'Related location',
						'type'    => 'integer',
						'options' => array_map(
							array( $this, 'city_page_location_option' ),
							$locations
						),
					),
				),
			);
		}

		return $output;
	}

	/** Return a location choice with WordPress-owned schema defaults. */
	private function city_page_location_option( int $post_id ): array {
		$state      = trim( (string) get_post_meta( $post_id, 'location_state', true ) );
		$normalized = function_exists( 'koala_beanstalk_normalize_state' )
			? koala_beanstalk_normalize_state( $state )
			: $this->normalize_location_state( $state );

		return array(
			'id'       => $post_id,
			'label'    => get_the_title( $post_id ),
			'defaults' => array_filter(
				array(
					'service_area_name'  => get_the_title( $post_id ),
					'state_name'         => (string) ( $normalized['name'] ?? '' ),
					'state_abbreviation' => (string) ( $normalized['abbreviation'] ?? '' ),
					'service_type'       => 'Insulation Services',
				),
				static fn( $value ) => '' !== $value
			),
		);
	}

	/** Normalize common US state values when the active theme helper is absent. */
	private function normalize_location_state( string $state ): array {
		$states = array(
			'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California',
			'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia',
			'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa',
			'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
			'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri',
			'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey',
			'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio',
			'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
			'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont',
			'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming',
		);
		$upper  = strtoupper( $state );
		if ( isset( $states[ $upper ] ) ) {
			return array( 'name' => $states[ $upper ], 'abbreviation' => $upper );
		}
		$code = array_search( strtolower( $state ), array_map( 'strtolower', $states ), true );
		return false === $code
			? array( 'name' => $state, 'abbreviation' => '' )
			: array( 'name' => $states[ $code ], 'abbreviation' => $code );
	}

	/**
	 * Returns MCP exposure and behavior annotations.
	 *
	 * @param bool $is_readonly Whether the ability is read-only.
	 * @param bool $idempotent  Whether the ability is idempotent.
	 * @return array
	 */
	private function ability_meta( bool $is_readonly, bool $idempotent ): array {
		return array(
			'annotations'  => array(
				'readonly'    => $is_readonly,
				'destructive' => false,
				'idempotent'  => $idempotent,
			),
			'mcp'          => array(
				'public' => true,
				'type'   => 'tool',
			),
			'show_in_rest' => false,
		);
	}

	/**
	 * Returns the pattern-ID input schema.
	 *
	 * @return array
	 */
	private function pattern_id_input_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'pattern_id' ),
			'properties'           => array(
				'pattern_id' => array(
					'type'      => 'string',
					'pattern'   => '^[a-z0-9-]+/[a-z0-9-]+$',
					'maxLength' => 100,
				),
			),
			'additionalProperties' => false,
		);
	}

	/** Returns the strict destination-list output schema. */
	private function destination_list_output_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'destinations' ),
			'properties'           => array(
				'destinations' => array(
					'type'     => 'array',
					'maxItems' => 50,
					'items'    => array(
						'type'                 => 'object',
						'required'             => array( 'post_type', 'label', 'singular_label', 'hierarchical', 'show_in_rest', 'patterns' ),
						'properties'           => array(
							'post_type'      => array( 'type' => 'string' ),
							'label'          => array( 'type' => 'string' ),
							'singular_label' => array( 'type' => 'string' ),
							'hierarchical'   => array( 'type' => 'boolean' ),
							'show_in_rest'   => array( 'type' => 'boolean' ),
							'patterns'       => array(
								'type'  => 'array',
								'items' => array( 'type' => 'string' ),
							),
						),
						'additionalProperties' => false,
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the content-field output schema.
	 *
	 * @return array
	 */
	private function content_field_output_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'id', 'label', 'type' ),
			'properties'           => array(
				'id'    => array( 'type' => 'string' ),
				'label' => array( 'type' => 'string' ),
				'type'  => array(
					'type' => 'string',
					'enum' => array( 'text', 'rich-text', 'image' ),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the pattern-list output schema.
	 *
	 * @return array
	 */
	private function pattern_list_output_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'patterns' ),
			'properties'           => array(
				'patterns' => array(
					'type'  => 'array',
					'items' => array(
						'type'                 => 'object',
						'required'             => array( 'id', 'label', 'description', 'post_type', 'post_types', 'manifest_version' ),
						'properties'           => array(
							'id'               => array( 'type' => 'string' ),
							'label'            => array( 'type' => 'string' ),
							'description'      => array( 'type' => 'string' ),
							'post_type'        => array( 'type' => 'string' ),
							'post_types'       => array(
								'type'     => 'array',
								'minItems' => 1,
								'maxItems' => 50,
								'items'    => array( 'type' => 'string' ),
							),
							'manifest_version' => array( 'type' => 'string' ),
						),
						'additionalProperties' => false,
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the pattern-schema output schema.
	 *
	 * @return array
	 */
	private function pattern_schema_output_schema(): array {
		$field = $this->content_field_output_schema();
		return array(
			'type'                 => 'object',
			'required'             => array( 'pattern_id', 'manifest_version', 'required_fields', 'optional_fields' ),
			'properties'           => array(
				'pattern_id'       => array( 'type' => 'string' ),
				'manifest_version' => array( 'type' => 'string' ),
				'required_fields'  => array(
					'type'  => 'array',
					'items' => $field,
				),
				'optional_fields'  => array(
					'type'  => 'array',
					'items' => $field,
				),
				'draft_context'    => array(
					'type'                 => 'object',
					'required'             => array( 'required', 'fields' ),
					'properties'           => array(
						'required' => array( 'type' => 'boolean' ),
						'fields'   => array(
							'type'     => 'array',
							'maxItems' => 1,
							'items'    => array(
								'type'                 => 'object',
								'required'             => array( 'id', 'label', 'type', 'options' ),
								'properties'           => array(
									'id'    => array( 'type' => 'string' ),
									'label' => array( 'type' => 'string' ),
									'type'  => array(
										'type' => 'string',
										'enum' => array( 'integer' ),
									),
									'options' => array(
										'type'     => 'array',
										'maxItems' => 200,
										'items'    => array(
											'type'                 => 'object',
											'required'             => array( 'id', 'label' ),
											'properties'           => array(
												'id'       => array( 'type' => 'integer', 'minimum' => 1 ),
												'label'    => array( 'type' => 'string' ),
												'defaults' => array(
													'type'                 => 'object',
													'additionalProperties' => array( 'type' => 'string' ),
												),
											),
											'additionalProperties' => false,
										),
									),
								),
								'additionalProperties' => false,
							),
						),
					),
					'additionalProperties' => false,
				),
				'editor_layout'    => array( 'type' => 'object' ),
				'structured_data'  => $this->structured_data_output_schema(),
			),
			'additionalProperties' => false,
		);
	}

	/** Returns the normalized structured-data contract output schema. */
	private function structured_data_output_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'contract_version', 'profile', 'label', 'produces', 'field_definitions' ),
			'properties'           => array(
				'contract_version'  => array( 'type' => 'integer', 'enum' => array( 1 ) ),
				'profile'           => array( 'type' => 'string' ),
				'label'             => array( 'type' => 'string' ),
				'produces'          => array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ),
				'field_definitions' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'object', 'required' => array( 'id', 'label', 'type', 'required', 'source' ),
						'properties' => array( 'id' => array( 'type' => 'string' ), 'label' => array( 'type' => 'string' ), 'type' => array( 'type' => 'string' ), 'required' => array( 'type' => 'boolean' ), 'source' => array( 'type' => 'string' ) ),
						'additionalProperties' => false,
					),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the create-draft input schema.
	 *
	 * @return array
	 */
	private function create_draft_input_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'external_id', 'pattern_id', 'post_type', 'title', 'content' ),
			'properties'           => array(
				'external_id' => array(
					'type'      => 'string',
					'minLength' => 1,
					'maxLength' => 191,
					'pattern'   => '^[A-Za-z0-9][A-Za-z0-9._:-]*$',
				),
				'pattern_id'  => array(
					'type'      => 'string',
					'pattern'   => '^[a-z0-9-]+/[a-z0-9-]+$',
					'maxLength' => 100,
				),
				'manifest_version' => array( 'type' => 'string', 'maxLength' => 100 ),
				'post_type'   => array(
					'type'      => 'string',
					'pattern'   => '^[a-z0-9_-]+$',
					'maxLength' => 20,
				),
				'title'       => array(
					'type'      => 'string',
					'minLength' => 1,
					'maxLength' => 200,
				),
				'slug'        => array(
					'type'      => 'string',
					'maxLength' => 200,
				),
				'content'     => array(
					'type'                 => 'object',
					'minProperties'        => 1,
					'additionalProperties' => array(
						'anyOf' => array(
							array(
								'type'      => 'string',
								'maxLength' => 20000,
							),
							array(
								'type'                 => 'object',
								'required'             => array( 'id', 'alt' ),
								'properties'           => array(
									'id'  => array(
										'type'    => 'integer',
										'minimum' => 1,
									),
									'alt' => array(
										'type'      => 'string',
										'maxLength' => 1000,
									),
								),
								'additionalProperties' => false,
							),
						),
					),
				),
				'context'     => array(
					'type'                 => 'object',
					'required'             => array( 'related_location_id' ),
					'properties'           => array(
						'related_location_id' => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'description' => 'Published location post used by the koala/city-page pattern.',
						),
					),
					'additionalProperties' => false,
				),
				'structured_data' => array(
					'type'                 => 'object',
					'required'             => array( 'contract_version', 'profile', 'values' ),
					'properties'           => array(
						'contract_version' => array( 'type' => 'integer' ),
						'profile'          => array( 'type' => 'string' ),
						'values'           => array( 'type' => 'object', 'additionalProperties' => true ),
					),
					'additionalProperties' => false,
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the create-draft output schema.
	 *
	 * @return array
	 */
	private function create_draft_output_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'post_id', 'status', 'slug', 'edit_url', 'preview_url' ),
			'properties'           => array(
				'post_id'     => array( 'type' => 'integer' ),
				'status'      => array(
					'type' => 'string',
					'enum' => array( 'draft' ),
				),
				'slug'        => array( 'type' => 'string' ),
				'edit_url'    => array(
					'type'   => 'string',
					'format' => 'uri',
				),
				'preview_url' => array(
					'type'   => 'string',
					'format' => 'uri',
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the external-ID draft lookup input schema.
	 *
	 * @return array
	 */
	private function find_draft_input_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'external_id', 'post_type' ),
			'properties'           => array(
				'external_id' => array(
					'type'      => 'string',
					'minLength' => 1,
					'maxLength' => 191,
					'pattern'   => '^[A-Za-z0-9][A-Za-z0-9._:-]*$',
				),
				'post_type'   => array(
					'type'      => 'string',
					'pattern'   => '^[a-z0-9_-]+$',
					'maxLength' => 20,
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the external-ID draft lookup output schema.
	 *
	 * @return array
	 */
	private function find_draft_output_schema(): array {
		return array(
			'type'                 => 'object',
			'required'             => array( 'found' ),
			'properties'           => array(
				'found'       => array( 'type' => 'boolean' ),
				'post_id'     => array( 'type' => 'integer' ),
				'status'      => array( 'type' => 'string', 'enum' => array( 'draft' ) ),
				'slug'        => array( 'type' => 'string' ),
				'edit_url'    => array( 'type' => 'string', 'format' => 'uri' ),
				'preview_url' => array( 'type' => 'string', 'format' => 'uri' ),
			),
			'additionalProperties' => false,
		);
	}
}

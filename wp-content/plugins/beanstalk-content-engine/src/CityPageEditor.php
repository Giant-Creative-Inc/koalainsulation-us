<?php
/** ACF authoring for City Pages. @package BeanstalkContentEngine */
namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/** Uses the live manifest and the same validator/builder as CSV imports. */
final class CityPageEditor {
	private const PREFIX = 'beanstalk_city_';
	private const ENABLE = 'field_beanstalk_city_enabled';
	private array $cache = array();
	private bool $saving = false;

	public function __construct( private PatternRegistry $patterns, private ContentValidator $validator, private ContentBuilder $builder, private CityPageContext $context, private CityPageUpdater $updater ) {}

	public function register(): void {
		add_action( 'acf/init', array( $this, 'fields' ), 20 );
		add_filter( 'acf/load_value', array( $this, 'load' ), 20, 3 );
		add_action( 'acf/validate_save_post', array( $this, 'validate' ) );
		add_action( 'acf/save_post', array( $this, 'save' ), 20 );
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_filter( 'use_block_editor_for_post', array( $this, 'use_blocks' ), 20, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'editor_style' ) );
		add_action( 'admin_notices', array( $this, 'notice' ) );
	}

	/** Field-managed city pages use the classic title + ACF form. Blocks remain stored. */
	public function use_blocks( bool $use, $post ): bool {
		if ( $post && 'resources-landing-pa' === $post->post_type && ( CityPageUpdater::TEMPLATE_VALUE === get_post_meta( $post->ID, CityPageUpdater::TEMPLATE_META, true ) || get_post_meta( $post->ID, self::PREFIX . 'enabled', true ) || ( isset( $_GET['beanstalk_city'] ) && '1' === $_GET['beanstalk_city'] ) ) ) { return false; }
		return $use;
	}

	public function editor_style(): void {
		$screen = get_current_screen();
		global $post;
		if ( $screen && 'post' === $screen->base && $post && ! $this->use_blocks( true, $post ) ) {
			wp_add_inline_style( 'common', '#postdivrich { display: none; }' );
		}
	}

	public function menu(): void {
		$type = get_post_type_object( 'resources-landing-pa' );
		if ( $type ) {
			add_submenu_page( 'edit.php?post_type=resources-landing-pa', 'Add City Page', 'Add City Page', $type->cap->create_posts, 'post-new.php?post_type=resources-landing-pa&beanstalk_city=1' );
		}
	}

	/** Define fields from the active theme's city manifest rather than a second schema. */
	public function fields(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
		$manifest = $this->patterns->manifest( CityPageUpdater::TEMPLATE_VALUE );
		if ( is_wp_error( $manifest ) ) {
			return;
		}
		$fields = array( array( 'key' => self::ENABLE, 'name' => self::PREFIX . 'enabled', 'label' => 'Generate Beanstalk City Page from these fields', 'type' => 'true_false', 'ui' => 1, 'default_value' => 0, 'instructions' => 'Use the page title above for the city name. These fields generate the page layout when saved. Edit the copy here; saving regenerates the editorial blocks. Save Draft or Publish using the normal WordPress controls.' ) );
		$condition = array( array( array( 'field' => self::ENABLE, 'operator' => '==', 'value' => '1' ) ) );
		$fields[] = array( 'key' => 'field_beanstalk_city_tab_details', 'label' => 'City details', 'type' => 'tab', 'conditional_logic' => $condition );
		foreach ( array( 'related_location_id' => 'Providing location', 'state_name' => 'State name', 'state_abbreviation' => 'State abbreviation', 'service_type' => 'Service type' ) as $id => $label ) {
			$field = array( 'key' => 'field_' . self::PREFIX . $id, 'name' => self::PREFIX . $id, 'label' => $label, 'type' => 'text', 'required' => 1, 'conditional_logic' => $condition );
			if ( 'related_location_id' === $id ) {
				$field = array_merge( $field, array( 'type' => 'post_object', 'post_type' => array( 'location' ), 'post_status' => array( 'publish' ), 'return_format' => 'id', 'ui' => 1, 'multiple' => 0, 'instructions' => 'Select the published franchise providing services in this city.' ) );
			} elseif ( 'service_type' === $id ) {
				$field['default_value'] = 'Insulation services';
			} elseif ( 'state_abbreviation' === $id ) {
				$field['maxlength'] = 2;
			}
			$fields[] = $field;
		}
		$defaults = $this->values( 0 );
		$section = '';
		foreach ( $manifest['fields'] as $id => $definition ) {
			$group = preg_replace( '/_(heading|introduction|description|benefit|form|image|caption|cta|feature|eyebrow|fallback|question|answer|supporting|button|decorative).*$/', '', $id );
			if ( str_starts_with( $id, 'faq_' ) ) { $group = 'faq'; }
			if ( $group !== $section ) {
				$section = $group;
				$fields[] = array( 'key' => 'field_beanstalk_city_tab_' . $group, 'label' => 'faq' === $group ? 'FAQs' : ( 'final' === $group ? 'Final CTA' : ucwords( str_replace( '_', ' ', $group ) ) ), 'type' => 'tab', 'conditional_logic' => $condition );
			}
			$type = 'image' === $definition['type'] ? 'image' : ( 'rich-text' === $definition['type'] ? 'wysiwyg' : 'text' );
			$field = array( 'key' => 'field_' . self::PREFIX . $id, 'name' => self::PREFIX . $id, 'label' => $definition['label'], 'type' => $type, 'required' => ! empty( $definition['required'] ), 'conditional_logic' => $condition, 'default_value' => $defaults[ $id ] ?? '', 'instructions' => 'Placeholders such as {{service_area_name}}, {{state_name}} and {{location_phone}} resolve from city and location data.' );
			if ( 'wysiwyg' === $type ) { $field += array( 'toolbar' => 'basic', 'media_upload' => 0, 'tabs' => 'all', 'delay' => 1 ); }
			if ( 'image' === $type ) {
				$field += array( 'return_format' => 'id', 'preview_size' => 'thumbnail', 'library' => 'all' );
				$field['instructions'] = 'Optional. Leave empty to retain the template image.';
			}
			$fields[] = $field;
			if ( 'image' === $type ) {
				$fields[] = array( 'key' => 'field_' . self::PREFIX . $id . '_alt', 'name' => self::PREFIX . $id . '_alt', 'label' => $definition['label'] . ' — alt text', 'type' => 'text', 'conditional_logic' => $condition, 'default_value' => $defaults[ $id . '_alt' ] ?? '', 'instructions' => 'Describe the image, or leave empty for a decorative image.' );
			}
		}
		acf_add_local_field_group( array( 'key' => 'group_beanstalk_city_editor', 'title' => 'Beanstalk City Page', 'fields' => $fields, 'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'resources-landing-pa' ) ) ), 'position' => 'acf_after_title', 'style' => 'default', 'menu_order' => -10, 'active' => true ) );
	}

	/** Hydrate fields from current blocks so CSV updates and block edits stay visible. */
	public function load( $value, $post_id, array $field ) {
		$name = $field['name'] ?? '';
		if ( ! str_starts_with( $name, self::PREFIX ) || ! is_numeric( $post_id ) ) { return $value; }
		$id = (int) $post_id;
		if ( self::PREFIX . 'enabled' === $name ) {
			if ( CityPageUpdater::TEMPLATE_VALUE === get_post_meta( $id, CityPageUpdater::TEMPLATE_META, true ) ) { return 1; }
			if ( ! metadata_exists( 'post', $id, self::PREFIX . 'enabled' ) && isset( $_GET['beanstalk_city'] ) && '1' === $_GET['beanstalk_city'] ) { return 1; }
			return $value ?? 0;
		}
		if ( CityPageUpdater::TEMPLATE_VALUE !== get_post_meta( $id, CityPageUpdater::TEMPLATE_META, true ) && metadata_exists( 'post', $id, $name ) ) { return $value; }
		$values = $this->values( $id );
		$key = substr( $name, strlen( self::PREFIX ) );
		return $values[ $key ] ?? $value;
	}

	/** Template defaults overlaid with named blocks and the saved city context. */
	private function values( int $id ): array {
		if ( isset( $this->cache[ $id ] ) ) { return $this->cache[ $id ]; }
		$manifest = $this->patterns->manifest( CityPageUpdater::TEMPLATE_VALUE );
		if ( is_wp_error( $manifest ) ) { return array(); }
		$markup = $this->patterns->read_pattern( $manifest['_pattern_path'] );
		$values = is_wp_error( $markup ) ? array() : self::extract( parse_blocks( $markup ), $manifest['fields'] );
		if ( $id ) {
			$post = get_post( $id );
			if ( $post && 'resources-landing-pa' === $post->post_type ) {
				$values = array_replace( $values, self::extract( parse_blocks( $post->post_content ), $manifest['fields'] ) );
				$schema = json_decode( (string) get_post_meta( $id, '_beanstalk_structured_data_values', true ), true );
				foreach ( array( 'state_name', 'state_abbreviation', 'service_type' ) as $key ) { if ( isset( $schema[ $key ] ) ) { $values[ $key ] = $schema[ $key ]; } }
				$relation = get_post_meta( $id, 'rl_related_location', true );
				$location_id = (int) ( is_array( $relation ) ? ( $relation[0] ?? 0 ) : $relation );
				if ( $location_id > 0 ) { $values['related_location_id'] = $location_id; }
			}
		}
		return $this->cache[ $id ] = $values;
	}

	/** Read only explicitly named manifest targets; never consume unrelated blocks. */
	public static function extract( array $blocks, array $fields ): array {
		$values = array();
		foreach ( $blocks as $block ) {
			foreach ( $fields as $id => $definition ) {
				if ( ( $block['attrs']['metadata']['name'] ?? '' ) !== $definition['target']['name'] || ( $block['blockName'] ?? '' ) !== $definition['target']['block'] ) { continue; }
				if ( 'image' === $definition['type'] ) {
					if ( ! empty( $block['attrs']['id'] ) ) { $values[ $id ] = (int) $block['attrs']['id']; $values[ $id . '_alt' ] = $block['attrs']['alt'] ?? ''; }
					continue;
				}
				$tag = 'summary' === $definition['target']['attribute'] ? 'summary' : ( 'core/heading' === $block['blockName'] ? 'h[1-6]' : 'p' );
				if ( preg_match( '#<(?:' . $tag . ')\b[^>]*>(.*?)</(?:' . $tag . ')>#s', $block['innerHTML'] ?? '', $match ) ) {
					$values[ $id ] = 'rich-text' === $definition['type'] ? $match[1] : html_entity_decode( wp_strip_all_tags( $match[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
				}
			}
			$values = array_replace( $values, self::extract( $block['innerBlocks'] ?? array(), $fields ) );
		}
		return $values;
	}

	/** Validate complete editor input before WordPress saves the form. */
	public function prepare( array $posted, string $title, string $slug ) {
		$manifest = $this->patterns->manifest( CityPageUpdater::TEMPLATE_VALUE );
		if ( is_wp_error( $manifest ) ) { return $manifest; }
		$row = array( 'title' => $title, 'slug' => $slug, 'service_area_name' => $title );
		foreach ( array( 'related_location_id', 'state_name', 'state_abbreviation', 'service_type' ) as $id ) { $row[ $id ] = $posted[ 'field_' . self::PREFIX . $id ] ?? ''; }
		$row['state_abbreviation'] = strtoupper( trim( (string) $row['state_abbreviation'] ) );
		if ( '' === trim( $title ) || ! preg_match( '/^[A-Z]{2}$/', $row['state_abbreviation'] ) || '' === trim( $row['state_name'] ) || '' === trim( $row['service_type'] ) ) { return new WP_Error( 'city_editor_context', 'Enter a city title, state name, two-letter state abbreviation and service type.' ); }
		$content = array();
		foreach ( $manifest['fields'] as $id => $definition ) {
			$value = $posted[ 'field_' . self::PREFIX . $id ] ?? null;
			if ( 'image' === $definition['type'] ) {
				$value = $value ? array( 'id' => (int) $value, 'alt' => (string) ( $posted[ 'field_' . self::PREFIX . $id . '_alt' ] ?? '' ) ) : null;
				$row[ $id ] = $value ? wp_json_encode( $value ) : '';
			} else { $row[ $id ] = (string) $value; }
			$content[ $id ] = $value;
		}
		$content = $this->validator->validate( $content, $manifest );
		if ( is_wp_error( $content ) ) { return $content; }
		$context = $this->context->prepare( CityPageUpdater::TEMPLATE_VALUE, 'resources-landing-pa', array( 'related_location_id' => (int) $row['related_location_id'] ), true );
		if ( is_wp_error( $context ) ) { return $context; }
		$built = $this->builder->build( $manifest, $content );
		return is_wp_error( $built ) ? $built : $row;
	}

	public function validate(): void {
		$posted = isset( $_POST['acf'] ) && is_array( $_POST['acf'] ) ? wp_unslash( $_POST['acf'] ) : array();
		if ( empty( $posted[ self::ENABLE ] ) ) { return; }
		$id = (int) ( $_POST['post_ID'] ?? $_POST['_acf_post_id'] ?? 0 );
		if ( ! $id || get_post_type( $id ) !== 'resources-landing-pa' || ! current_user_can( 'edit_post', $id ) ) { acf_add_validation_error( '', 'You cannot edit this City Page.' ); return; }
		$title = sanitize_text_field( wp_unslash( $_POST['post_title'] ?? get_the_title( $id ) ) );
		$result = $this->prepare( $posted, $title, sanitize_title( $title ) );
		if ( is_wp_error( $result ) ) { acf_add_validation_error( '', $result->get_error_message() ); }
	}

	/** ACF has saved its fields at priority 10; regenerate only on explicit editor saves. */
	public function save( $post_id ): void {
		$id = is_numeric( $post_id ) ? (int) $post_id : 0;
		if ( $this->saving || ! $id || empty( $_POST['acf'][ self::ENABLE ] ) || get_post_type( $id ) !== 'resources-landing-pa' || wp_is_post_revision( $id ) || wp_is_post_autosave( $id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $id ) ) { return; }
		$this->saving = true;
		try {
			$post = get_post( $id );
			$posted = wp_unslash( $_POST['acf'] );
			if ( '' === $post->post_name && '' !== trim( $post->post_title ) ) {
				$slug_result = wp_update_post( array( 'ID' => $id, 'post_name' => sanitize_title( $post->post_title ) ), true );
				if ( is_wp_error( $slug_result ) ) { set_transient( 'beanstalk_city_editor_error_' . get_current_user_id(), $slug_result->get_error_message(), 300 ); return; }
				$post = get_post( $id );
			}
			$row = $this->prepare( $posted, $post->post_title, $post->post_name );
			$was_enabled = CityPageUpdater::TEMPLATE_VALUE === get_post_meta( $id, CityPageUpdater::TEMPLATE_META, true );
			if ( ! is_wp_error( $row ) ) { $row['post_id'] = $id; $row = $this->updater->update( $row, true ); }
			if ( is_wp_error( $row ) ) {
				if ( ! $was_enabled && 'publish' === $post->post_status ) { wp_update_post( array( 'ID' => $id, 'post_status' => 'draft' ) ); }
				set_transient( 'beanstalk_city_editor_error_' . get_current_user_id(), $row->get_error_message(), 300 );
			}
			$this->cache = array();
		} finally { $this->saving = false; }
	}

	public function notice(): void {
		$error = get_transient( 'beanstalk_city_editor_error_' . get_current_user_id() );
		if ( $error ) { delete_transient( 'beanstalk_city_editor_error_' . get_current_user_id() ); wp_admin_notice( 'City Page could not be generated: ' . esc_html( $error ), array( 'type' => 'error' ) ); }
	}
}

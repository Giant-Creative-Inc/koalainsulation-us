<?php
/** Validated field previews without changing published pages. @package BeanstalkContentEngine */
namespace GiantCreative\BeanstalkContentEngine;

final class CityPageEditorPreview {
	private ?array $snapshot = null;

	public function __construct( private CityPageEditor $editor, private CityPageUpdater $updater ) {}

	public function register(): void {
		add_action( 'wp_ajax_beanstalk_city_editor_preview', array( $this, 'request' ) );
		add_action( 'init', array( $this, 'load_snapshot' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
	}

	public function assets(): void {
		global $post;
		$screen = get_current_screen();
		if ( ! $screen || 'post' !== $screen->base || ! $post || $this->editor->use_blocks( true, $post ) ) { return; }
		wp_enqueue_script( 'beanstalk-city-editor-preview', plugins_url( 'assets/city-editor-preview.js', BEANSTALK_CONTENT_ENGINE_FILE ), array( 'post' ), BEANSTALK_CONTENT_ENGINE_VERSION, true );
		wp_localize_script( 'beanstalk-city-editor-preview', 'beanstalkCityEditorPreview', array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'beanstalk_city_editor_preview' ) ) );
	}

	/** Explicit Preview saves drafts, but only snapshots changes to published pages. */
	public function request(): void {
		check_ajax_referer( 'beanstalk_city_editor_preview', 'nonce' );
		$id = (int) ( $_POST['post_ID'] ?? 0 );
		$post = get_post( $id );
		if ( ! $post || 'resources-landing-pa' !== $post->post_type || ! current_user_can( 'edit_post', $id ) || ! in_array( $post->post_status, array( 'auto-draft', 'draft', 'publish', 'private', 'pending', 'future' ), true ) ) { wp_send_json_error( 'You cannot preview this City Page.', 403 ); }
		$fields = isset( $_POST['acf'] ) && is_array( $_POST['acf'] ) ? wp_unslash( $_POST['acf'] ) : array();
		if ( empty( $fields['field_beanstalk_city_enabled'] ) ) { wp_send_json_error( 'Enable Beanstalk City Page fields before previewing.', 400 ); }
		$title = sanitize_text_field( wp_unslash( $_POST['post_title'] ?? '' ) );
		$slug = sanitize_title( wp_unslash( $_POST['post_name'] ?? '' ) ) ?: sanitize_title( $title );
		$row = $this->editor->prepare( $fields, $title, $slug );
		if ( is_wp_error( $row ) ) { wp_send_json_error( $row->get_error_message(), 400 ); }
		if ( in_array( $post->post_status, array( 'auto-draft', 'draft' ), true ) ) {
			// Prevent nested WordPress writes from invoking ACF's ordinary save handler.
			$original_fields = $_POST['acf'];
			unset( $_POST['acf'] );
			try {
				$updated = wp_update_post( array( 'ID' => $id, 'post_title' => wp_slash( $title ), 'post_name' => $slug, 'post_status' => 'draft' ), true );
				if ( is_wp_error( $updated ) || ! $updated ) { wp_send_json_error( 'The preview draft could not be saved.', 400 ); }
				$row['post_id'] = $id;
				$result = $this->updater->update( $row, true );
			} finally { $_POST['acf'] = $original_fields; }
			if ( is_wp_error( $result ) ) { wp_send_json_error( $result->get_error_message(), 400 ); }
			wp_send_json_success( array( 'url' => set_url_scheme( get_preview_post_link( $id ) ) ) );
		}
		if ( ! has_term( 'areas-served', 'resources-page-type', $id ) ) { wp_send_json_error( 'Save this page as a City Page before previewing changes.', 400 ); }
		$meta = array(
			'_beanstalk_template' => 'koala/city-page',
			'rl_related_location' => array( (string) $row['related_location_id'] ),
			'_rl_related_location' => 'field_670cb342e69f1',
			'_beanstalk_structured_data_profile' => 'koala/service-area',
			'_beanstalk_structured_data_values' => wp_json_encode( array_intersect_key( $row, array_flip( array( 'service_area_name', 'state_name', 'state_abbreviation', 'service_type' ) ) ) ),
		);
		$meta = array_merge( $meta, CityPageUpdater::seo_values( $row ) );
		$token = wp_generate_uuid4();
		$snapshot = array( 'post_id' => $id, 'title' => $title, 'content' => $row['_editor_post_content'], 'meta' => $meta );
		$key = 'beanstalk_city_preview_' . get_current_user_id() . '_' . $token;
		set_transient( $key, $snapshot, 15 * MINUTE_IN_SECONDS );
		if ( get_transient( $key ) !== $snapshot ) { wp_send_json_error( 'The private preview could not be prepared.', 500 ); }
		wp_send_json_success( array( 'url' => add_query_arg( 'beanstalk_city_preview', $token, set_url_scheme( get_preview_post_link( $id ) ) ) ) );
	}

	/** Only the editing user can activate the short-lived snapshot in a frontend request. */
	public function load_snapshot(): void {
		if ( empty( $_GET['beanstalk_city_preview'] ) || ! is_user_logged_in() ) { return; }
		$token = sanitize_key( wp_unslash( $_GET['beanstalk_city_preview'] ) );
		$value = get_transient( 'beanstalk_city_preview_' . get_current_user_id() . '_' . $token );
		if ( ! is_array( $value ) || ! current_user_can( 'edit_post', $value['post_id'] ) ) { return; }
		$this->snapshot = $value;
		nocache_headers();
		add_filter( 'the_preview', array( $this, 'post' ), 99 );
		add_filter( 'get_post_metadata', array( $this, 'metadata' ), 99, 4 );
		add_filter( 'the_title', array( $this, 'title' ), 99, 2 );
		add_filter( 'post_content', array( $this, 'content' ), 99, 2 );
		add_filter( 'wp_robots', static fn( $robots ) => array_merge( $robots, array( 'noindex' => true, 'nofollow' => true ) ) );
	}

	public function post( $post ) {
		if ( ! $this->snapshot || (int) $post->ID !== (int) $this->snapshot['post_id'] ) { return $post; }
		$preview = clone $post;
		$preview->post_title = $this->snapshot['title'];
		$preview->post_content = $this->snapshot['content'];
		return $preview;
	}

	public function metadata( $value, $id, $key, $single ) {
		if ( $this->snapshot && (int) $id === (int) $this->snapshot['post_id'] && array_key_exists( $key, $this->snapshot['meta'] ) ) { return array( $this->snapshot['meta'][ $key ] ); }
		return $value;
	}

	public function title( $value, $id ) { return $this->snapshot && (int) $id === (int) $this->snapshot['post_id'] ? $this->snapshot['title'] : $value; }
	public function content( $value, $id ) { return $this->snapshot && (int) $id === (int) $this->snapshot['post_id'] ? $this->snapshot['content'] : $value; }
}

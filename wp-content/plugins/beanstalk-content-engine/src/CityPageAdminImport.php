<?php
/** Admin CSV import controller. @package BeanstalkContentEngine */
namespace GiantCreative\BeanstalkContentEngine;

/** Administrator-only, nonce-protected preview and explicit selected-row import. */
final class CityPageAdminImport {
	public function __construct( private CityPageImportJob $service ) {}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_ajax_beanstalk_city_import', array( $this, 'request' ) );
		add_action( 'admin_post_beanstalk_city_template', array( $this, 'download' ) );
	}

	public function menu(): void {
		add_management_page( 'City Page Import', 'City Page Import', 'manage_options', 'beanstalk-city-import', array( $this, 'render' ) );
	}

	public function assets( string $hook ): void {
		if ( 'tools_page_beanstalk-city-import' !== $hook ) {
			return;
		}
		wp_enqueue_script( 'beanstalk-city-import', plugins_url( 'assets/city-import.js', BEANSTALK_CONTENT_ENGINE_FILE ), array(), BEANSTALK_CONTENT_ENGINE_VERSION, true );
		wp_localize_script( 'beanstalk-city-import', 'beanstalkCityImport', array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'beanstalk_city_import' ) ) );
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Administrator access required.' );
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=beanstalk_city_template' ), 'beanstalk_city_template' );
		?>
		<div class="wrap" id="beanstalk-city-import">
			<h1>City Page Import</h1>
			<p>Upload the Local Cities Pages CSV to update existing city pages or create missing pages as drafts. An existing slug must belong to an areas-served page. Missing slugs create new drafts. Existing publication status is preserved. Applying an import also enables the Beanstalk City Page template.</p>
			<p>Title supplies the service-area name. The related_location_id column accepts a numeric ID or an exact published franchise slug (with optional surrounding slashes). Service type defaults to “Insulation services” and the reviews link label to “Read more reviews”. Placeholders remain editable and resolve through the city template. Image URLs must already exist in this site’s Media Library; blank optional images retain pattern defaults.</p>
			<p><a href="<?php echo esc_url( $url ); ?>">Download CSV template</a> · Maximum 2,000 rows / 10 MB. Rows with all city identity fields blank are skipped. Preview does not change pages. Back up the database before applying a batch.</p>
			<form id="city-import-upload"><input type="file" name="csv" accept=".csv,text/csv" required aria-label="City pages CSV"><button class="button button-primary">Upload and preview</button></form>
			<p id="city-import-status" role="status" aria-live="polite"></p>
			<p><button class="button" id="city-import-preview" type="button" disabled>Continue preview</button> <button class="button" id="city-import-select" type="button" disabled>Select all valid rows</button> <button class="button button-primary" id="city-import-apply" type="button" disabled>Apply selected rows</button> <button class="button" id="city-import-report" type="button" disabled>Download results CSV</button></p>
			<table class="widefat striped"><thead><tr><th>Select</th><th>Row</th><th>City</th><th>Slug</th><th>Result</th><th>Details</th></tr></thead><tbody id="city-import-results"></tbody></table>
		</div>
		<?php
	}

	public function download(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Administrator access required.', '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'beanstalk_city_template' );
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="koala-city-pages-template.csv"' );
		readfile( BEANSTALK_CONTENT_ENGINE_PATH . 'templates/city-pages.csv' );
		exit;
	}

	/** Every operation is bound to the current administrator, nonce and unique job. */
	public function request(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Administrator access required.', 403 );
		}
		check_ajax_referer( 'beanstalk_city_import', 'nonce' );
		$key = 'beanstalk_city_import_' . get_current_user_id();
		// Atomic database lock prevents concurrent tabs from replaying the same batch.
		if ( ! add_option( 'beanstalk_city_import_lock', time(), '', false ) ) {
			wp_send_json_error( 'Another import request is running. Retry when it finishes. A stale lock after a server crash must be cleared by an administrator.', 409 );
		}
		try {
			$response = $this->process( $key );
		} catch ( \Throwable $error ) {
			$response = new \WP_Error( 'csv_interrupted', 'Import interrupted. Reload to inspect saved results before retrying. ' . $error->getMessage() );
		} finally {
			delete_option( 'beanstalk_city_import_lock' );
		}
		if ( is_wp_error( $response ) ) {
			wp_send_json_error( $response->get_error_message(), 400 );
		}
		wp_send_json_success( $response );
	}

	private function process( string $key ) {
		$operation = sanitize_key( wp_unslash( $_POST['operation'] ?? '' ) );
		if ( 'upload' === $operation ) {
			$file = $_FILES['csv'] ?? array();
			if ( UPLOAD_ERR_OK !== ( $file['error'] ?? -1 ) || ( $file['size'] ?? 0 ) > 10 * MB_IN_BYTES || ! is_uploaded_file( $file['tmp_name'] ?? '' ) || 'csv' !== strtolower( pathinfo( $file['name'] ?? '', PATHINFO_EXTENSION ) ) ) {
				return new \WP_Error( 'csv_upload', 'Upload a CSV file of at most 10 MB.' );
			}
			$rows = CityPageCsv::read( $file['tmp_name'] );
			if ( is_wp_error( $rows ) ) {
				return $rows;
			}
			$job = $this->service->create( $rows );
			$job['id'] = wp_generate_uuid4();
		} else {
			$job = get_transient( $key );
			$id = sanitize_text_field( wp_unslash( $_POST['job'] ?? '' ) );
			if ( ! is_array( $job ) || ( 'resume' !== $operation && ! hash_equals( $job['id'], $id ) ) ) {
				return new \WP_Error( 'csv_expired', 'This preview expired or was replaced. Upload the CSV again.' );
			}
			if ( 'preview' === $operation ) {
				$this->service->preview( $job );
			} elseif ( 'apply' === $operation ) {
				$selected = isset( $_POST['selected'] ) && is_array( $_POST['selected'] ) ? array_map( 'intval', $_POST['selected'] ) : array();
				$this->service->apply( $job, $selected, static function ( $state ) use ( $key ) {
					set_transient( $key, $state, 2 * HOUR_IN_SECONDS );
					if ( get_transient( $key ) !== $state ) {
						throw new \RuntimeException( 'Import progress could not be saved. Inspect the last page before retrying.' );
					}
				} );
			} elseif ( ! in_array( $operation, array( 'status', 'resume' ), true ) ) {
				return new \WP_Error( 'csv_operation', 'Unknown import operation.' );
			}
		}
		set_transient( $key, $job, 2 * HOUR_IN_SECONDS );
		$items = array();
		foreach ( $job['results'] as $i => $result ) {
			$items[] = array_merge( $result, array( 'index' => $i, 'row' => $job['rows'][ $i ]['__csv_row'] ?? $i + 2, 'title' => $job['rows'][ $i ]['title'], 'slug' => $job['prepared'][ $i ]['slug'] ?? sanitize_title( $job['rows'][ $i ]['slug'] ) ) );
		}
		return array( 'job' => $job['id'], 'cursor' => $job['cursor'], 'total' => count( $job['rows'] ), 'items' => $items );
	}
}

<?php
/**
 * WP-CLI command for controlled City Page CSV migrations.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_CLI;
use WP_Error;

/** Imports spreadsheet CSV rows into existing City Pages. */
final class CityPageCliCommand {
	/**
	 * Create the CLI adapter.
	 *
	 * @param CityPageUpdater $updater Existing-page update service.
	 */
	public function __construct( private CityPageUpdater $updater ) {}

	/**
	 * Validate or update existing Koala City Pages from a CSV export.
	 *
	 * ## OPTIONS
	 *
	 * <csv>
	 * : Absolute path to the City Page Content CSV export.
	 *
	 * [--apply]
	 * : Apply updates. Omit this flag for a read-only dry run.
	 *
	 * [--post-id=<id>]
	 * : Limit processing to exactly one existing WordPress post ID.
	 *
	 * ## EXAMPLES
	 *
	 *     wp beanstalk city-pages update /tmp/city-pages.csv
	 *     wp beanstalk city-pages update /tmp/city-pages.csv --post-id=12345 --apply
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function __invoke( array $args, array $assoc_args ): void {
		$path   = (string) ( $args[0] ?? '' );
		$apply  = array_key_exists( 'apply', $assoc_args );
		$target = isset( $assoc_args['post-id'] ) ? (int) $assoc_args['post-id'] : 0;
		$rows   = self::read_csv( $path );
		if ( is_wp_error( $rows ) ) {
			WP_CLI::error( $rows->get_error_message() );
		}

		$selected = array();
		$results  = array();
		$failed   = false;
		$seen     = array();
		foreach ( $rows as $index => $row ) {
			if ( 0 !== $target && (int) ( $row['post_id'] ?? 0 ) !== $target ) {
				continue;
			}
			$row_post_id = (int) ( $row['post_id'] ?? 0 );
			if ( isset( $seen[ $row_post_id ] ) ) {
				$failed    = true;
				$results[] = array(
					'row'     => $index + 2,
					'post_id' => $row_post_id,
					'result'  => 'error',
					'detail'  => 'duplicate_post_id',
				);
				continue;
			}
			$seen[ $row_post_id ] = true;
			$selected[]           = array(
				'index' => $index,
				'row'   => $row,
			);
			$result               = $this->updater->update( $row, false );
			if ( is_wp_error( $result ) ) {
				$failed    = true;
				$results[] = array(
					'row'     => $index + 2,
					'post_id' => $row['post_id'] ?? '',
					'result'  => 'error',
					'detail'  => $result->get_error_code() . ': ' . $result->get_error_message(),
				);
				continue;
			}
			$results[] = array(
				'row'     => $index + 2,
				'post_id' => $result['post_id'],
				'result'  => $result['action'],
				'detail'  => $result['slug'],
			);
		}
		if ( 0 !== $target && empty( $results ) ) {
			WP_CLI::error( 'The requested post ID was not present in the CSV.' );
		}
		if ( empty( $results ) ) {
			WP_CLI::error( 'The CSV contains no City Page rows.' );
		}
		WP_CLI\Utils\format_items( 'table', $results, array( 'row', 'post_id', 'result', 'detail' ) );
		if ( $failed ) {
			WP_CLI::error( 'Preflight failed. No City Page was changed.' );
		}
		if ( ! $apply ) {
			WP_CLI::success( 'Dry run passed. No WordPress content was changed.' );
			return;
		}

		$results = array();
		foreach ( $selected as $selected_row ) {
			$result = $this->updater->update( $selected_row['row'], true );
			if ( is_wp_error( $result ) ) {
				WP_CLI::error( sprintf( 'Row %d failed during apply: %s', $selected_row['index'] + 2, $result->get_error_message() ) );
			}
			$results[] = array(
				'row'     => $selected_row['index'] + 2,
				'post_id' => $result['post_id'],
				'result'  => $result['action'],
				'detail'  => $result['slug'],
			);
		}
		WP_CLI\Utils\format_items( 'table', $results, array( 'row', 'post_id', 'result', 'detail' ) );
		WP_CLI::success( 'City Page updates completed.' );
	}

	/**
	 * Read a UTF-8 CSV into associative rows without altering source files.
	 *
	 * @param string $path Absolute CSV path.
	 */
	public static function read_csv( string $path ) {
		if ( '' === $path || ! is_readable( $path ) || ! is_file( $path ) ) {
			return new WP_Error( 'beanstalk_import_unreadable', __( 'The CSV file is not readable.', 'beanstalk-content-engine' ) );
		}
		$handle = fopen( $path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Streaming user-supplied CSV is required for bounded memory.
		if ( false === $handle ) {
			return new WP_Error( 'beanstalk_import_unreadable', __( 'The CSV file could not be opened.', 'beanstalk-content-engine' ) );
		}
		$headers = fgetcsv( $handle, null, ',', '"', '' );
		if ( ! is_array( $headers ) ) {
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			return new WP_Error( 'beanstalk_import_empty', __( 'The CSV file has no header row.', 'beanstalk-content-engine' ) );
		}
		$headers[0] = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $headers[0] );
		$headers    = array_map( 'trim', $headers );
		if ( count( $headers ) !== count( array_unique( $headers ) ) || in_array( '', $headers, true ) ) {
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			return new WP_Error( 'beanstalk_import_invalid_headers', __( 'CSV headers must be unique and non-empty.', 'beanstalk-content-engine' ) );
		}
		foreach ( array( 'post_id', 'slug', 'related_location_id', 'service_area_name', 'state_name', 'state_abbreviation', 'service_type' ) as $required ) {
			if ( ! in_array( $required, $headers, true ) ) {
				fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
				/* translators: %s is a required CSV header. */
				return new WP_Error( 'beanstalk_import_missing_header', sprintf( __( 'The CSV is missing required header %s.', 'beanstalk-content-engine' ), $required ) );
			}
		}
		$rows = array();
		while ( true ) {
			$values = fgetcsv( $handle, null, ',', '"', '' );
			if ( false === $values ) {
				break;
			}
			if ( array( '' ) === $values || empty( array_filter( $values, static fn( $value ) => '' !== trim( (string) $value ) ) ) ) {
				continue;
			}
			if ( count( $values ) !== count( $headers ) ) {
				fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
				return new WP_Error( 'beanstalk_import_column_mismatch', __( 'A CSV row does not match the header column count.', 'beanstalk-content-engine' ) );
			}
			$rows[] = array_combine( $headers, $values );
		}
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		return $rows;
	}
}

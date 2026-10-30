<?php
/** Batched import state. @package BeanstalkContentEngine */
namespace GiantCreative\BeanstalkContentEngine;

/** Keeps validated identities fixed between preview and apply. */
final class CityPageImportJob {
	public function __construct( private CityPageUpdater $updater ) {}

	public function create( array $rows ): array {
		return array( 'rows' => $rows, 'prepared' => array(), 'results' => array(), 'cursor' => 0, 'seen' => array() );
	}

	/** Validate five rows without modifying content. */
	public function preview( array &$job ): void {
		$end = min( count( $job['rows'] ), $job['cursor'] + 5 );
		while ( $job['cursor'] < $end ) {
			$i = $job['cursor']++;
			$row = CityPageCsv::normalize( $job['rows'][ $i ] );
			if ( ! is_wp_error( $row ) && isset( $job['seen'][ $row['post_id'] ] ) ) {
				$row = new \WP_Error( 'csv_duplicate', 'Duplicate target page in this CSV.' );
			}
			if ( ! is_wp_error( $row ) ) {
				$job['seen'][ $row['post_id'] ] = true;
				$result = $this->updater->update( $row, false );
			} else {
				$result = $row;
			}
			$job['results'][ $i ] = array(
				'status' => is_wp_error( $result ) ? 'error' : 'validated',
				'detail' => is_wp_error( $result ) ? $result->get_error_message() : 'Ready to update existing page ' . $row['post_id'],
			);
			if ( ! is_wp_error( $result ) ) {
				$job['prepared'][ $i ] = $row;
			}
		}
	}

	/** Apply at most five explicitly selected, previously validated rows. */
	public function apply( array &$job, array $selected, ?callable $save = null ): void {
		if ( $job['cursor'] !== count( $job['rows'] ) ) {
			return;
		}
		foreach ( array_slice( array_unique( array_map( 'intval', $selected ) ), 0, 5 ) as $i ) {
			if ( 'validated' !== ( $job['results'][ $i ]['status'] ?? '' ) || ! isset( $job['prepared'][ $i ] ) ) {
				continue;
			}
			$row = $job['prepared'][ $i ];
			// Persist before writing: an interrupted request must never silently replay a write.
			$job['results'][ $i ] = array( 'status' => 'interrupted', 'detail' => 'Update started. If interrupted, inspect this page before uploading again.' );
			if ( $save ) {
				$save( $job );
			}
			if ( ! has_term( 'areas-served', 'resources-page-type', $row['post_id'] ) ) {
				$result = new \WP_Error( 'csv_target_changed', 'The target is no longer an areas-served page.' );
			} else {
				$result = $this->updater->update( $row, true );
			}
			$job['results'][ $i ] = array(
				'status' => is_wp_error( $result ) ? 'error' : 'updated',
				'detail' => is_wp_error( $result ) ? $result->get_error_message() : 'Updated page ' . $row['post_id'],
			);
			if ( $save ) {
				$save( $job );
			}
		}
	}
}

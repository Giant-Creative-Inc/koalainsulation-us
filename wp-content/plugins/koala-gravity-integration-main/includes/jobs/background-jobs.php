<?php
/**
 * WP-Cron background job queue and processor.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KGI_MAX_RETRIES', 3 );

/**
 * Registers the WP-Cron action for background entry processing.
 *
 * @since 0.1.0
 */
function kgi_register_background_job_hooks(): void {
	add_action( 'kgi_process_quote_entry', 'kgi_process_quote_entry_job', 10, 1 );
	add_action( 'template_redirect', 'kgi_maybe_process_queued_entry_now' );
}

/**
 * Signs an entry ID for use in the thank-you page redirect URL.
 *
 * Prevents the thank-you page trigger (see kgi_maybe_process_queued_entry_now())
 * from being replayed against arbitrary entry IDs.
 *
 * @since 0.1.0
 *
 * @param int $entry_id Gravity Forms entry ID.
 * @return string Signature for the given entry ID.
 */
function kgi_sign_entry_id( int $entry_id ): string {
	return wp_hash( 'kgi_entry_' . $entry_id );
}

/**
 * Processes a queued entry immediately if the request is the signed
 * thank-you page redirect for that entry.
 *
 * The original wp_schedule_single_event() job queued in
 * kgi_queue_quote_entry_job() still exists as a fallback (e.g. if the
 * visitor never reaches the thank-you page) and otherwise depends on
 * WP-Cron's pseudo-cron being triggered by some unrelated future site
 * visit, which is what causes multi-minute delays on low-traffic sites.
 * Processing here, on the request that's guaranteed to follow the
 * redirect, avoids that wait. The status guard in
 * kgi_process_quote_entry_job() keeps the two triggers idempotent.
 *
 * Hooked to `template_redirect`.
 *
 * @since 0.1.0
 */
function kgi_maybe_process_queued_entry_now(): void {
	if ( ! isset( $_GET['kgi_entry'], $_GET['kgi_sig'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$entry_id = absint( $_GET['kgi_entry'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sig      = sanitize_text_field( wp_unslash( $_GET['kgi_sig'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( $entry_id <= 0 || ! hash_equals( kgi_sign_entry_id( $entry_id ), $sig ) ) {
		return;
	}

	kgi_process_quote_entry_job( $entry_id );
}

/**
 * Schedules a single background job to process a quote entry.
 *
 * Deduplicates against already-pending jobs for the same entry. Sets
 * `schedule_failed` status and logs if WP-Cron scheduling returns false.
 *
 * @since 0.1.0
 *
 * @param int $entry_id Gravity Forms entry ID.
 */
function kgi_queue_quote_entry_job( int $entry_id ): void {
	if ( $entry_id <= 0 ) {
		return;
	}

	if ( wp_next_scheduled( 'kgi_process_quote_entry', array( $entry_id ) ) ) {
		return;
	}

	$scheduled = wp_schedule_single_event(
		time() + 10,
		'kgi_process_quote_entry',
		array( $entry_id )
	);

	if ( false === $scheduled ) {
		gform_update_meta( $entry_id, 'kgi_submission_status', 'schedule_failed' );

		kgi_log(
			'Failed to schedule background job.',
			array( 'entry_id' => $entry_id )
		);

		return;
	}

	gform_update_meta( $entry_id, 'kgi_submission_status', 'queued' );
	gform_update_meta( $entry_id, 'kgi_queued_at', time() );

	kgi_log(
		'Quote entry queued for background processing.',
		array( 'entry_id' => $entry_id )
	);
}

/**
 * Re-queues a failed n8n request with exponential backoff.
 *
 * Retries up to KGI_MAX_RETRIES times at 5, 15, and 45 minute intervals.
 * Marks the entry `failed` permanently once retries are exhausted.
 *
 * Only call this for transient n8n failures — not for configuration or
 * data errors, which won't resolve on retry.
 *
 * @since 0.1.0
 *
 * @param int    $entry_id     Gravity Forms entry ID.
 * @param string $error        Error message to store if retries are exhausted.
 */
function kgi_retry_quote_entry_job( int $entry_id, string $error ): void {
	$retry_count = (int) gform_get_meta( $entry_id, 'kgi_retry_count' );
	$delays      = array( 300, 900, 2700 ); // 5 min, 15 min, 45 min

	if ( $retry_count >= KGI_MAX_RETRIES ) {
		gform_update_meta( $entry_id, 'kgi_submission_status', 'failed' );
		gform_update_meta( $entry_id, 'kgi_error_message', $error );

		kgi_log(
			'Max retries reached. Entry marked failed.',
			array(
				'entry_id'    => $entry_id,
				'retry_count' => $retry_count,
				'error'       => $error,
			)
		);

		return;
	}

	$delay = $delays[ $retry_count ];

	gform_update_meta( $entry_id, 'kgi_retry_count', $retry_count + 1 );
	gform_update_meta( $entry_id, 'kgi_submission_status', 'retrying' );

	wp_schedule_single_event(
		time() + $delay,
		'kgi_process_quote_entry',
		array( $entry_id )
	);

	kgi_log(
		'n8n request failed. Retrying.',
		array(
			'entry_id'      => $entry_id,
			'retry'         => $retry_count + 1,
			'delay_seconds' => $delay,
			'error'         => $error,
		)
	);
}

/**
 * Fires a non-blocking webhook request logging the entry to a Google Sheet.
 *
 * The webhook URL is a per-location ACF field ('webhook_url') on the
 * location post, not a plugin-wide setting — different franchise locations
 * can log to different sheets. Best-effort side channel for record-keeping,
 * not required for the quote to be processed. Skipped entirely if the
 * location has no webhook URL set. Uses `blocking => false` so a slow or
 * unreachable endpoint never delays the background job or its retry logic.
 *
 * Even in non-blocking mode, wp_remote_post() still returns a WP_Error
 * immediately if the request never gets dispatched at all (blocked host,
 * DNS failure, invalid URL) — it only skips waiting for the actual HTTP
 * response. That dispatch-time result is persisted to entry meta (visible
 * in the Gravity Forms entry sidebar) since kgi_log() is silently a no-op
 * for anonymous front-end submissions, which would otherwise leave no trace
 * of whether this ran at all.
 *
 * @since 0.1.0
 *
 * @param int     $entry_id Gravity Forms entry ID.
 * @param mixed[] $entry    Gravity Forms entry array.
 * @param WP_Post $location The resolved location CPT post.
 */
function kgi_maybe_send_to_google_sheet( int $entry_id, array $entry, WP_Post $location ): void {
	$webhook_url = get_field( 'webhook_url', $location->ID );

	if ( empty( $webhook_url ) ) {
		gform_update_meta( $entry_id, 'kgi_google_sheet_status', 'skipped_no_url' );
		return;
	}

	try {
		$form_id           = (int) $entry['form_id'];
		$field_map         = kgi_get_field_map_for_form( $form_id );
		$page_url_field_id = kgi_get_page_url_field_id_for_form( $form_id );

		$response = wp_remote_post(
			$webhook_url,
			array(
				'headers'  => array( 'Content-Type' => 'application/json' ),
				'body'     => wp_json_encode( kgi_build_google_sheet_payload( $entry, $field_map, $page_url_field_id ) ),
				'timeout'  => 5,
				'blocking' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			gform_update_meta( $entry_id, 'kgi_google_sheet_status', 'dispatch_failed' );
			gform_update_meta( $entry_id, 'kgi_google_sheet_error', $response->get_error_message() );

			kgi_log(
				'Google Sheet webhook dispatch failed.',
				array(
					'entry_id' => $entry_id,
					'error'    => $response->get_error_message(),
				)
			);

			return;
		}

		gform_update_meta( $entry_id, 'kgi_google_sheet_status', 'dispatched' );
		gform_update_meta( $entry_id, 'kgi_google_sheet_sent_at', time() );

		kgi_log(
			'Entry sent to Google Sheet webhook.',
			array( 'entry_id' => $entry_id )
		);
	} catch ( \Throwable $e ) {
		gform_update_meta( $entry_id, 'kgi_google_sheet_status', 'failed' );
		gform_update_meta( $entry_id, 'kgi_google_sheet_error', $e->getMessage() );

		kgi_log(
			'Google Sheet webhook failed.',
			array(
				'entry_id' => $entry_id,
				'error'    => $e->getMessage(),
			)
		);
	}
}

/**
 * Processes a queued quote entry in the background via WP-Cron.
 *
 * Loads the entry, transitions the status through processing states, and
 * catches any throwable to ensure a `failed` status is always written on error.
 *
 * @since 0.1.0
 *
 * @param int $entry_id Gravity Forms entry ID.
 */
function kgi_process_quote_entry_job( int $entry_id ): void {
	$status = gform_get_meta( $entry_id, 'kgi_submission_status' );

	if ( in_array( $status, array( 'processing', 'succeeded' ), true ) ) {
		return;
	}

	$entry = GFAPI::get_entry( $entry_id );

	if ( is_wp_error( $entry ) ) {
		gform_update_meta( $entry_id, 'kgi_submission_status', 'failed' );
		gform_update_meta( $entry_id, 'kgi_error_message', $entry->get_error_message() );

		kgi_log(
			'Could not load entry for background job.',
			array(
				'entry_id' => $entry_id,
				'error'    => $entry->get_error_message(),
			)
		);

		return;
	}

	gform_update_meta( $entry_id, 'kgi_submission_status', 'processing' );

	kgi_log(
		'Processing quote entry background job.',
		array( 'entry_id' => $entry_id )
	);

	try {
		$location        = kgi_get_location_from_entry_meta( $entry_id );
		$location_source = (string) gform_get_meta( $entry_id, 'kgi_location_source' );
		$needs_review    = (bool) gform_get_meta( $entry_id, 'kgi_needs_review' );
		$routed_location = null;

		// A missing location is only a hard failure when it isn't the known
		// "unresolved" case. An unresolved lead (no URL/ZIP match and no default
		// location configured) is still sent to n8n — flagged, with an empty
		// location payload — so the workflow can alert (e.g. post to Slack)
		// instead of the lead being dropped.
		if ( ! $location && 'unresolved' !== $location_source ) {
			gform_update_meta( $entry_id, 'kgi_submission_status', 'failed' );
			gform_update_meta( $entry_id, 'kgi_error_message', 'Location could not be resolved from entry meta.' );

			kgi_log(
				'Background job aborted. Location not found.',
				array( 'entry_id' => $entry_id )
			);

			return;
		}

		if ( $location ) {
			$routed_location_id = absint( gform_get_meta( $entry_id, 'kgi_routed_location_id' ) );
			$routed_location    = $routed_location_id > 0 ? get_post( $routed_location_id ) : null;

			if ( ! $routed_location instanceof WP_Post || kgi_get_location_post_type() !== $routed_location->post_type ) {
				$routed_location = kgi_resolve_location_for_entry_zip( $entry, $location, $entry_id );
			}

			if ( ! $routed_location ) {
				$original_location_id = $location->ID;

				gform_update_meta( $entry_id, 'kgi_location_status', 'missing_location' );
				gform_update_meta( $entry_id, 'kgi_location_id', '' );
				gform_update_meta( $entry_id, 'kgi_location_name', '' );
				gform_update_meta( $entry_id, 'kgi_location_source', 'unresolved' );
				gform_update_meta( $entry_id, 'kgi_needs_review', 1 );
				gform_update_meta( $entry_id, 'kgi_routed_location_id', '' );
				gform_update_meta( $entry_id, 'kgi_routed_location_name', '' );
				gform_update_meta( $entry_id, 'kgi_zip_routing_status', 'unresolved' );

				if ( ! $needs_review ) {
					kgi_notify_unresolved_lead( $entry_id, $entry, 'unresolved' );
				}

				kgi_log(
					'Submitted ZIP had no exact owner or owner within the fallback radius. Location unassigned for review.',
					array(
						'entry_id'             => $entry_id,
						'original_location_id' => $original_location_id,
					)
				);

				$location        = null;
				$location_source = 'unresolved';
				$needs_review    = true;
			}
		}

		if ( $location ) {
			kgi_maybe_send_to_google_sheet( $entry_id, $entry, $location );
		}

		$webhook_url = get_option( 'kgi_n8n_webhook_url', '' );

		if ( empty( $webhook_url ) ) {
			gform_update_meta( $entry_id, 'kgi_submission_status', 'failed' );
			gform_update_meta( $entry_id, 'kgi_error_message', 'n8n webhook URL is not configured.' );

			kgi_log(
				'Background job aborted. No webhook URL configured.',
				array( 'entry_id' => $entry_id )
			);

			return;
		}

		if ( $location ) {
			gform_update_meta( $entry_id, 'kgi_routed_location_id', $routed_location->ID );
			gform_update_meta( $entry_id, 'kgi_routed_location_name', get_field( 'location_name', $routed_location->ID ) );
			gform_update_meta(
				$entry_id,
				'kgi_zip_routing_status',
				$routed_location->ID === $location->ID ? 'original_location' : 'reassigned'
			);

			if ( $routed_location->ID !== $location->ID ) {
				kgi_log(
					'Location reassigned by submitted ZIP/postal code.',
					array(
						'entry_id'             => $entry_id,
						'original_location_id' => $location->ID,
						'routed_location_id'   => $routed_location->ID,
					)
				);
			}

			$location_payload = kgi_build_location_payload( $routed_location );
		} else {
			$location_payload = kgi_build_unresolved_location_payload();
		}

		// Routing flags so the n8n workflow can branch — e.g. post a Slack
		// alert when no location was found instead of pushing to a CRM.
		$routing_flags = array(
			'location_found'  => (bool) $location,
			'location_source' => '' !== $location_source ? $location_source : ( $location ? 'url' : 'unresolved' ),
			'needs_review'    => $needs_review,
		);

		$entry_payload = kgi_build_entry_payload( $entry, kgi_get_field_map_for_form( (int) $entry['form_id'] ) );

		// Always include every attribution/tracking key, defaulting to an empty
		// string when this form has no field mapped for it, so n8n receives a
		// stable payload shape regardless of which fields a given form carries.
		foreach ( kgi_get_tracking_field_keys() as $tracking_key ) {
			if ( ! array_key_exists( $tracking_key, $entry_payload ) ) {
				$entry_payload[ $tracking_key ] = '';
			}
		}

		// The form ID is intrinsic to the entry, so it is set from the entry
		// itself rather than a mapped hidden field — always correct, nothing to
		// configure on the form.
		$entry_payload['form_id'] = (string) $entry['form_id'];

		$payload = array_merge(
			$entry_payload,
			$location_payload,
			$routing_flags
		);

		$sent_at      = time();
		$submitted_at = strtotime( $entry['date_created'] . ' +00:00' );
		$send_delay   = false !== $submitted_at ? $sent_at - $submitted_at : null;

		gform_update_meta( $entry_id, 'kgi_sent_at', $sent_at );

		if ( null !== $send_delay ) {
			gform_update_meta( $entry_id, 'kgi_send_delay_seconds', $send_delay );
		}

		kgi_log(
			'Sending entry to n8n.',
			array(
				'entry_id'      => $entry_id,
				'submitted_at'  => $entry['date_created'],
				'sent_at'       => gmdate( 'Y-m-d H:i:s', $sent_at ),
				'delay_seconds' => $send_delay,
			)
		);

		$response = wp_remote_post(
			$webhook_url,
			array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( $payload ),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			kgi_log(
				'n8n request failed.',
				array(
					'entry_id' => $entry_id,
					'error'    => $response->get_error_message(),
				)
			);

			kgi_retry_quote_entry_job( $entry_id, $response->get_error_message() );

			return;
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( $status_code < 200 || $status_code >= 300 ) {
			$body  = wp_remote_retrieve_body( $response );
			$error = 'n8n returned HTTP ' . $status_code . ': ' . $body;

			kgi_log(
				'n8n returned a non-2xx response.',
				array(
					'entry_id'    => $entry_id,
					'status_code' => $status_code,
					'body'        => $body,
				)
			);

			kgi_retry_quote_entry_job( $entry_id, $error );

			return;
		}

		gform_update_meta( $entry_id, 'kgi_submission_status', 'succeeded' );

		kgi_log(
			'Entry successfully sent to n8n.',
			array(
				'entry_id'    => $entry_id,
				'status_code' => $status_code,
			)
		);

	} catch ( \Throwable $e ) {
		gform_update_meta( $entry_id, 'kgi_submission_status', 'failed' );
		gform_update_meta( $entry_id, 'kgi_error_message', $e->getMessage() );

		kgi_log(
			'Background job failed.',
			array(
				'entry_id' => $entry_id,
				'error'    => $e->getMessage(),
			)
		);
	}
}

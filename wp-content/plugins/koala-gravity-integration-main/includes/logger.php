<?php
/**
 * Debug logging utility.
 *
 * @package Koala_Gravity_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Writes a prefixed debug log entry when WP_DEBUG is enabled.
 *
 * Only logs for logged-in users or during WP-Cron runs so that anonymous
 * production traffic does not generate log entries.
 *
 * @since 0.1.0
 *
 * @param string  $message Log message.
 * @param mixed[] $context Optional key-value context data appended as JSON.
 */
function kgi_log( string $message, array $context = array() ): void {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}

	if ( ! is_user_logged_in() && ! wp_doing_cron() ) {
		return;
	}

	$line = '[KGI] ' . $message;

	if ( ! empty( $context ) ) {
		$line .= ' ' . wp_json_encode( $context );
	}

	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log( $line );
}

/**
 * Writes a prefixed error to the server error log, always.
 *
 * Unlike kgi_log(), this is not limited to WP_DEBUG or logged-in users, so
 * problems that affect live visitors (such as failed ZIP lookups) are always
 * visible to the host. Never pass secrets in the context.
 *
 * @since 0.8.0
 *
 * @param string  $message Log message.
 * @param mixed[] $context Optional key-value context data appended as JSON.
 */
function kgi_log_error( string $message, array $context = array() ): void {
	$line = '[KGI] ' . $message;

	if ( ! empty( $context ) ) {
		$line .= ' ' . wp_json_encode( $context );
	}

	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log( $line );
}

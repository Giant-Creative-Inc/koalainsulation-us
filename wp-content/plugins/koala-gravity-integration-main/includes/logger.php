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

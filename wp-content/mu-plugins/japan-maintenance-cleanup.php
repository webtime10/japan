<?php
/**
 * Plugin Name: Japan maintenance cleanup
 * Description: Removes stale WordPress .maintenance after failed or stuck updates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'plugins_loaded',
	static function () {
		$file = ABSPATH . '.maintenance';
		if ( ! is_file( $file ) ) {
			return;
		}

		$upgrading = 0;
		include $file;

		// Core ignores maintenance older than 10 minutes; delete the file so it cannot get stuck.
		if ( ( time() - (int) $upgrading ) >= 10 * MINUTE_IN_SECONDS ) {
			@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
	},
	0
);

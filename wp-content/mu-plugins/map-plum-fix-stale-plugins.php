<?php
/**
 * Removes missing plugins from active_plugins (e.g. deleted plum22).
 * Must-use: loads before regular plugins.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'plugins_loaded',
	function () {
		$active = get_option( 'active_plugins', array() );
		if ( ! is_array( $active ) || empty( $active ) ) {
			return;
		}

		$changed = false;
		foreach ( $active as $key => $plugin ) {
			if ( ! is_string( $plugin ) || $plugin === '' ) {
				unset( $active[ $key ] );
				$changed = true;
				continue;
			}
			if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
				unset( $active[ $key ] );
				$changed = true;
			}
		}

		if ( $changed ) {
			update_option( 'active_plugins', array_values( $active ) );
		}
	},
	1
);

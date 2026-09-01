<?php
/**
 * Database tables.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return void
 */
function fcc_active_bd() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();
	$prefix          = $wpdb->prefix . 'fcc_';

	dbDelta(
		"CREATE TABLE {$prefix}language (
		language_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(64) NOT NULL DEFAULT '',
		code varchar(5) NOT NULL DEFAULT '',
		locale varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		PRIMARY KEY  (language_id),
		UNIQUE KEY code (code)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}category (
		category_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		group_type varchar(32) NOT NULL DEFAULT 'age',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (category_id),
		KEY group_type (group_type)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}category_description (
		category_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		PRIMARY KEY  (category_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);
}

/**
 * Одна запись языка для внутренней связи с описаниями.
 *
 * @return void
 */
function fcc_seed_languages() {
	global $wpdb;

	$prefix = $wpdb->prefix . 'fcc_';
	$table  = $prefix . 'language';
	$count  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	if ( $count > 0 ) {
		return;
	}

	$wpdb->insert(
		$table,
		array(
			'name'       => 'Default',
			'code'       => 'ru',
			'locale'     => 'ru_RU',
			'sort_order' => 0,
			'status'     => 1,
		),
		array( '%s', '%s', '%s', '%d', '%d' )
	);
}

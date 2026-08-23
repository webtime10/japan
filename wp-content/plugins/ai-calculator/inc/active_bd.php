<?php
/**
 * Database tables (OpenCart-style catalog).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return void
 */
function ai_calculator_active_bd() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();
	$prefix          = $wpdb->prefix . 'ai_calculator_';

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
		manufacturer_id bigint(20) unsigned NOT NULL DEFAULT 0,
		parent_id bigint(20) unsigned NOT NULL DEFAULT 0,
		image varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (category_id),
		KEY parent_id (parent_id),
		KEY manufacturer_id (manufacturer_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_add_category_manufacturer_column( $prefix );

	dbDelta(
		"CREATE TABLE {$prefix}category_description (
		category_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		meta_title varchar(255) NOT NULL DEFAULT '',
		meta_description varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (category_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}manufacturer (
		manufacturer_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		image varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (manufacturer_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}manufacturer_description (
		manufacturer_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		PRIMARY KEY  (manufacturer_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}product (
		product_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		manufacturer_id bigint(20) unsigned NOT NULL DEFAULT 0,
		image varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (product_id),
		KEY manufacturer_id (manufacturer_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_add_product_manufacturer_column( $prefix );
	ai_calculator_maybe_fix_product_auto_increment( $prefix );

	dbDelta(
		"CREATE TABLE {$prefix}product_description (
		product_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		block1 varchar(255) NOT NULL DEFAULT '',
		block2 varchar(255) NOT NULL DEFAULT '',
		block3 varchar(255) NOT NULL DEFAULT '',
		block4 varchar(255) NOT NULL DEFAULT '',
		block5 varchar(255) NOT NULL DEFAULT '',
		block6 varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (product_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_add_product_description_block_columns( $prefix );

	dbDelta(
		"CREATE TABLE {$prefix}product_to_category (
		product_id bigint(20) unsigned NOT NULL,
		category_id bigint(20) unsigned NOT NULL,
		PRIMARY KEY  (product_id,category_id),
		KEY category_id (category_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}product_related (
		product_id bigint(20) unsigned NOT NULL,
		related_product_id bigint(20) unsigned NOT NULL,
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (product_id,related_product_id),
		KEY product_id (product_id),
		KEY related_product_id (related_product_id)
	) $charset_collate;"
	);

	ai_calculator_seed_languages( $prefix );
}

/**
 * Удалить таблицы prompt_* (один раз при обновлении плагина).
 *
 * @param string $prefix Table prefix, e.g. wp_ai_calculator_
 * @return void
 */
function ai_calculator_drop_prompt_tables( $prefix ) {
	if ( get_option( 'ai_calculator_prompt_tables_dropped' ) ) {
		return;
	}

	global $wpdb;

	$tables = array(
		'prompt_product_to_category',
		'prompt_product_description',
		'prompt_product',
		'prompt_category_description',
		'prompt_category',
	);

	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE IF EXISTS `{$prefix}{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	update_option( 'ai_calculator_prompt_tables_dropped', '1', false );
}

/**
 * Add manufacturer_id to category table on upgrade from older versions.
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_category_manufacturer_column( $prefix ) {
	global $wpdb;

	$table  = $prefix . 'category';
	$column = $wpdb->get_results( "SHOW COLUMNS FROM `{$table}` LIKE 'manufacturer_id'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! empty( $column ) ) {
		return;
	}

	$wpdb->query( "ALTER TABLE `{$table}` ADD COLUMN manufacturer_id bigint(20) unsigned NOT NULL DEFAULT 0 AFTER category_id, ADD KEY manufacturer_id (manufacturer_id)" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Колонки block1–block6 в product_description (с миграцией со старых имён).
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_product_description_block_columns( $prefix ) {
	global $wpdb;

	$table      = $prefix . 'product_description';
	$definition = "varchar(255) NOT NULL DEFAULT ''";
	$renames    = array(
		'block_one'    => 'block1',
		'block_two'    => 'block2',
		'block_three'  => 'block3',
		'chapter_four' => 'block4',
	);

	foreach ( $renames as $old_name => $new_name ) {
		$old_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW COLUMNS FROM `{$table}` LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$old_name
			)
		);
		$new_exists = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW COLUMNS FROM `{$table}` LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$new_name
			)
		);

		if ( ! empty( $old_exists ) && empty( $new_exists ) ) {
			$wpdb->query(
				"ALTER TABLE `{$table}` CHANGE `{$old_name}` `{$new_name}` {$definition}" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			);
		}
	}

	foreach ( array( 'block1', 'block2', 'block3', 'block4', 'block5', 'block6' ) as $column ) {
		$exists = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW COLUMNS FROM `{$table}` LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$column
			)
		);
		if ( ! empty( $exists ) ) {
			continue;
		}

		$wpdb->query(
			"ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);
	}
}

/**
 * Add manufacturer_id to product table on upgrade from older versions.
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_product_manufacturer_column( $prefix ) {
	global $wpdb;

	$table  = $prefix . 'product';
	$column = $wpdb->get_results( "SHOW COLUMNS FROM `{$table}` LIKE 'manufacturer_id'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! empty( $column ) ) {
		return;
	}

	$wpdb->query( "ALTER TABLE `{$table}` ADD COLUMN manufacturer_id bigint(20) unsigned NOT NULL DEFAULT 0 AFTER product_id, ADD KEY manufacturer_id (manufacturer_id)" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Older dbDelta runs could leave product_id without AUTO_INCREMENT.
 *
 * @param string $prefix Table prefix including trailing underscore part.
 */
function ai_calculator_maybe_fix_product_auto_increment( $prefix ) {
	global $wpdb;

	$table       = $prefix . 'product';
	$desc_table  = $prefix . 'product_description';
	$p2c_table   = $prefix . 'product_to_category';
	$rel_table   = $prefix . 'product_related';
	$zero_exists = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}` WHERE product_id = 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $zero_exists > 0 ) {
		$next_id = (int) $wpdb->get_var( "SELECT COALESCE(MAX(product_id), 0) + 1 FROM `{$table}` WHERE product_id > 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $next_id > 0 ) {
			$wpdb->update( $table, array( 'product_id' => $next_id ), array( 'product_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $desc_table, array( 'product_id' => $next_id ), array( 'product_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $p2c_table, array( 'product_id' => $next_id ), array( 'product_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $rel_table, array( 'product_id' => $next_id ), array( 'product_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $rel_table, array( 'related_product_id' => $next_id ), array( 'related_product_id' => 0 ), array( '%d' ), array( '%d' ) );
		}
	}

	$row = $wpdb->get_row( "SHOW COLUMNS FROM `{$table}` LIKE 'product_id'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $row ) {
		return;
	}

	// AUTO_INCREMENT requires an index; older tables sometimes lost PRIMARY KEY entirely.
	if ( 'PRI' !== (string) $row->Key ) {
		$has_primary = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
				WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND CONSTRAINT_TYPE = 'PRIMARY KEY'",
				DB_NAME,
				$table
			)
		);
		if ( $has_primary < 1 ) {
			$wpdb->query( "ALTER TABLE `{$table}` ADD PRIMARY KEY (`product_id`)" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
	}

	if ( false === stripos( (string) $row->Extra, 'auto_increment' ) ) {
		$wpdb->query( "ALTER TABLE `{$table}` MODIFY `product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	$max = (int) $wpdb->get_var( "SELECT COALESCE(MAX(product_id), 0) FROM `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $max > 0 ) {
		$next = $max + 1;
		$wpdb->query( "ALTER TABLE `{$table}` AUTO_INCREMENT = {$next}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}

/**
 * @param string $prefix Table prefix including trailing underscore part.
 */
function ai_calculator_seed_languages( $prefix ) {
	global $wpdb;

	$table = $prefix . 'language';
	$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $count > 0 ) {
		return;
	}

	$defaults = array(
		array( 'English', 'en', 'en_US', 0 ),
		array( 'Русский', 'ru', 'ru_RU', 1 ),
	);

	foreach ( $defaults as $row ) {
		$wpdb->insert(
			$table,
			array(
				'name'       => $row[0],
				'code'       => $row[1],
				'locale'     => $row[2],
				'sort_order' => $row[3],
				'status'     => 1,
			),
			array( '%s', '%s', '%s', '%d', '%d' )
		);
	}
}

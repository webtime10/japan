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
		image2 varchar(255) NOT NULL DEFAULT '',
		image3 varchar(255) NOT NULL DEFAULT '',
		image4 varchar(255) NOT NULL DEFAULT '',
		image5 varchar(255) NOT NULL DEFAULT '',
		image6 varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (product_id),
		KEY manufacturer_id (manufacturer_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_add_product_manufacturer_column( $prefix );
	ai_calculator_maybe_add_product_gallery_image_columns( $prefix );
	ai_calculator_maybe_fix_manufacturer_auto_increment( $prefix );
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

	dbDelta(
		"CREATE TABLE {$prefix}attribute_group (
		attribute_group_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (attribute_group_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}attribute_group_description (
		attribute_group_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(64) NOT NULL DEFAULT '',
		PRIMARY KEY  (attribute_group_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}attribute (
		attribute_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		attribute_group_id bigint(20) unsigned NOT NULL DEFAULT 0,
		image varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (attribute_id),
		KEY attribute_group_id (attribute_group_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}attribute_description (
		attribute_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(64) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		block1 varchar(255) NOT NULL DEFAULT '',
		block2 varchar(255) NOT NULL DEFAULT '',
		block3 varchar(255) NOT NULL DEFAULT '',
		block4 varchar(255) NOT NULL DEFAULT '',
		block5 varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (attribute_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_add_attribute_extended_columns( $prefix );

	dbDelta(
		"CREATE TABLE {$prefix}product_attribute (
		product_id bigint(20) unsigned NOT NULL,
		attribute_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		text text NOT NULL,
		PRIMARY KEY  (product_id,attribute_id,language_id),
		KEY attribute_id (attribute_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_fix_attribute_auto_increment( $prefix );
	ai_calculator_maybe_fix_category_auto_increment( $prefix );
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
 * Older dbDelta runs could leave category_id without AUTO_INCREMENT.
 *
 * @param string $prefix Table prefix including trailing underscore part.
 */
function ai_calculator_maybe_fix_category_auto_increment( $prefix ) {
	global $wpdb;

	$table      = $prefix . 'category';
	$desc_table = $prefix . 'category_description';
	$p2c_table  = $prefix . 'product_to_category';
	$zero_exists = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}` WHERE category_id = 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $zero_exists > 0 ) {
		$next_id = (int) $wpdb->get_var( "SELECT COALESCE(MAX(category_id), 0) + 1 FROM `{$table}` WHERE category_id > 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $next_id > 0 ) {
			$wpdb->update( $table, array( 'category_id' => $next_id ), array( 'category_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $desc_table, array( 'category_id' => $next_id ), array( 'category_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $p2c_table, array( 'category_id' => $next_id ), array( 'category_id' => 0 ), array( '%d' ), array( '%d' ) );
		}
	}

	$row = $wpdb->get_row( "SHOW COLUMNS FROM `{$table}` LIKE 'category_id'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $row || false !== stripos( (string) $row->Extra, 'auto_increment' ) ) {
		return;
	}

	$wpdb->query( "ALTER TABLE `{$table}` MODIFY `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
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

	foreach ( array( 'block1', 'block2', 'block3', 'block4', 'block5', 'block6', 'block7', 'block8' ) as $column ) {
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

	ai_calculator_maybe_add_product_description_dop1_column( $prefix );
}

/**
 * Подсказка выбора на шаге квиза (dop1): «Один вариант ответа» / «До двух…».
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_product_description_dop1_column( $prefix ) {
	global $wpdb;

	$table  = $prefix . 'product_description';
	$exists = $wpdb->get_results(
		$wpdb->prepare(
			"SHOW COLUMNS FROM `{$table}` LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			'dop1'
		)
	);
	if ( empty( $exists ) ) {
		$wpdb->query(
			"ALTER TABLE `{$table}` ADD COLUMN `dop1` varchar(255) NOT NULL DEFAULT '' AFTER `block8`" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);
	}

	ai_calculator_maybe_add_product_description_dop2_column( $prefix );
}

/**
 * Текст счётчика шага Ideal Region (dop2): «Вопрос» в «Вопрос 1 из 8».
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_product_description_dop2_column( $prefix ) {
	global $wpdb;

	$table  = $prefix . 'product_description';
	$exists = $wpdb->get_results(
		$wpdb->prepare(
			"SHOW COLUMNS FROM `{$table}` LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			'dop2'
		)
	);
	if ( ! empty( $exists ) ) {
		return;
	}

	$wpdb->query(
		"ALTER TABLE `{$table}` ADD COLUMN `dop2` varchar(255) NOT NULL DEFAULT '' AFTER `dop1`" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	);
}

/**
 * Колонки image, description, block1–block5 для атрибутов.
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_attribute_extended_columns( $prefix ) {
	global $wpdb;

	$attr_table = $prefix . 'attribute';
	$image_col  = $wpdb->get_results( "SHOW COLUMNS FROM `{$attr_table}` LIKE 'image'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( empty( $image_col ) ) {
		$wpdb->query( "ALTER TABLE `{$attr_table}` ADD COLUMN image varchar(255) NOT NULL DEFAULT '' AFTER attribute_group_id" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	$desc_table = $prefix . 'attribute_description';
	$columns    = array(
		'description' => 'longtext NOT NULL',
		'image'       => "varchar(255) NOT NULL DEFAULT ''",
		'block1'      => "varchar(255) NOT NULL DEFAULT ''",
		'block2'      => "varchar(255) NOT NULL DEFAULT ''",
		'block3'      => "varchar(255) NOT NULL DEFAULT ''",
		'block4'      => "varchar(255) NOT NULL DEFAULT ''",
		'block5'      => "varchar(255) NOT NULL DEFAULT ''",
	);

	foreach ( $columns as $column => $definition ) {
		$exists = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW COLUMNS FROM `{$desc_table}` LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$column
			)
		);
		if ( ! empty( $exists ) ) {
			continue;
		}

		$wpdb->query(
			"ALTER TABLE `{$desc_table}` ADD COLUMN `{$column}` {$definition}" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);
	}
}

/**
 * Доп. поля в product_attribute: метка, фото, block1–block5.
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_product_attribute_columns( $prefix ) {
	global $wpdb;

	$table   = $prefix . 'product_attribute';
	$columns = array(
		'label'  => "varchar(255) NOT NULL DEFAULT ''",
		'image'  => "varchar(255) NOT NULL DEFAULT ''",
		'block1' => "varchar(255) NOT NULL DEFAULT ''",
		'block2' => "varchar(255) NOT NULL DEFAULT ''",
		'block3' => "varchar(255) NOT NULL DEFAULT ''",
		'block4' => "varchar(255) NOT NULL DEFAULT ''",
		'block5' => "varchar(255) NOT NULL DEFAULT ''",
		'block6' => "varchar(255) NOT NULL DEFAULT ''",
		'block7' => "varchar(255) NOT NULL DEFAULT ''",
		'block8' => "varchar(255) NOT NULL DEFAULT ''",
	);

	foreach ( $columns as $column => $definition ) {
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
 * Дополнительные фото товара (image2–image6).
 *
 * @param string $prefix
 */
function ai_calculator_maybe_add_product_gallery_image_columns( $prefix ) {
	global $wpdb;

	$table = $prefix . 'product';
	$after = 'image';

	foreach ( array( 'image2', 'image3', 'image4', 'image5', 'image6' ) as $column ) {
		$exists = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW COLUMNS FROM `{$table}` LIKE %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$column
			)
		);
		if ( ! empty( $exists ) ) {
			$after = $column;
			continue;
		}

		$wpdb->query(
			"ALTER TABLE `{$table}` ADD COLUMN `{$column}` varchar(255) NOT NULL DEFAULT '' AFTER `{$after}`" // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);
		$after = $column;
	}
}

/**
 * Older dbDelta runs could leave manufacturer_id without AUTO_INCREMENT.
 *
 * @param string $prefix Table prefix including trailing underscore part.
 */
function ai_calculator_maybe_fix_manufacturer_auto_increment( $prefix ) {
	global $wpdb;

	$table      = $prefix . 'manufacturer';
	$desc_table = $prefix . 'manufacturer_description';
	$cat_table  = $prefix . 'category';
	$prod_table = $prefix . 'product';
	$zero_exists = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}` WHERE manufacturer_id = 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $zero_exists > 0 ) {
		$next_id = (int) $wpdb->get_var( "SELECT COALESCE(MAX(manufacturer_id), 0) + 1 FROM `{$table}` WHERE manufacturer_id > 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $next_id > 0 ) {
			$wpdb->update( $table, array( 'manufacturer_id' => $next_id ), array( 'manufacturer_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $desc_table, array( 'manufacturer_id' => $next_id ), array( 'manufacturer_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $cat_table, array( 'manufacturer_id' => $next_id ), array( 'manufacturer_id' => 0 ), array( '%d' ), array( '%d' ) );
			$wpdb->update( $prod_table, array( 'manufacturer_id' => $next_id ), array( 'manufacturer_id' => 0 ), array( '%d' ), array( '%d' ) );
		}
	}

	$row = $wpdb->get_row( "SHOW COLUMNS FROM `{$table}` LIKE 'manufacturer_id'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! $row || false !== stripos( (string) $row->Extra, 'auto_increment' ) ) {
		return;
	}

	$wpdb->query( "ALTER TABLE `{$table}` MODIFY `manufacturer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
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
 * Create attribute tables on upgrade (OpenCart-style).
 *
 * @param string $prefix Table prefix including trailing underscore part.
 */
function ai_calculator_maybe_create_attribute_tables( $prefix ) {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $prefix . 'attribute' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	if ( $exists === $prefix . 'attribute' ) {
		ai_calculator_maybe_fix_attribute_auto_increment( $prefix );
		return;
	}

	$charset_collate = $wpdb->get_charset_collate();

	dbDelta(
		"CREATE TABLE {$prefix}attribute_group (
		attribute_group_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (attribute_group_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}attribute_group_description (
		attribute_group_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(64) NOT NULL DEFAULT '',
		PRIMARY KEY  (attribute_group_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}attribute (
		attribute_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		attribute_group_id bigint(20) unsigned NOT NULL DEFAULT 0,
		image varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY  (attribute_id),
		KEY attribute_group_id (attribute_group_id)
	) $charset_collate;"
	);

	dbDelta(
		"CREATE TABLE {$prefix}attribute_description (
		attribute_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(64) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		block1 varchar(255) NOT NULL DEFAULT '',
		block2 varchar(255) NOT NULL DEFAULT '',
		block3 varchar(255) NOT NULL DEFAULT '',
		block4 varchar(255) NOT NULL DEFAULT '',
		block5 varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (attribute_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_add_attribute_extended_columns( $prefix );

	dbDelta(
		"CREATE TABLE {$prefix}product_attribute (
		product_id bigint(20) unsigned NOT NULL,
		attribute_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		text text NOT NULL,
		PRIMARY KEY  (product_id,attribute_id,language_id),
		KEY attribute_id (attribute_id)
	) $charset_collate;"
	);

	ai_calculator_maybe_fix_attribute_auto_increment( $prefix );
}

/**
 * Ensure attribute tables have PRIMARY KEY + AUTO_INCREMENT (dbDelta sometimes drops them).
 *
 * @param string $prefix Table prefix including trailing underscore part.
 */
function ai_calculator_maybe_fix_attribute_auto_increment( $prefix ) {
	global $wpdb;

	$tables = array(
		'attribute_group' => 'attribute_group_id',
		'attribute'       => 'attribute_id',
	);

	foreach ( $tables as $table_name => $id_column ) {
		$table = $prefix . $table_name;
		$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( $found !== $table ) {
			continue;
		}

		// Remap id=0 rows — AUTO_INCREMENT / PK cannot start with zeros.
		$zero_exists = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}` WHERE `{$id_column}` = 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $zero_exists > 0 ) {
			$next_id = (int) $wpdb->get_var( "SELECT COALESCE(MAX(`{$id_column}`), 0) + 1 FROM `{$table}` WHERE `{$id_column}` > 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			if ( $next_id <= 0 ) {
				$next_id = 1;
			}
			if ( 'attribute' === $table_name ) {
				$wpdb->update( $prefix . 'attribute_description', array( 'attribute_id' => $next_id ), array( 'attribute_id' => 0 ), array( '%d' ), array( '%d' ) );
				$wpdb->update( $prefix . 'product_attribute', array( 'attribute_id' => $next_id ), array( 'attribute_id' => 0 ), array( '%d' ), array( '%d' ) );
			} elseif ( 'attribute_group' === $table_name ) {
				$wpdb->update( $prefix . 'attribute_group_description', array( 'attribute_group_id' => $next_id ), array( 'attribute_group_id' => 0 ), array( '%d' ), array( '%d' ) );
				$wpdb->update( $prefix . 'attribute', array( 'attribute_group_id' => $next_id ), array( 'attribute_group_id' => 0 ), array( '%d' ), array( '%d' ) );
			}
			$wpdb->update( $table, array( $id_column => $next_id ), array( $id_column => 0 ), array( '%d' ), array( '%d' ) );
		}

		$has_primary = (int) $wpdb->get_var( "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = '{$table}' AND index_name = 'PRIMARY'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $has_primary <= 0 ) {
			// Drop duplicate empty ids if any before adding PK.
			$wpdb->query( "ALTER TABLE `{$table}` ADD PRIMARY KEY (`{$id_column}`)" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		$row = $wpdb->get_row( "SHOW COLUMNS FROM `{$table}` LIKE '{$id_column}'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $row && false === stripos( (string) $row->Extra, 'auto_increment' ) ) {
			$max_id = (int) $wpdb->get_var( "SELECT COALESCE(MAX(`{$id_column}`), 0) FROM `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "ALTER TABLE `{$table}` MODIFY `{$id_column}` bigint(20) unsigned NOT NULL AUTO_INCREMENT" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			if ( $max_id > 0 ) {
				$wpdb->query( "ALTER TABLE `{$table}` AUTO_INCREMENT = " . ( $max_id + 1 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}
		}
	}

	// Composite PKs on description / product link tables.
	$composites = array(
		$prefix . 'attribute_group_description' => array( 'attribute_group_id', 'language_id' ),
		$prefix . 'attribute_description'       => array( 'attribute_id', 'language_id' ),
		$prefix . 'product_attribute'           => array( 'product_id', 'attribute_id', 'language_id' ),
	);

	foreach ( $composites as $table => $columns ) {
		$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( $found !== $table ) {
			continue;
		}
		$has_primary = (int) $wpdb->get_var( "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = '{$table}' AND index_name = 'PRIMARY'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $has_primary > 0 ) {
			continue;
		}
		$cols_sql = '`' . implode( '`,`', $columns ) . '`';
		$wpdb->query( "ALTER TABLE `{$table}` ADD PRIMARY KEY ({$cols_sql})" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}

/**
 * Create attributes from attribute groups that have no attributes yet.
 *
 * @param string $prefix Table prefix including trailing underscore part.
 */
function ai_calculator_maybe_sync_group_attributes( $prefix ) {
	global $wpdb;

	$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $prefix . 'attribute' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	if ( $found !== $prefix . 'attribute' ) {
		return;
	}

	if ( ! class_exists( 'AI_Calculator_Attribute_Model' ) ) {
		require_once AI_CALCULATOR_PATH . 'admin/core/class-ai-calculator-model.php';
		require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-attribute-group-model.php';
		require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-attribute-model.php';
	}

	( new AI_Calculator_Attribute_Model() )->sync_missing_attributes_from_groups();
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

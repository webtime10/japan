<?php
/**
 * Создание таблиц БД при активации плагина (аналог OpenCart: product, category, language…).
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return void
 */
function map_plum_active_bd() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();
	$prefix          = $wpdb->prefix . 'map_plum_';

	$sql_language = "CREATE TABLE {$prefix}language (
		language_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(64) NOT NULL DEFAULT '',
		code varchar(5) NOT NULL DEFAULT '',
		locale varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		PRIMARY KEY  (language_id),
		UNIQUE KEY code (code)
	) $charset_collate;";
	dbDelta( $sql_language );

	$sql_manufacturer = "CREATE TABLE {$prefix}manufacturer (
		manufacturer_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		image varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		PRIMARY KEY  (manufacturer_id)
	) $charset_collate;";
	dbDelta( $sql_manufacturer );

	$sql_manufacturer_desc = "CREATE TABLE {$prefix}manufacturer_description (
		manufacturer_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		PRIMARY KEY  (manufacturer_id,language_id),
		KEY language_id (language_id)
	) $charset_collate;";
	dbDelta( $sql_manufacturer_desc );

	$sql_category = "CREATE TABLE {$prefix}category (
		category_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		parent_id bigint(20) unsigned NOT NULL DEFAULT 0,
		image varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (category_id),
		KEY parent_id (parent_id)
	) $charset_collate;";
	dbDelta( $sql_category );

	$sql_category_desc = "CREATE TABLE {$prefix}category_description (
		category_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		meta_title varchar(255) NOT NULL DEFAULT '',
		meta_description varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (category_id,language_id),
		KEY language_id (language_id),
		KEY name (name)
	) $charset_collate;";
	dbDelta( $sql_category_desc );

	$sql_product = "CREATE TABLE {$prefix}product (
		product_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		manufacturer_id bigint(20) unsigned NOT NULL DEFAULT 0,
		model varchar(64) NOT NULL DEFAULT '',
		sku varchar(64) NOT NULL DEFAULT '',
		image varchar(255) NOT NULL DEFAULT '',
		image_id bigint(20) unsigned NOT NULL DEFAULT 0,
		polylink varchar(255) NOT NULL DEFAULT '',
		price decimal(15,4) NOT NULL DEFAULT 0.0000,
		quantity int(11) NOT NULL DEFAULT 0,
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (product_id),
		KEY manufacturer_id (manufacturer_id),
		KEY model (model),
		KEY sku (sku)
	) $charset_collate;";
	dbDelta( $sql_product );

	$sql_product_desc = "CREATE TABLE {$prefix}product_description (
		product_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		meta_title varchar(255) NOT NULL DEFAULT '',
		meta_description varchar(255) NOT NULL DEFAULT '',
		meta_keyword varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY  (product_id,language_id),
		KEY language_id (language_id),
		KEY name (name)
	) $charset_collate;";
	dbDelta( $sql_product_desc );

	// Явная миграция: удаляем старые неиспользуемые поля и индексы, добавляем нужные.
	map_plum_migrate_product_schema( $prefix );

	$sql_product_category = "CREATE TABLE {$prefix}product_to_category (
		product_id bigint(20) unsigned NOT NULL,
		category_id bigint(20) unsigned NOT NULL,
		PRIMARY KEY  (product_id,category_id),
		KEY category_id (category_id)
	) $charset_collate;";
	dbDelta( $sql_product_category );

	map_plum_create_marker_tables( $prefix, $charset_collate );

	map_plum_active_bd_seed_languages( $prefix );
	map_plum_seed_bern_markers_from_map( $prefix );
	map_plum_seed_lucerne_markers_from_map( $prefix );
	map_plum_seed_region_products_from_maps( $prefix );
}

/**
 * Таблицы маркеров и связь с товарами.
 *
 * @param string $prefix
 * @param string $charset_collate
 */
function map_plum_create_marker_tables( $prefix, $charset_collate ) {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql_marker = "CREATE TABLE {$prefix}marker (
		marker_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		manufacturer_id bigint(20) unsigned NOT NULL DEFAULT 0,
		category_id bigint(20) unsigned NOT NULL DEFAULT 0,
		coordinates varchar(255) NOT NULL DEFAULT '',
		image varchar(255) NOT NULL DEFAULT '',
		image_id bigint(20) unsigned NOT NULL DEFAULT 0,
		polylink varchar(255) NOT NULL DEFAULT '',
		sort_order int(11) NOT NULL DEFAULT 0,
		status tinyint(1) NOT NULL DEFAULT 1,
		date_added datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		date_modified datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (marker_id),
		KEY manufacturer_id (manufacturer_id),
		KEY category_id (category_id),
		KEY sort_order (sort_order)
	) {$charset_collate};";
	dbDelta( $sql_marker );

	$sql_marker_desc = "CREATE TABLE {$prefix}marker_description (
		marker_id bigint(20) unsigned NOT NULL,
		language_id bigint(20) unsigned NOT NULL,
		name varchar(255) NOT NULL DEFAULT '',
		description longtext NOT NULL,
		arabic_name varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY (marker_id,language_id),
		KEY language_id (language_id),
		KEY name (name)
	) {$charset_collate};";
	dbDelta( $sql_marker_desc );

	$sql_product_marker = "CREATE TABLE {$prefix}product_to_marker (
		product_id bigint(20) unsigned NOT NULL,
		marker_id bigint(20) unsigned NOT NULL,
		PRIMARY KEY (product_id,marker_id),
		KEY marker_id (marker_id)
	) {$charset_collate};";
	dbDelta( $sql_product_marker );

	map_plum_migrate_marker_schema( $prefix );
}

/**
 * Начальные языки (если таблица пустая).
 *
 * @param string $prefix Префикс таблиц, например wp_map_plum_.
 * @return void
 */
function map_plum_active_bd_seed_languages( $prefix ) {
	global $wpdb;

	$table = $prefix . 'language';
	$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	if ( $count > 0 ) {
		return;
	}

	$languages = array(
		array(
			'name'       => 'English',
			'code'       => 'en',
			'locale'     => 'en-US',
			'sort_order' => 1,
			'status'     => 1,
		),
		array(
			'name'       => 'Русский',
			'code'       => 'ru',
			'locale'     => 'ru-RU',
			'sort_order' => 2,
			'status'     => 1,
		),
		array(
			'name'       => 'עברית',
			'code'       => 'he',
			'locale'     => 'he-IL',
			'sort_order' => 3,
			'status'     => 1,
		),
	);

	foreach ( $languages as $lang ) {
		$wpdb->insert( $table, $lang, array( '%s', '%s', '%s', '%d', '%d' ) );
	}
}

/**
 * Одноразово наполняет маркеры районами Берна из GeoJSON.
 *
 * @param string $prefix Префикс таблиц, например wp_map_plum_.
 * @return void
 */
function map_plum_seed_bern_markers_from_map( $prefix ) {
	map_plum_seed_markers_from_geojson(
		$prefix,
		MAP_PLUM_PATH . 'sorts/sw/bern/bern-districts.json',
		'map_plum_bern_markers_seeded'
	);
}

/**
 * Одноразово наполняет маркеры округами Люцерна из GeoJSON.
 *
 * @param string $prefix Префикс таблиц, например wp_map_plum_.
 * @return void
 */
function map_plum_seed_lucerne_markers_from_map( $prefix ) {
	map_plum_seed_markers_from_geojson(
		$prefix,
		MAP_PLUM_PATH . 'sorts/sw/lucerne/lucerne-districts.json',
		'map_plum_lucerne_markers_seeded'
	);
}

/**
 * Одноразово импортирует маркеры из GeoJSON (центр каждого v_kreis).
 *
 * @param string $prefix          Префикс таблиц, например wp_map_plum_.
 * @param string $json_path       Путь к GeoJSON.
 * @param string $seeded_option   Имя опции «уже импортировано».
 * @return void
 */
function map_plum_seed_markers_from_geojson( $prefix, $json_path, $seeded_option ) {
	global $wpdb;

	if ( (int) get_option( $seeded_option, 0 ) === 1 ) {
		return;
	}

	if ( ! is_readable( $json_path ) ) {
		return;
	}

	$geo = json_decode( (string) file_get_contents( $json_path ), true );
	if ( empty( $geo['features'] ) || ! is_array( $geo['features'] ) ) {
		return;
	}

	$languages = map_plum_get_seed_languages_for_markers();
	if ( empty( $languages ) ) {
		return;
	}

	$marker_table = $prefix . 'marker';
	$desc_table   = $prefix . 'marker_description';
	$sort_order   = 0;

	foreach ( $geo['features'] as $feature ) {
		$name = '';
		if ( isset( $feature['properties']['v_kreis'] ) ) {
			$name = (string) $feature['properties']['v_kreis'];
		}
		if ( '' === $name || empty( $feature['geometry'] ) ) {
			continue;
		}

		$center = map_plum_geojson_center( $feature['geometry'] );
		if ( empty( $center ) ) {
			continue;
		}

		$coordinates = sprintf( '%.6f, %.6f', $center['lat'], $center['lng'] );
		$marker_id   = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT marker_id FROM `{$marker_table}` WHERE coordinates = %s LIMIT 1",
				$coordinates
			)
		);

		if ( $marker_id <= 0 ) {
			$wpdb->insert(
				$marker_table,
				array(
					'coordinates' => $coordinates,
					'sort_order'  => $sort_order,
					'status'      => 1,
				),
				array( '%s', '%d', '%d' )
			);
			$marker_id = (int) $wpdb->insert_id;
		}

		if ( $marker_id <= 0 ) {
			continue;
		}

		foreach ( $languages as $language_id ) {
			$wpdb->replace(
				$desc_table,
				array(
					'marker_id'   => $marker_id,
					'language_id' => (int) $language_id,
					'name'        => $name,
				),
				array( '%d', '%d', '%s' )
			);
		}
		$sort_order++;
	}

	update_option( $seeded_option, 1 );
}

/**
 * @return array<int>
 */
function map_plum_get_seed_languages_for_markers() {
	$language_ids = array();

	if ( class_exists( 'Map_Plum_Language_Model' ) ) {
		$model     = new Map_Plum_Language_Model();
		$languages = $model->get_all_active();
		foreach ( $languages as $language ) {
			if ( empty( $language->code ) || empty( $language->language_id ) ) {
				continue;
			}
			if ( in_array( (string) $language->code, array( 'en', 'he', 'ar' ), true ) ) {
				$language_ids[] = (int) $language->language_id;
			}
		}
	}

	return array_values( array_unique( array_filter( $language_ids ) ) );
}

/**
 * Вычисляет центр GeoJSON geometry.
 *
 * @param array<string,mixed> $geometry
 * @return array<string,float>|array<empty,empty>
 */
function map_plum_geojson_center( $geometry ) {
	$type = isset( $geometry['type'] ) ? (string) $geometry['type'] : '';
	$sum_lat = 0.0;
	$sum_lng = 0.0;
	$count   = 0;

	$scan_ring = static function ( $ring ) use ( &$sum_lat, &$sum_lng, &$count ) {
		if ( ! is_array( $ring ) ) {
			return;
		}
		foreach ( $ring as $point ) {
			if ( ! is_array( $point ) || count( $point ) < 2 ) {
				continue;
			}
			$sum_lng += (float) $point[0];
			$sum_lat += (float) $point[1];
			$count++;
		}
	};

	if ( 'Polygon' === $type && ! empty( $geometry['coordinates'] ) ) {
		foreach ( $geometry['coordinates'] as $ring ) {
			$scan_ring( $ring );
		}
	}

	if ( 'MultiPolygon' === $type && ! empty( $geometry['coordinates'] ) ) {
		foreach ( $geometry['coordinates'] as $polygon ) {
			if ( ! is_array( $polygon ) ) {
				continue;
			}
			foreach ( $polygon as $ring ) {
				$scan_ring( $ring );
			}
		}
	}

	if ( $count <= 0 ) {
		return array();
	}

	return array(
		'lat' => $sum_lat / $count,
		'lng' => $sum_lng / $count,
	);
}

/**
 * Одноразово создаёт товары-области из карт Берна и Люцерна.
 *
 * @param string $prefix Префикс таблиц, например wp_map_plum_.
 * @return void
 */
function map_plum_seed_region_products_from_maps( $prefix ) {
	global $wpdb;

	if ( (int) get_option( 'map_plum_region_products_seeded', 0 ) === 1 ) {
		return;
	}

	$sources = array(
		MAP_PLUM_PATH . 'sorts/sw/bern/bern-districts.json',
		MAP_PLUM_PATH . 'sorts/sw/lucerne/lucerne-districts.json',
	);

	$region_names = array();
	foreach ( $sources as $json_path ) {
		if ( ! is_readable( $json_path ) ) {
			continue;
		}
		$geo = json_decode( (string) file_get_contents( $json_path ), true );
		if ( empty( $geo['features'] ) || ! is_array( $geo['features'] ) ) {
			continue;
		}
		foreach ( $geo['features'] as $feature ) {
			if ( ! isset( $feature['properties']['v_kreis'] ) ) {
				continue;
			}
			$name = trim( (string) $feature['properties']['v_kreis'] );
			if ( '' !== $name ) {
				$region_names[] = $name;
			}
		}
	}

	$region_names = array_values( array_unique( $region_names ) );
	if ( empty( $region_names ) ) {
		return;
	}

	$language_ids = map_plum_get_seed_languages_for_markers();
	if ( empty( $language_ids ) ) {
		return;
	}

	$product_table      = $prefix . 'product';
	$product_desc_table = $prefix . 'product_description';

	$product_columns      = $wpdb->get_col( "SHOW COLUMNS FROM `{$product_table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$product_desc_columns = $wpdb->get_col( "SHOW COLUMNS FROM `{$product_desc_table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	$sort_order = 0;
	foreach ( $region_names as $name ) {
		$exists = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT pd.product_id
				FROM `{$product_desc_table}` pd
				INNER JOIN `{$product_table}` p ON p.product_id = pd.product_id
				WHERE pd.name = %s
				LIMIT 1",
				$name
			)
		);
		if ( $exists > 0 ) {
			continue;
		}

		$product_data = array(
			'manufacturer_id' => 0,
			'image'           => '',
			'image_id'        => 0,
			'polylink'        => '',
			'price'           => 0,
			'sort_order'      => $sort_order,
			'status'          => 1,
		);
		$format_map = array(
			'manufacturer_id' => '%d',
			'image'           => '%s',
			'image_id'        => '%d',
			'polylink'        => '%s',
			'price'           => '%f',
			'sort_order'      => '%d',
			'status'          => '%d',
		);

		// Совместимость со старыми схемами.
		if ( in_array( 'model', $product_columns, true ) ) {
			$product_data['model'] = '';
			$format_map['model']   = '%s';
		}
		if ( in_array( 'sku', $product_columns, true ) ) {
			$product_data['sku'] = '';
			$format_map['sku']   = '%s';
		}
		if ( in_array( 'quantity', $product_columns, true ) ) {
			$product_data['quantity'] = 0;
			$format_map['quantity']   = '%d';
		}

		// Оставляем только существующие колонки.
		$product_data = array_intersect_key( $product_data, array_flip( $product_columns ) );
		$product_formats = array();
		foreach ( $product_data as $key => $unused ) {
			$product_formats[] = isset( $format_map[ $key ] ) ? $format_map[ $key ] : '%s';
		}
		$wpdb->insert( $product_table, $product_data, $product_formats );
		$product_id = (int) $wpdb->insert_id;
		if ( $product_id <= 0 ) {
			continue;
		}

		foreach ( $language_ids as $language_id ) {
			$desc_data = array(
				'product_id'  => $product_id,
				'language_id' => (int) $language_id,
				'name'        => $name,
				'description' => '',
			);
			$desc_format_map = array(
				'product_id'  => '%d',
				'language_id' => '%d',
				'name'        => '%s',
				'description' => '%s',
			);

			if ( in_array( 'meta_title', $product_desc_columns, true ) ) {
				$desc_data['meta_title'] = '';
				$desc_format_map['meta_title'] = '%s';
			}
			if ( in_array( 'meta_description', $product_desc_columns, true ) ) {
				$desc_data['meta_description'] = '';
				$desc_format_map['meta_description'] = '%s';
			}
			if ( in_array( 'meta_keyword', $product_desc_columns, true ) ) {
				$desc_data['meta_keyword'] = '';
				$desc_format_map['meta_keyword'] = '%s';
			}

			$desc_data = array_intersect_key( $desc_data, array_flip( $product_desc_columns ) );
			$desc_formats = array();
			foreach ( $desc_data as $key => $unused ) {
				$desc_formats[] = isset( $desc_format_map[ $key ] ) ? $desc_format_map[ $key ] : '%s';
			}
			$wpdb->insert( $product_desc_table, $desc_data, $desc_formats );
		}

		$sort_order++;
	}

	update_option( 'map_plum_region_products_seeded', 1 );
}

/**
 * Миграция таблиц product/product_description под актуальные поля карточки товара.
 *
 * @param string $prefix Префикс таблиц, например wp_map_plum_.
 * @return void
 */
function map_plum_migrate_product_schema( $prefix ) {
	global $wpdb;

	$product_table      = $prefix . 'product';
	$product_desc_table = $prefix . 'product_description';

	$product_columns      = $wpdb->get_col( "SHOW COLUMNS FROM `{$product_table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$product_desc_columns = $wpdb->get_col( "SHOW COLUMNS FROM `{$product_desc_table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	if ( ! in_array( 'image_id', $product_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_table}` ADD COLUMN `image_id` bigint(20) unsigned NOT NULL DEFAULT 0 AFTER `image`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( ! in_array( 'polylink', $product_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_table}` ADD COLUMN `polylink` varchar(255) NOT NULL DEFAULT '' AFTER `image_id`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	$product_indexes = $wpdb->get_col( "SHOW INDEX FROM `{$product_table}`", 2 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( in_array( 'model', $product_indexes, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_table}` DROP INDEX `model`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( in_array( 'sku', $product_indexes, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_table}` DROP INDEX `sku`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	if ( in_array( 'model', $product_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_table}` DROP COLUMN `model`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( in_array( 'sku', $product_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_table}` DROP COLUMN `sku`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( in_array( 'quantity', $product_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_table}` DROP COLUMN `quantity`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	if ( in_array( 'meta_title', $product_desc_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_desc_table}` DROP COLUMN `meta_title`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( in_array( 'meta_description', $product_desc_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_desc_table}` DROP COLUMN `meta_description`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( in_array( 'meta_keyword', $product_desc_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$product_desc_table}` DROP COLUMN `meta_keyword`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}

/**
 * Добавляет manufacturer_id в marker, если колонки ещё нет.
 *
 * @param string $prefix Префикс таблиц, например wp_map_plum_.
 * @return void
 */
function map_plum_migrate_marker_schema( $prefix ) {
	global $wpdb;

	$marker_table    = $prefix . 'marker';
	$marker_columns  = $wpdb->get_col( "SHOW COLUMNS FROM `{$marker_table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$marker_indexes  = $wpdb->get_col( "SHOW INDEX FROM `{$marker_table}`", 2 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	if ( ! in_array( 'manufacturer_id', $marker_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_table}` ADD COLUMN `manufacturer_id` bigint(20) unsigned NOT NULL DEFAULT 0 AFTER `marker_id`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	if ( ! in_array( 'manufacturer_id', $marker_indexes, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_table}` ADD KEY `manufacturer_id` (`manufacturer_id`)" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	if ( ! in_array( 'category_id', $marker_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_table}` ADD COLUMN `category_id` bigint(20) unsigned NOT NULL DEFAULT 0 AFTER `manufacturer_id`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$marker_columns[] = 'category_id';
	}

	$marker_indexes = $wpdb->get_col( "SHOW INDEX FROM `{$marker_table}`", 2 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! in_array( 'category_id', $marker_indexes, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_table}` ADD KEY `category_id` (`category_id`)" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	if ( ! in_array( 'image', $marker_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_table}` ADD COLUMN `image` varchar(255) NOT NULL DEFAULT '' AFTER `coordinates`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( ! in_array( 'image_id', $marker_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_table}` ADD COLUMN `image_id` bigint(20) unsigned NOT NULL DEFAULT 0 AFTER `image`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( ! in_array( 'polylink', $marker_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_table}` ADD COLUMN `polylink` varchar(255) NOT NULL DEFAULT '' AFTER `image_id`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	$marker_desc_table   = $prefix . 'marker_description';
	$marker_desc_columns = $wpdb->get_col( "SHOW COLUMNS FROM `{$marker_desc_table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( ! in_array( 'description', $marker_desc_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_desc_table}` ADD COLUMN `description` longtext NOT NULL AFTER `name`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( ! in_array( 'arabic_name', $marker_desc_columns, true ) ) {
		$wpdb->query( "ALTER TABLE `{$marker_desc_table}` ADD COLUMN `arabic_name` varchar(255) NOT NULL DEFAULT '' AFTER `description`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}

<?php
/**
 * Категории и маркеры из БД для фронтенд-карт.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return int
 */
function map_plum_get_front_language_id() {
	if ( function_exists( 'pll_current_language' ) ) {
		$slug = pll_current_language( 'slug' );
		if ( $slug && class_exists( 'Map_Plum_Language_Model' ) ) {
			$row = ( new Map_Plum_Language_Model() )->get_by_code( (string) $slug );
			if ( $row && ! empty( $row->language_id ) ) {
				return (int) $row->language_id;
			}
		}
	}

	if ( class_exists( 'Map_Plum_Language_Model' ) ) {
		return (new Map_Plum_Language_Model())->get_default_language_id();
	}

	return 1;
}

/**
 * @param string $slug
 * @return array<int>
 */
function map_plum_map_manufacturer_ids_for_slug( $slug ) {
	static $cache = array();

	$slug = strtolower( (string) $slug );
	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	$slug_norm = map_plum_normalize_canton_slug( $slug );
	$meta      = map_plum_get_canton_meta( $slug_norm );
	$needles   = ( $meta && ! empty( $meta['manufacturer_needles'] ) ) ? $meta['manufacturer_needles'] : array( $slug_norm );

	if ( empty( $needles ) ) {
		$cache[ $slug ] = array();
		return $cache[ $slug ];
	}

	global $wpdb;
	$prefix     = $wpdb->prefix . 'map_plum_';
	$like_parts = array();
	foreach ( $needles as $needle ) {
		$like_parts[] = $wpdb->prepare( 'LOWER(md.name) LIKE %s', '%' . strtolower( $needle ) . '%' );
	}

	$where = implode( ' OR ', $like_parts );
	$sql   = "SELECT DISTINCT m.manufacturer_id
		FROM `{$prefix}manufacturer` m
		INNER JOIN `{$prefix}manufacturer_description` md ON m.manufacturer_id = md.manufacturer_id
		WHERE ({$where})";

	$ids            = $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$ids            = array_values( array_filter( array_map( 'intval', (array) $ids ) ) );

	if ( empty( $ids ) && $meta && ! empty( $meta['bbox'] ) && is_array( $meta['bbox'] ) ) {
		$bbox = array_map( 'floatval', $meta['bbox'] );
		if ( count( $bbox ) >= 4 ) {
			$marker_table = $prefix . 'marker';
			$ids          = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT manufacturer_id
					FROM `{$marker_table}`
					WHERE status = 1
						AND manufacturer_id > 0
						AND CAST(TRIM(SUBSTRING_INDEX(coordinates, ',', 1)) AS DECIMAL(10,6)) BETWEEN %f AND %f
						AND CAST(TRIM(SUBSTRING_INDEX(coordinates, ',', -1)) AS DECIMAL(10,6)) BETWEEN %f AND %f",
					min( $bbox[0], $bbox[2] ),
					max( $bbox[0], $bbox[2] ),
					min( $bbox[1], $bbox[3] ),
					max( $bbox[1], $bbox[3] )
				)
			);
			$ids          = array_values( array_filter( array_map( 'intval', (array) $ids ) ) );
		}
	}

	$cache[ $slug ] = $ids;

	return $cache[ $slug ];
}

/**
 * @param string $coordinates
 * @return array{lat: float, lng: float}|null
 */
function map_plum_parse_marker_coordinates( $coordinates ) {
	$parts = array_map( 'trim', explode( ',', (string) $coordinates ) );
	if ( count( $parts ) < 2 ) {
		return null;
	}

	$lat = (float) $parts[0];
	$lng = (float) $parts[1];
	if ( 0.0 === $lat && 0.0 === $lng ) {
		return null;
	}

	return array(
		'lat' => $lat,
		'lng' => $lng,
	);
}

/**
 * @param object $row
 * @return string
 */
function map_plum_resolve_marker_image_url( $row ) {
	if ( ! empty( $row->image_id ) ) {
		$url = wp_get_attachment_image_url( (int) $row->image_id, 'large' );
		if ( $url ) {
			return esc_url( $url );
		}
	}

	if ( empty( $row->image ) ) {
		return '';
	}

	$image = trim( (string) $row->image );
	if ( '' === $image ) {
		return '';
	}

	if ( preg_match( '#^https?://#i', $image ) ) {
		return esc_url( $image );
	}

	if ( 0 === strpos( $image, '//' ) ) {
		return esc_url( 'https:' . $image );
	}

	if ( 0 === strpos( $image, '/' ) ) {
		return esc_url( home_url( $image ) );
	}

	return esc_url( $image );
}

/**
 * @param object $row
 * @return array<string, mixed>
 */
function map_plum_format_marker_poi_row( $row ) {
	$coords = map_plum_parse_marker_coordinates( $row->coordinates );
	if ( ! $coords ) {
		return array();
	}

	$link = ! empty( $row->polylink ) ? trim( (string) $row->polylink ) : '';

	return array(
		'id'          => (int) $row->marker_id,
		'lat'         => $coords['lat'],
		'lng'         => $coords['lng'],
		'title'       => ! empty( $row->arabic_name ) ? (string) $row->arabic_name : ( ! empty( $row->name ) ? (string) $row->name : '' ),
		'description' => ! empty( $row->description ) ? (string) $row->description : '',
		'photo'       => map_plum_resolve_marker_image_url( $row ),
		'link'        => $link ? esc_url( $link ) : '',
	);
}

/**
 * Категории, у которых есть маркеры с выбранной категорией на этой карте.
 *
 * @param string   $slug
 * @param int|null $language_id
 * @return array<int, array{id: int, name: string}>
 */
function map_plum_get_map_categories_for_front( $slug, $language_id = null ) {
	global $wpdb;

	$manufacturer_ids = map_plum_map_manufacturer_ids_for_slug( $slug );
	if ( empty( $manufacturer_ids ) ) {
		return array();
	}

	if ( null === $language_id ) {
		$language_id = map_plum_get_front_language_id();
	}

	$prefix           = $wpdb->prefix . 'map_plum_';
	$cat_desc         = $prefix . 'category_description';
	$manufacturer_sql = implode( ',', array_map( 'intval', $manufacturer_ids ) );

	$sql = "SELECT DISTINCT c.category_id,
			SUBSTRING_INDEX(
				GROUP_CONCAT(COALESCE(NULLIF(cd.description, ''), cd.name) ORDER BY (cd.language_id = %d) DESC, cd.language_id ASC SEPARATOR '\n'),
				'\n',
				1
			) AS name,
			c.sort_order
		FROM `{$prefix}category` c
		INNER JOIN `{$cat_desc}` cd ON c.category_id = cd.category_id
		INNER JOIN `{$prefix}marker` m ON m.category_id = c.category_id
		WHERE c.status = 1 AND m.status = 1 AND m.category_id > 0 AND m.manufacturer_id IN ({$manufacturer_sql})
		GROUP BY c.category_id
		ORDER BY c.sort_order ASC, name ASC";

	$rows = $wpdb->get_results( $wpdb->prepare( $sql, $language_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	$categories = array();
	foreach ( (array) $rows as $row ) {
		$categories[] = array(
			'id'   => (int) $row->category_id,
			'name' => ! empty( $row->name ) ? (string) $row->name : 'Категория #' . (int) $row->category_id,
		);
	}

	return $categories;
}

/**
 * Маркеры по category_id для карты (категория выбрана в карточке маркера).
 *
 * @param string   $slug
 * @param int|null $language_id
 * @return array<string, array<int, array<string, mixed>>>
 */
function map_plum_get_markers_by_category_for_map( $slug, $language_id = null ) {
	global $wpdb;

	$manufacturer_ids = map_plum_map_manufacturer_ids_for_slug( $slug );
	if ( empty( $manufacturer_ids ) ) {
		return array();
	}

	if ( null === $language_id ) {
		$language_id = map_plum_get_front_language_id();
	}

	$prefix           = $wpdb->prefix . 'map_plum_';
	$marker_desc      = $prefix . 'marker_description';
	$manufacturer_sql = implode( ',', array_map( 'intval', $manufacturer_ids ) );

	$sql = "SELECT m.marker_id, m.coordinates, m.image, m.image_id, m.polylink, m.category_id,
			SUBSTRING_INDEX(
				GROUP_CONCAT(md.name ORDER BY (md.language_id = %d) DESC, md.language_id ASC SEPARATOR '\n'),
				'\n',
				1
			) AS name,
			SUBSTRING_INDEX(
				GROUP_CONCAT(md.description ORDER BY (md.language_id = %d) DESC, md.language_id ASC SEPARATOR '\n'),
				'\n',
				1
			) AS description,
			SUBSTRING_INDEX(
				GROUP_CONCAT(md.arabic_name ORDER BY (md.language_id = %d) DESC, md.language_id ASC SEPARATOR '\n'),
				'\n',
				1
			) AS arabic_name
		FROM `{$prefix}marker` m
		LEFT JOIN `{$marker_desc}` md ON m.marker_id = md.marker_id
		WHERE m.status = 1 AND m.category_id > 0 AND m.manufacturer_id IN ({$manufacturer_sql})
		GROUP BY m.marker_id, m.category_id";

	$rows   = $wpdb->get_results( $wpdb->prepare( $sql, $language_id, $language_id, $language_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$by_cat = array();

	foreach ( (array) $rows as $row ) {
		$cat_id = (int) $row->category_id;
		if ( $cat_id <= 0 ) {
			continue;
		}

		$poi = map_plum_format_marker_poi_row( $row );
		if ( empty( $poi ) ) {
			continue;
		}

		$key = (string) $cat_id;
		if ( ! isset( $by_cat[ $key ] ) ) {
			$by_cat[ $key ] = array();
		}

		$by_cat[ $key ][] = $poi;
	}

	return $by_cat;
}

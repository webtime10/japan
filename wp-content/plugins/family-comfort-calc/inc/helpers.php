<?php
/**
 * Helpers.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function fcc_get_group_types() {
	return array(
		'age'       => __( 'Возраст детей', 'family-comfort-calc' ),
		'interest'  => __( 'Интересы', 'family-comfort-calc' ),
		'direction' => __( 'Направления', 'family-comfort-calc' ),
	);
}

/**
 * @param string $group
 * @return bool
 */
function fcc_is_valid_group( $group ) {
	return isset( fcc_get_group_types()[ $group ] );
}

/**
 * Decode JSON list of IDs or place tags from DB.
 *
 * @param mixed $raw
 * @param bool  $as_tags If true, sanitize as place tags; else as int IDs.
 * @return array
 */
function fcc_decode_id_list( $raw, $as_tags = false ) {
	if ( is_array( $raw ) ) {
		$decoded = $raw;
	} else {
		$decoded = json_decode( (string) $raw, true );
		if ( ! is_array( $decoded ) ) {
			$decoded = array();
		}
	}

	if ( $as_tags ) {
		return fcc_sanitize_page_tags( $decoded );
	}

	$out = array();
	foreach ( $decoded as $item ) {
		$id = (int) $item;
		if ( $id > 0 && ! in_array( $id, $out, true ) ) {
			$out[] = $id;
		}
	}
	return $out;
}

/**
 * @return array<int, object>
 */
function fcc_get_categories( $group ) {
	if ( ! fcc_is_valid_group( $group ) ) {
		return array();
	}

	require_once FCC_PATH . 'admin/models/class-fcc-category-model.php';
	$model = new FCC_Category_Model( $group );
	return $model->get_list( fcc_get_default_language_id() );
}

/**
 * Один язык на сайте — всегда ID 1.
 *
 * @return int
 */
function fcc_get_default_language_id() {
	return 1;
}

/**
 * Map DB page rows to search result items.
 *
 * @param array<int, object> $rows
 * @return array<int, array{id:int,title:string,url:string,image:string}>
 */
function fcc_map_page_search_rows( $rows ) {
	$out = array();
	if ( ! is_array( $rows ) ) {
		return $out;
	}

	foreach ( $rows as $row ) {
		$id = (int) $row->ID;
		$image = get_the_post_thumbnail_url( $id, 'medium' );
		if ( ! $image ) {
			$image = get_the_post_thumbnail_url( $id, 'thumbnail' );
		}
		if ( ! $image ) {
			$image = '';
		}

		$out[] = array(
			'id'    => $id,
			'title' => (string) $row->post_title,
			'url'   => (string) get_permalink( $id ),
			'image' => (string) $image,
		);
	}

	return $out;
}

/**
 * Minimum characters for page title search.
 *
 * @return int
 */
function fcc_page_search_min_chars() {
	return 3;
}

/**
 * Search published pages with the default template (page.php / Default).
 *
 * @param string $term
 * @param int    $limit
 * @return array<int, array{id:int,title:string,url:string,image:string}>
 */
function fcc_search_default_pages( $term, $limit = 20 ) {
	global $wpdb;

	$term  = trim( (string) $term );
	$limit = max( 1, min( 50, (int) $limit ) );

	if ( strlen( $term ) < fcc_page_search_min_chars() ) {
		return array();
	}

	$like = '%' . $wpdb->esc_like( $term ) . '%';

	// Default template: no _wp_page_template meta, or value '' / 'default'.
	$sql = $wpdb->prepare(
		"SELECT p.ID, p.post_title
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->postmeta} pm
			ON p.ID = pm.post_id
			AND pm.meta_key = '_wp_page_template'
		WHERE p.post_type = 'page'
			AND p.post_status = 'publish'
			AND p.post_title LIKE %s
			AND ( pm.meta_id IS NULL OR pm.meta_value = '' OR pm.meta_value = 'default' )
		ORDER BY p.post_title ASC
		LIMIT %d",
		$like,
		$limit
	);

	$rows = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return fcc_map_page_search_rows( is_array( $rows ) ? $rows : array() );
}

/**
 * Alias: city page picker uses the same default-template search.
 *
 * @param string $term
 * @param int    $limit
 * @return array<int, array{id:int,title:string,url:string,image:string}>
 */
function fcc_search_city_pages( $term, $limit = 20 ) {
	return fcc_search_default_pages( $term, $limit );
}

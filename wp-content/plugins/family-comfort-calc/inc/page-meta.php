<?php
/**
 * Page meta: привязка категорий Family Comfort к странице (post type page).
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function fcc_get_page_meta_keys() {
	return array(
		'age'       => '_fcc_page_age_id',
		'interest'  => '_fcc_page_interest_id',
		'direction' => '_fcc_page_direction_id',
	);
}

/**
 * @param int $post_id
 * @return array{age: array<int>, interest: array<int>, direction: array<int>}
 */
function fcc_get_page_category_ids( $post_id = 0 ) {
	$post_id = $post_id > 0 ? (int) $post_id : (int) get_the_ID();
	$out     = array(
		'age'       => array(),
		'interest'  => array(),
		'direction' => array(),
	);

	if ( $post_id <= 0 ) {
		return $out;
	}

	foreach ( fcc_get_page_meta_keys() as $group => $meta_key ) {
		$out[ $group ] = fcc_normalize_page_category_ids( get_post_meta( $post_id, $meta_key, true ) );
	}

	return $out;
}

/**
 * Поддержка старого формата: одно число → массив из одного ID.
 *
 * @param mixed $raw
 * @return array<int>
 */
function fcc_normalize_page_category_ids( $raw ) {
	if ( is_array( $raw ) ) {
		$ids = array_map( 'intval', $raw );
		$ids = array_values( array_unique( array_filter( $ids ) ) );
		return $ids;
	}

	if ( is_numeric( $raw ) ) {
		$id = (int) $raw;
		return $id > 0 ? array( $id ) : array();
	}

	return array();
}

/**
 * @param string     $group
 * @param array<int> $category_ids
 * @return array<int>
 */
function fcc_sanitize_page_category_ids( $group, $category_ids ) {
	if ( ! fcc_is_valid_group( $group ) || ! is_array( $category_ids ) ) {
		return array();
	}

	$out = array();
	foreach ( $category_ids as $raw_id ) {
		$id = (int) $raw_id;
		if ( $id <= 0 || in_array( $id, $out, true ) ) {
			continue;
		}
		$cat = fcc_get_category( $group, $id );
		if ( $cat ) {
			$out[] = (int) $cat->category_id;
		}
	}

	return $out;
}

/**
 * @param int $post_id
 * @return array<string, array<int, object>>
 */
function fcc_get_page_categories( $post_id = 0 ) {
	$ids = fcc_get_page_category_ids( $post_id );
	$out = array();

	foreach ( $ids as $group => $category_ids ) {
		$out[ $group ] = array();
		foreach ( $category_ids as $category_id ) {
			$cat = fcc_get_category( $group, $category_id );
			if ( $cat ) {
				$out[ $group ][] = $cat;
			}
		}
	}

	return $out;
}

/**
 * @param string $group
 * @param int    $category_id
 * @return object|null
 */
function fcc_get_category( $group, $category_id ) {
	if ( ! fcc_is_valid_group( $group ) || $category_id <= 0 ) {
		return null;
	}

	require_once FCC_PATH . 'admin/models/class-fcc-category-model.php';
	$model = new FCC_Category_Model( $group );
	$row   = $model->get( (int) $category_id );

	if ( ! $row || (int) $row->status !== 1 ) {
		return null;
	}

	$descriptions = $model->get_descriptions( (int) $category_id );
	$lang_id      = fcc_get_default_language_id();

	if ( isset( $descriptions[ $lang_id ] ) ) {
		$row->name        = $descriptions[ $lang_id ]->name;
		$row->description = $descriptions[ $lang_id ]->description;
	}

	return $row;
}

/**
 * @return string
 */
function fcc_get_page_tags_meta_key() {
	return '_fcc_page_tags';
}

/**
 * @return int
 */
function fcc_get_page_tags_max() {
	return 10;
}

/**
 * @param mixed $tags
 * @return array<int, array{label: string, url: string}>
 */
function fcc_sanitize_page_tags( $tags ) {
	if ( ! is_array( $tags ) ) {
		return array();
	}

	$out = array();
	$max = fcc_get_page_tags_max();

	foreach ( $tags as $tag ) {
		if ( count( $out ) >= $max ) {
			break;
		}

		if ( ! is_array( $tag ) ) {
			continue;
		}

		$label = isset( $tag['label'] ) ? sanitize_text_field( (string) $tag['label'] ) : '';
		$url   = isset( $tag['url'] ) ? esc_url_raw( (string) $tag['url'] ) : '';

		if ( '' === $label ) {
			continue;
		}

		$out[] = array(
			'label' => $label,
			'url'   => $url,
		);
	}

	return $out;
}

/**
 * @param int $post_id
 * @return array<int, array{label: string, url: string}>
 */
function fcc_get_page_tags( $post_id = 0 ) {
	$post_id = $post_id > 0 ? (int) $post_id : (int) get_the_ID();
	if ( $post_id <= 0 ) {
		return array();
	}

	$raw = get_post_meta( $post_id, fcc_get_page_tags_meta_key(), true );
	if ( ! is_array( $raw ) ) {
		return array();
	}

	return fcc_sanitize_page_tags( $raw );
}

/**
 * @return string
 */
function fcc_get_page_image_meta_key() {
	return '_fcc_page_image';
}

/**
 * @return string
 */
function fcc_get_page_status_meta_key() {
	return '_fcc_page_status';
}

/**
 * @param int $post_id
 * @return string
 */
function fcc_get_page_image( $post_id = 0 ) {
	$post_id = $post_id > 0 ? (int) $post_id : (int) get_the_ID();
	if ( $post_id <= 0 ) {
		return '';
	}

	return esc_url( (string) get_post_meta( $post_id, fcc_get_page_image_meta_key(), true ) );
}

/**
 * @param int $post_id
 * @return bool
 */
function fcc_is_page_card_enabled( $post_id = 0 ) {
	$post_id = $post_id > 0 ? (int) $post_id : (int) get_the_ID();
	if ( $post_id <= 0 ) {
		return true;
	}

	$status = get_post_meta( $post_id, fcc_get_page_status_meta_key(), true );

	if ( '' === $status || false === $status ) {
		return true;
	}

	return (int) $status === 1;
}

/**
 * Названия направлений (городов) для карточки.
 *
 * @param int $post_id
 * @return array<int, string>
 */
function fcc_get_page_direction_names( $post_id = 0 ) {
	$categories = fcc_get_page_categories( $post_id );
	$names      = array();

	if ( empty( $categories['direction'] ) ) {
		return $names;
	}

	foreach ( $categories['direction'] as $cat ) {
		$name = isset( $cat->name ) ? trim( (string) $cat->name ) : '';
		if ( '' !== $name ) {
			$names[] = $name;
		}
	}

	return $names;
}

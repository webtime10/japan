<?php
/**
 * Данные для фронтового калькулятора направлений.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array<string, mixed>>
 */
function fcc_get_direction_cards_data() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);

	$cards = array();

	foreach ( $pages as $page ) {
		if ( ! fcc_is_page_card_enabled( (int) $page->ID ) ) {
			continue;
		}

		$category_ids = fcc_get_page_category_ids( (int) $page->ID );
		$directions   = fcc_get_page_categories( (int) $page->ID )['direction'] ?? array();

		if ( empty( $directions ) ) {
			continue;
		}

		$tags  = fcc_get_page_tags( (int) $page->ID );
		$image = fcc_get_page_image( (int) $page->ID );
		$url   = get_permalink( $page );

		foreach ( $directions as $direction ) {
			$city = ! empty( $direction->name ) ? (string) $direction->name : '';
			if ( '' === $city ) {
				continue;
			}

			$cards[] = array(
				'page_id'      => (int) $page->ID,
				'direction_id' => (int) $direction->category_id,
				'title'        => $city,
				'url'          => is_string( $url ) ? $url : '',
				'image'        => $image,
				'tags'         => $tags,
				'age_ids'      => $category_ids['age'],
				'interest_ids' => $category_ids['interest'],
				'rating'       => 4.5,
			);
		}
	}

	return $cards;
}

/**
 * @return array<int, string>
 */
function fcc_get_select_options( $group ) {
	$options = array();
	foreach ( fcc_get_categories( $group ) as $cat ) {
		if ( (int) $cat->status !== 1 ) {
			continue;
		}
		$id = (int) $cat->category_id;
		$options[ $id ] = $cat->name ? (string) $cat->name : '#' . $id;
	}
	return $options;
}

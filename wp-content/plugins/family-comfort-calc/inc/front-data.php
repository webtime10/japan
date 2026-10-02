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
 * Рейтинг карточки: цикл 5.0, 5.0, 4.8, 4.8, 4.7.
 *
 * @param int $index порядковый индекс карточки (0-based)
 * @return float
 */
function fcc_get_card_rating( $index = 0 ) {
	$pool  = array( 5.0, 5.0, 4.8, 4.8, 4.7 );
	$index = abs( (int) $index );
	return (float) $pool[ $index % count( $pool ) ];
}

/**
 * Карточки из постов плагина. Теги — только со WP-страниц (старые ручные places игнорируются).
 *
 * @return array<int, array<string, mixed>>
 */
function fcc_get_direction_cards_data() {
	require_once FCC_PATH . 'admin/models/class-fcc-post-model.php';

	$model   = new FCC_Post_Model();
	$rows    = $model->get_enabled_for_front( fcc_get_default_language_id() );
	$from_wp = fcc_get_wp_page_contributions_by_direction();
	$cards   = array();

	foreach ( $rows as $row ) {
		$direction_id = (int) $row->direction_id;
		$post_title   = ! empty( $row->post_name ) ? (string) $row->post_name : '';
		if ( $direction_id <= 0 || '' === $post_title ) {
			continue;
		}

		$post_id = (int) $row->post_id;
		$image   = ! empty( $row->image ) ? (string) $row->image : '';
		$url     = ! empty( $row->url ) ? (string) $row->url : '';

		$normalized_tags = array();
		if ( ! empty( $from_wp[ $direction_id ]['tags'] ) && is_array( $from_wp[ $direction_id ]['tags'] ) ) {
			foreach ( $from_wp[ $direction_id ]['tags'] as $tag ) {
				$label = isset( $tag['label'] ) ? (string) $tag['label'] : '';
				if ( '' === $label ) {
					continue;
				}
				$normalized_tags[] = array(
					'label'        => $label,
					'url'          => isset( $tag['url'] ) ? (string) $tag['url'] : '',
					'age_ids'      => isset( $tag['age_ids'] ) && is_array( $tag['age_ids'] )
						? array_values( array_unique( array_filter( array_map( 'intval', $tag['age_ids'] ) ) ) )
						: array(),
					'interest_ids' => isset( $tag['interest_ids'] ) && is_array( $tag['interest_ids'] )
						? array_values( array_unique( array_filter( array_map( 'intval', $tag['interest_ids'] ) ) ) )
						: array(),
				);
			}
		}

		$age_ids      = array();
		$interest_ids = array();
		foreach ( $normalized_tags as $tag ) {
			foreach ( $tag['age_ids'] as $age_id ) {
				if ( ! in_array( $age_id, $age_ids, true ) ) {
					$age_ids[] = $age_id;
				}
			}
			foreach ( $tag['interest_ids'] as $interest_id ) {
				if ( ! in_array( $interest_id, $interest_ids, true ) ) {
					$interest_ids[] = $interest_id;
				}
			}
		}

		$cards[] = array(
			'page_id'      => $post_id,
			'post_type'    => 'fcc_internal_post',
			'direction_id' => $direction_id,
			'title'        => $post_title,
			'url'          => $url,
			'image'        => $image,
			'tags'         => $normalized_tags,
			'age_ids'      => $age_ids,
			'interest_ids' => $interest_ids,
			'rating'       => fcc_get_card_rating( count( $cards ) ),
		);
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

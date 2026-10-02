<?php
/**
 * Shared helpers formerly used by page meta (still used by post form).
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
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

	if ( ! $row ) {
		return null;
	}

	$lang_id      = fcc_get_default_language_id();
	$descriptions = $model->get_descriptions( (int) $category_id );
	$name         = '';

	if ( isset( $descriptions[ $lang_id ] ) ) {
		$name = (string) $descriptions[ $lang_id ]->name;
	}

	$row->name = $name;
	return $row;
}

/**
 * Max attraction tags per plugin post.
 *
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
 * Meta keys for WP page → Family Comfort binding.
 *
 * @return array<string, string>
 */
function fcc_get_wp_page_meta_keys() {
	return array(
		'age'       => '_fcc_wp_page_age_ids',
		'interest'  => '_fcc_wp_page_interest_ids',
		'direction' => '_fcc_wp_page_direction_id',
	);
}

/**
 * @return string
 */
function fcc_get_wp_page_auto_tag_meta_key() {
	return '_fcc_wp_page_auto_tag';
}

/**
 * @param int $post_id
 * @return array{age: array<int>, interest: array<int>, direction: array<int>}
 */
function fcc_get_wp_page_category_ids( $post_id = 0 ) {
	$post_id = $post_id > 0 ? (int) $post_id : (int) get_the_ID();
	$out     = array(
		'age'       => array(),
		'interest'  => array(),
		'direction' => array(),
	);

	if ( $post_id <= 0 ) {
		return $out;
	}

	foreach ( fcc_get_wp_page_meta_keys() as $group => $meta_key ) {
		$out[ $group ] = fcc_normalize_page_category_ids( get_post_meta( $post_id, $meta_key, true ) );
	}

	return $out;
}

/**
 * @param int $post_id
 * @return string
 */
function fcc_get_wp_page_auto_tag( $post_id = 0 ) {
	$post_id = $post_id > 0 ? (int) $post_id : (int) get_the_ID();
	if ( $post_id <= 0 ) {
		return '';
	}

	return sanitize_text_field( (string) get_post_meta( $post_id, fcc_get_wp_page_auto_tag_meta_key(), true ) );
}

/**
 * @param array{age: array<int>, interest: array<int>, direction: array<int>} $category_ids
 * @return bool
 */
function fcc_wp_page_has_full_selection( $category_ids ) {
	return ! empty( $category_ids['direction'] )
		&& ! empty( $category_ids['age'] )
		&& ! empty( $category_ids['interest'] );
}

/**
 * @param string $a
 * @param string $b
 * @return bool
 */
function fcc_tag_label_equals( $a, $b ) {
	$a = (string) $a;
	$b = (string) $b;
	if ( function_exists( 'mb_strtolower' ) ) {
		return mb_strtolower( $a, 'UTF-8' ) === mb_strtolower( $b, 'UTF-8' );
	}
	return strtolower( $a ) === strtolower( $b );
}

/**
 * Тег со страницы (если полный выбор + название тега).
 *
 * @param int $post_id
 * @return array{label: string, url: string, age_ids: array<int>, interest_ids: array<int>, direction_id: int}|null
 */
function fcc_get_wp_page_contribution( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return null;
	}

	$page = get_post( $post_id );
	if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
		return null;
	}

	$category_ids = fcc_get_wp_page_category_ids( $post_id );
	if ( ! fcc_wp_page_has_full_selection( $category_ids ) ) {
		return null;
	}

	$auto = fcc_get_wp_page_auto_tag( $post_id );
	if ( '' === $auto ) {
		return null;
	}

	$url = get_permalink( $post_id );
	$url = is_string( $url ) ? $url : '';

	return array(
		'label'         => $auto,
		'url'           => $url,
		'age_ids'       => $category_ids['age'],
		'interest_ids'  => $category_ids['interest'],
		'direction_id'  => (int) $category_ids['direction'][0],
	);
}

/**
 * Все вкладки со страниц, сгруппированные по городу (direction_id).
 *
 * @return array<int, array{tags: array<int, array{label: string, url: string}>, age_ids: array<int>, interest_ids: array<int>}>
 */
function fcc_get_wp_page_contributions_by_direction() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => fcc_get_wp_page_auto_tag_meta_key(),
					'compare' => 'EXISTS',
				),
			),
		)
	);

	$by_city = array();

	foreach ( $pages as $page_id ) {
		$contrib = fcc_get_wp_page_contribution( (int) $page_id );
		if ( ! $contrib ) {
			continue;
		}

		$city_id = (int) $contrib['direction_id'];
		if ( $city_id <= 0 ) {
			continue;
		}

		if ( ! isset( $by_city[ $city_id ] ) ) {
			$by_city[ $city_id ] = array(
				'tags'         => array(),
				'age_ids'      => array(),
				'interest_ids' => array(),
			);
		}

		$dup = false;
		foreach ( $by_city[ $city_id ]['tags'] as $existing ) {
			if ( fcc_tag_label_equals( $existing['label'], $contrib['label'] ) ) {
				$dup = true;
				break;
			}
		}
		if ( ! $dup ) {
			$by_city[ $city_id ]['tags'][] = array(
				'label'        => $contrib['label'],
				'url'          => $contrib['url'],
				'age_ids'      => $contrib['age_ids'],
				'interest_ids' => $contrib['interest_ids'],
				'page_id'      => (int) $page_id,
			);
		}

		foreach ( $contrib['age_ids'] as $age_id ) {
			$age_id = (int) $age_id;
			if ( $age_id > 0 && ! in_array( $age_id, $by_city[ $city_id ]['age_ids'], true ) ) {
				$by_city[ $city_id ]['age_ids'][] = $age_id;
			}
		}
		foreach ( $contrib['interest_ids'] as $interest_id ) {
			$interest_id = (int) $interest_id;
			if ( $interest_id > 0 && ! in_array( $interest_id, $by_city[ $city_id ]['interest_ids'], true ) ) {
				$by_city[ $city_id ]['interest_ids'][] = $interest_id;
			}
		}
	}

	return $by_city;
}

/**
 * Найти direction_id по названию категории (город).
 *
 * @param string $name
 * @return int
 */
function fcc_find_direction_id_by_name( $name ) {
	$name = trim( (string) $name );
	if ( '' === $name ) {
		return 0;
	}

	foreach ( fcc_get_categories( 'direction' ) as $cat ) {
		$cat_name = isset( $cat->name ) ? trim( (string) $cat->name ) : '';
		if ( '' === $cat_name ) {
			continue;
		}
		if ( fcc_tag_label_equals( $cat_name, $name ) ) {
			return (int) $cat->category_id;
		}
	}

	return 0;
}

/**
 * @param string     $group
 * @param array<int> $ids
 * @return array<int, string>
 */
function fcc_get_category_names_by_ids( $group, $ids ) {
	$names = array();
	if ( ! is_array( $ids ) ) {
		return $names;
	}
	foreach ( $ids as $id ) {
		$id  = (int) $id;
		$cat = fcc_get_category( $group, $id );
		if ( $cat && ! empty( $cat->name ) ) {
			$names[] = (string) $cat->name;
		}
	}
	return $names;
}

/**
 * Теги со WP-страниц для города (direction) — для админки поста.
 *
 * @param int $direction_id
 * @return array<int, array{label: string, url: string, age_ids: array<int>, interest_ids: array<int>, age_labels: array<int, string>, interest_labels: array<int, string>, page_id: int}>
 */
function fcc_get_wp_page_tags_for_direction( $direction_id ) {
	$direction_id = (int) $direction_id;
	if ( $direction_id <= 0 ) {
		return array();
	}

	$all = fcc_get_wp_page_contributions_by_direction();
	if ( empty( $all[ $direction_id ]['tags'] ) ) {
		return array();
	}

	$out = array();
	foreach ( $all[ $direction_id ]['tags'] as $tag ) {
		$age_ids      = isset( $tag['age_ids'] ) && is_array( $tag['age_ids'] ) ? $tag['age_ids'] : array();
		$interest_ids = isset( $tag['interest_ids'] ) && is_array( $tag['interest_ids'] ) ? $tag['interest_ids'] : array();
		$out[]        = array(
			'label'           => (string) $tag['label'],
			'url'             => isset( $tag['url'] ) ? (string) $tag['url'] : '',
			'age_ids'         => $age_ids,
			'interest_ids'    => $interest_ids,
			'age_labels'      => fcc_get_category_names_by_ids( 'age', $age_ids ),
			'interest_labels' => fcc_get_category_names_by_ids( 'interest', $interest_ids ),
			'page_id'         => isset( $tag['page_id'] ) ? (int) $tag['page_id'] : 0,
		);
	}

	return $out;
}

/**
 * HTML карточек автотегов (админка).
 *
 * @param array $tags
 * @return string
 */
function fcc_render_auto_tags_html( $tags ) {
	if ( empty( $tags ) || ! is_array( $tags ) ) {
		return '<p class="description fcc-auto-tags-empty">' . esc_html__( 'Пока нет тегов. Назначьте на WP-странице: город + возраст + интересы + название тега.', 'family-comfort-calc' ) . '</p>';
	}

	ob_start();
	echo '<div class="fcc-auto-tags-list">';
	foreach ( $tags as $tag ) {
		$label = isset( $tag['label'] ) ? (string) $tag['label'] : '';
		$url   = isset( $tag['url'] ) ? (string) $tag['url'] : '';
		$ages  = isset( $tag['age_labels'] ) && is_array( $tag['age_labels'] ) ? $tag['age_labels'] : array();
		$ints  = isset( $tag['interest_labels'] ) && is_array( $tag['interest_labels'] ) ? $tag['interest_labels'] : array();

		echo '<div class="fcc-auto-tag">';
		echo '<div class="fcc-auto-tag__title">' . esc_html( $label ) . '</div>';
		if ( '' !== $url ) {
			echo '<div class="fcc-auto-tag__url"><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $url ) . '</a></div>';
		}
		echo '<div class="fcc-auto-tag__params">';
		echo '<span><strong>' . esc_html__( 'Возраст:', 'family-comfort-calc' ) . '</strong> ' . esc_html( $ages ? implode( ', ', $ages ) : '—' ) . '</span>';
		echo '<span><strong>' . esc_html__( 'Интересы:', 'family-comfort-calc' ) . '</strong> ' . esc_html( $ints ? implode( ', ', $ints ) : '—' ) . '</span>';
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';
	return (string) ob_get_clean();
}

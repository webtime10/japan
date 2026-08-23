<?php
/**
 * Реестр префектур Японии для карт Map Plum.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, array<string, mixed>>
 */
function map_plum_cantons_registry() {
	static $registry = null;
	if ( null !== $registry ) {
		return $registry;
	}

	$generated = MAP_PLUM_PATH . 'inc/map-plum-japan-registry.generated.php';
	if ( is_readable( $generated ) ) {
		$data = require $generated;
		if ( is_array( $data ) ) {
			$registry = $data;
			return $registry;
		}
	}

	$registry = array();
	return $registry;
}

/**
 * @param string $slug
 * @return string
 */
function map_plum_normalize_canton_slug( $slug ) {
	$slug = strtolower( sanitize_title( (string) $slug ) );
	$slug = str_replace( '_', '-', $slug );

	foreach ( map_plum_cantons_registry() as $canonical => $meta ) {
		if ( $canonical === $slug ) {
			return $canonical;
		}
		if ( ! empty( $meta['aliases'] ) && in_array( $slug, $meta['aliases'], true ) ) {
			return $canonical;
		}
	}

	return $slug;
}

/**
 * @param string $slug
 * @return array<string, mixed>|null
 */
function map_plum_get_canton_meta( $slug ) {
	$slug     = map_plum_normalize_canton_slug( $slug );
	$registry = map_plum_cantons_registry();

	return isset( $registry[ $slug ] ) ? $registry[ $slug ] : null;
}

/**
 * @return array<int, string>
 */
function map_plum_get_all_canton_shortcode_tags() {
	$tags = array();
	foreach ( map_plum_cantons_registry() as $slug => $meta ) {
		$tags[] = $slug;
		if ( ! empty( $meta['aliases'] ) ) {
			foreach ( $meta['aliases'] as $alias ) {
				$tags[] = $alias;
			}
		}
	}

	return array_values( array_unique( $tags ) );
}

/**
 * Строки для дашборда: префектура + все шорткоды для копирования.
 *
 * @return array<int, array{slug: string, name: string, tags: array<int, string>}>
 */
function map_plum_get_dashboard_shortcode_rows() {
	$rows = array();
	foreach ( map_plum_cantons_registry() as $slug => $meta ) {
		$tags = array( $slug );
		if ( ! empty( $meta['aliases'] ) ) {
			foreach ( $meta['aliases'] as $alias ) {
				$tags[] = $alias;
			}
		}
		$rows[] = array(
			'slug' => $slug,
			'name' => ! empty( $meta['name'] ) ? (string) $meta['name'] : $slug,
			'tags' => array_values( array_unique( $tags ) ),
		);
	}

	return $rows;
}

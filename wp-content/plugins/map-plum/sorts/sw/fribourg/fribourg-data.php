<?php
/**
 * Настройки карты Fribourg (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/fribourg/fribourg-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.79, 7.15 ),
	'map_zoom'   => 10,
);

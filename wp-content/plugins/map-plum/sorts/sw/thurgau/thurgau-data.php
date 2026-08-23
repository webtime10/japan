<?php
/**
 * Настройки карты Thurgau (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/thurgau/thurgau-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.55, 9 ),
	'map_zoom'   => 10,
);

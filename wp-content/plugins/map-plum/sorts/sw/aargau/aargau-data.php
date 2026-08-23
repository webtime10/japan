<?php
/**
 * Настройки карты Aargau (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/aargau/aargau-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.39, 8.05 ),
	'map_zoom'   => 10,
);

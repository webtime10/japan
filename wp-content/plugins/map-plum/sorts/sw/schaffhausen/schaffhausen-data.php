<?php
/**
 * Настройки карты Schaffhausen (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/schaffhausen/schaffhausen-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.7, 8.65 ),
	'map_zoom'   => 11,
);

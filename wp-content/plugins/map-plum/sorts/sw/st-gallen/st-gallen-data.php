<?php
/**
 * Настройки карты St. Gallen (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/st-gallen/st-gallen-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.33, 9.1 ),
	'map_zoom'   => 9,
);

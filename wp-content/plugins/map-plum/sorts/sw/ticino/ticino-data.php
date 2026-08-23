<?php
/**
 * Настройки карты Ticino (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/ticino/ticino-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.32, 8.8 ),
	'map_zoom'   => 9,
);

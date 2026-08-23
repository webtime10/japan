<?php
/**
 * Настройки карты Zug (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/zug/zug-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.17, 8.52 ),
	'map_zoom'   => 11,
);

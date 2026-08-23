<?php
/**
 * Настройки карты Solothurn (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/solothurn/solothurn-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.21, 7.54 ),
	'map_zoom'   => 10,
);

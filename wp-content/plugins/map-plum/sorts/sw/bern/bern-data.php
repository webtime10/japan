<?php
/**
 * Настройки карты Берна (без демо-POI — маркеры и категории из админки Map Plum).
 *
 * Границы районов (областей) на карте: sorts/sw/bern/bern-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.95, 7.6 ),
	'map_zoom'   => 9,
);

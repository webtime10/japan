<?php
/**
 * Настройки карты Люцерна (без демо-POI — маркеры и категории из админки Map Plum).
 *
 * Границы районов (областей) на карте: sorts/sw/lucerne/lucerne-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.03, 8.18 ),
	'map_zoom'   => 12,
);

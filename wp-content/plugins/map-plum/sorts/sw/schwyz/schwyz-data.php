<?php
/**
 * Настройки карты Schwyz (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/schwyz/schwyz-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.02, 8.65 ),
	'map_zoom'   => 11,
);

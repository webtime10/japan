<?php
/**
 * Настройки карты Obwalden (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/obwalden/obwalden-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.88, 8.25 ),
	'map_zoom'   => 11,
);

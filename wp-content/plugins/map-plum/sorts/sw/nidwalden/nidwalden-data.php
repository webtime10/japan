<?php
/**
 * Настройки карты Nidwalden (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/nidwalden/nidwalden-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.95, 8.38 ),
	'map_zoom'   => 11,
);

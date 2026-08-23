<?php
/**
 * Настройки карты всей Швейцарии (маркеры и категории — из админки Map Plum).
 *
 * Границы кантонов: sorts/sw/switzerland/switzerland-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.82, 8.23 ),
	'map_zoom'   => 7,
);

<?php
/**
 * Настройки карты Neuchâtel (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/neuchatel/neuchatel-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.99, 6.75 ),
	'map_zoom'   => 10,
);

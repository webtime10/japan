<?php
/**
 * Настройки карты Zürich (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/zurich/zurich-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.37, 8.54 ),
	'map_zoom'   => 10,
);

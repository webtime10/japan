<?php
/**
 * Настройки карты Basel-Stadt (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/basel-stadt/basel-stadt-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.56, 7.59 ),
	'map_zoom'   => 12,
);

<?php
/**
 * Настройки карты Basel-Landschaft (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/basel-landschaft/basel-landschaft-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.45, 7.75 ),
	'map_zoom'   => 11,
);

<?php
/**
 * Настройки карты Jura (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/jura/jura-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.36, 7.15 ),
	'map_zoom'   => 10,
);

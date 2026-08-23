<?php
/**
 * Настройки карты Glarus (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/glarus/glarus-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 47.04, 9.07 ),
	'map_zoom'   => 11,
);

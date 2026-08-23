<?php
/**
 * Настройки карты Genève (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/geneva/geneva-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.2, 6.14 ),
	'map_zoom'   => 11,
);

<?php
/**
 * Настройки карты Vaud (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/vaud/vaud-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.56, 6.65 ),
	'map_zoom'   => 9,
);

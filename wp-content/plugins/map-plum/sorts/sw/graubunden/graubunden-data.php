<?php
/**
 * Настройки карты Graubünden (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/graubunden/graubunden-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.65, 9.65 ),
	'map_zoom'   => 9,
);

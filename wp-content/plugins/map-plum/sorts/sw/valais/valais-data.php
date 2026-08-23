<?php
/**
 * Настройки карты Valais (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/valais/valais-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( 46.24, 7.36 ),
	'map_zoom'   => 9,
);

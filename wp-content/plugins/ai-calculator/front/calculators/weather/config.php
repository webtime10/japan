<?php
/**
 * Настройки калькулятора погоды — все ID только здесь.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	// Каталог: товары в select (product_id = value, name = подпись).
	'category_id_months'  => 2,
	'category_id_regions' => 3,
	// Калькулятор «Погода» в админке (filter_manufacturer=1).
	'manufacturer_id'     => 1,

	// Язык названий в select и код для Laravel. 0 = текущий язык Polylang (en, ar, he).
	'language_id' => 0,
);

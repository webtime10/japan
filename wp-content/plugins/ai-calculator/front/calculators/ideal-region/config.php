<?php
/**
 * Your Ideal Region calculator config.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	// Калькулятор «Твой идеальный регион» в каталоге AI Calculator (категории в WP).
	'manufacturer_id' => 5,

	// ID производителя на lara2.loc (Япония = 2). Переопределяется в админке AI Calculator.
	'laravel_manufacturer_id' => 2,

	// 0 = текущий язык Polylang.
	'language_id'     => 0,

	// Сколько шагов показывать на фронте (каталог грузится целиком).
	'max_step'        => 8,
);

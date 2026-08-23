<?php
/**
 * Настройки калькулятора бюджета — все ID только здесь.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	// Калькулятор (manufacturer) в каталоге AI Calculator.
	'manufacturer_id' => 2,

	// Язык каталога. 0 = текущий язык Polylang → language_id в wp_ai_calculator_language.
	'language_id' => 0,
);

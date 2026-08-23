<?php
/**
 * UI-строки калькулятора бюджета — Polylang + languages-data.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	static function () {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		$strings = array(
			'Калькулятор стоимости поездки',
			'Вопрос',
			'из',
			'Далее',
			'Назад',
			'Заказать маршрут',
			'Закрыть',
			'Форма будет добавлена.',
			'Выберите кол-во',
			'Возраст 1 ребенка',
			'Напишите регион',
		);

		foreach ( $strings as $string ) {
			pll_register_string( 'ai-calculator-budget-' . md5( $string ), $string, 'ai-calculator', false );
		}
	}
);

/**
 * Одна UI-строка: Polylang → languages-data → дефолт.
 *
 * @param string $key     Ключ в languages-data.
 * @param string $default Русский текст по умолчанию.
 * @return string
 */
function ai_calculator_budget_label( $key, $default ) {
	$default   = (string) $default;
	$from_file = ai_calculator_translate( $key, '' );

	if ( is_string( $from_file ) && '' !== $from_file ) {
		return $from_file;
	}

	if ( function_exists( 'pll__' ) ) {
		$pll = pll__( $default );
		if ( is_string( $pll ) && '' !== $pll ) {
			return $pll;
		}
	}

	return $default;
}

/**
 * Все подписи кнопок и служебных текстов калькулятора бюджета.
 *
 * @return array<string, string>
 */
function ai_calculator_budget_ui_labels() {
	return array(
		'title'                  => ai_calculator_budget_label( 'budget_title', 'Калькулятор стоимости поездки' ),
		'question'               => ai_calculator_budget_label( 'budget_question', 'Вопрос' ),
		'of'                     => ai_calculator_budget_label( 'budget_of', 'из' ),
		'next'                   => ai_calculator_budget_label( 'budget_next', 'Далее' ),
		'back'                   => ai_calculator_budget_label( 'budget_back', 'Назад' ),
		'order_route'            => ai_calculator_budget_label( 'order_route', 'Заказать маршрут' ),
		'close'                  => ai_calculator_budget_label( 'budget_close', 'Закрыть' ),
		'form_placeholder'       => ai_calculator_budget_label( 'budget_form_placeholder', 'Форма будет добавлена.' ),
		'travelers_placeholder'  => ai_calculator_budget_label( 'budget_travelers_placeholder', 'Выберите кол-во' ),
		'children_age_placeholder' => ai_calculator_budget_label( 'budget_children_age_placeholder', 'Возраст 1 ребенка' ),
		'region_placeholder'     => ai_calculator_budget_label( 'budget_region_placeholder', 'Напишите регион' ),
	);
}

/**
 * Кнопка «Заказать маршрут».
 *
 * @return string
 */
function ai_calculator_budget_order_route_label() {
	$labels = ai_calculator_budget_ui_labels();

	return $labels['order_route'];
}

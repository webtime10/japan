<?php
/**
 * UI-строки Ideal Region — админка + Polylang + languages-data.
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
			'Ваш идеальный регион',
			'Вопрос',
			'из',
			'Далее',
			'Назад',
			'Выберите вариант',
			'Подобрать регион',
			'Подбираем регион…',
			'Ваш результат',
			'Мы подобрали для вас лучшие регионы',
			'Ещё {n} варианта',
			'Подробнее',
			'Скрыть',
		);

		foreach ( $strings as $string ) {
			pll_register_string( 'ai-calculator-ideal-region-' . md5( $string ), $string, 'ai-calculator', false );
		}
	}
);

/**
 * Дефолты подписей Ideal Region (русский).
 *
 * @return array<string, string>
 */
function ai_calculator_ideal_region_label_defaults() {
	return array(
		'title'                 => 'Ваш идеальный регион',
		'question'              => 'Вопрос',
		'of'                    => 'из',
		'next'                  => 'Далее',
		'back'                  => 'Назад',
		'choose'                => 'Выберите вариант',
		'submit'                => 'Подобрать регион',
		'submitting'            => 'Подбираем регион…',
		'submit_success'        => 'Ответы отправлены.',
		'submit_error'          => 'Не удалось отправить ответы. Попробуйте ещё раз.',
		'no_data'               => 'Нет вариантов для этого шага. Добавьте категорию и товары в админке.',
		'results_title'         => 'Мы подобрали для вас лучшие регионы',
		'user_goal_placeholder' => 'Ваш результат',
		'other_variants'        => 'Ещё {n} варианта',
		'more'                  => 'Подробнее',
		'hide'                  => 'Скрыть',
		'no_regions'            => 'Не удалось подобрать регионы.',
		'match'                 => 'Совпадение',
	);
}

/**
 * Ключи languages-data для Ideal Region.
 *
 * @return array<string, string> label_key => languages-data key
 */
function ai_calculator_ideal_region_language_keys() {
	return array(
		'title'                 => 'ideal_region_title',
		'question'              => 'ideal_region_question',
		'of'                    => 'ideal_region_of',
		'next'                  => 'ideal_region_next',
		'back'                  => 'ideal_region_back',
		'choose'                => 'ideal_region_choose',
		'submit'                => 'ideal_region_submit',
		'submitting'            => 'ideal_region_submitting',
		'user_goal_placeholder' => 'ideal_region_user_goal_placeholder',
		'results_title'         => 'ideal_region_results_title',
		'other_variants'        => 'ideal_region_other_variants',
		'more'                  => 'ideal_region_more',
		'hide'                  => 'ideal_region_hide',
		'match'                 => 'ideal_region_match',
	);
}

/**
 * Одна UI-строка: админка → languages-data → Polylang → дефолт.
 *
 * @param string $key     Ключ в ai_calculator_ideal_region_labels.
 * @param string $default Русский текст по умолчанию.
 * @return string
 */
function ai_calculator_ideal_region_label( $key, $default ) {
	$default = (string) $default;
	$key     = (string) $key;

	$saved = get_option( 'ai_calculator_ideal_region_labels', array() );
	if ( is_array( $saved ) && ! empty( $saved[ $key ] ) ) {
		return (string) $saved[ $key ];
	}

	$lang_keys = ai_calculator_ideal_region_language_keys();
	if ( isset( $lang_keys[ $key ] ) && function_exists( 'ai_calculator_translate' ) ) {
		$from_file = ai_calculator_translate( $lang_keys[ $key ], '' );
		if ( is_string( $from_file ) && '' !== $from_file ) {
			return $from_file;
		}
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
 * Все подписи Ideal Region для фронта.
 *
 * @return array<string, string>
 */
function ai_calculator_ideal_region_ui_labels() {
	$defaults = ai_calculator_ideal_region_label_defaults();
	$labels   = array();

	foreach ( $defaults as $key => $default ) {
		if ( 'title' === $key ) {
			continue;
		}
		$labels[ $key ] = ai_calculator_ideal_region_label( $key, $default );
	}

	$saved = get_option( 'ai_calculator_ideal_region_labels', array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$title = '';
	if ( ! empty( $saved['title'] ) ) {
		$title = trim( (string) $saved['title'] );
	}
	if ( '' === $title && function_exists( 'ai_calculator_get_custom_title' ) ) {
		$custom = trim( (string) ai_calculator_get_custom_title( 'ideal_region', '' ) );
		if ( '' !== $custom ) {
			$title = $custom;
		}
	}
	if ( '' === $title && function_exists( 'ai_calculator_get_ideal_region_background' ) ) {
		$bg       = ai_calculator_get_ideal_region_background();
		$bg_label = isset( $bg['label'] ) ? trim( (string) $bg['label'] ) : '';
		if ( '' !== $bg_label ) {
			$title = $bg_label;
		}
	}
	if ( '' === $title ) {
		$title = ai_calculator_ideal_region_label( 'title', $defaults['title'] );
	}
	$labels['title'] = $title;

	return $labels;
}

/**
 * Сохранить подписи Ideal Region и синхронизировать заголовок.
 *
 * @param array<string, mixed> $posted Сырые значения из POST.
 * @return array<string, string>
 */
function ai_calculator_save_ideal_region_labels( array $posted ) {
	$defaults = ai_calculator_ideal_region_label_defaults();
	$existing = get_option( 'ai_calculator_ideal_region_labels', array() );
	if ( ! is_array( $existing ) ) {
		$existing = array();
	}
	$clean = array();

	foreach ( array_keys( $defaults ) as $key ) {
		if ( ! array_key_exists( $key, $posted ) ) {
			if ( ! empty( $existing[ $key ] ) ) {
				$clean[ $key ] = (string) $existing[ $key ];
			}
			continue;
		}
		$value = sanitize_text_field( (string) $posted[ $key ] );
		if ( '' !== $value ) {
			$clean[ $key ] = $value;
		}
	}

	update_option( 'ai_calculator_ideal_region_labels', $clean, false );

	if ( ! empty( $clean['title'] ) ) {
		$titles = get_option( 'ai_calculator_titles', array() );
		if ( ! is_array( $titles ) ) {
			$titles = array();
		}
		$titles['ideal_region'] = $clean['title'];
		update_option( 'ai_calculator_titles', $titles, false );

		$bg = function_exists( 'ai_calculator_get_ideal_region_background' )
			? ai_calculator_get_ideal_region_background()
			: array( 'image' => '', 'label' => '' );
		$bg['label'] = $clean['title'];
		update_option( 'ai_calculator_ideal_region_bg', $bg, false );
	}

	return $clean;
}

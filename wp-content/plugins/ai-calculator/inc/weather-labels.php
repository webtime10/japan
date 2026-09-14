<?php
/**
 * UI-строки Weather — админка (по языкам) + languages-data по умолчанию.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ключи подписей погоды, редактируемые в админке.
 *
 * @return array<string, string> key => admin label (RU)
 */
function ai_calculator_weather_label_fields() {
	return array(
		'weather_title'                 => __( 'H2 заголовок', 'ai-calculator' ),
		'weather_select_month'          => __( 'Селект: месяц', 'ai-calculator' ),
		'weather_select_region'         => __( 'Селект: регион', 'ai-calculator' ),
		'weather_label_avg_temperature' => __( 'Подпись: средняя температура', 'ai-calculator' ),
		'weather_label_temp_day'        => __( 'Подпись: днём', 'ai-calculator' ),
		'weather_label_temp_night'      => __( 'Подпись: ночью', 'ai-calculator' ),
		'weather_label_precipitation'   => __( 'Подпись: осадки', 'ai-calculator' ),
		'weather_label_sunny_days'      => __( 'Подпись: солнечные дни', 'ai-calculator' ),
		'weather_label_active_season'   => __( 'Подпись: активный сезон', 'ai-calculator' ),
	);
}

/**
 * Языки подписей погоды для этого сайта (один язык).
 *
 * @return list<string>
 */
function ai_calculator_weather_label_lang_slugs() {
	return array( 'he' );
}

/**
 * Сохранённые оверрайды: [ lang => [ key => value ] ].
 *
 * @return array<string, array<string, string>>
 */
function ai_calculator_get_weather_labels_saved() {
	$saved = get_option( 'ai_calculator_weather_labels', array() );
	if ( ! is_array( $saved ) ) {
		return array();
	}

	$out = array();
	foreach ( $saved as $lang => $rows ) {
		$lang = strtolower( sanitize_key( (string) $lang ) );
		if ( '' === $lang || ! is_array( $rows ) ) {
			continue;
		}
		$out[ $lang ] = array();
		foreach ( $rows as $key => $value ) {
			$key   = (string) $key;
			$value = trim( (string) $value );
			if ( '' === $key || '' === $value ) {
				continue;
			}
			$out[ $lang ][ $key ] = $value;
		}
	}

	return $out;
}

/**
 * Сохранить оверрайды из POST.
 *
 * @param array<string, mixed> $posted ai_calculator_weather_labels[lang][key]
 * @return array<string, array<string, string>>
 */
function ai_calculator_save_weather_labels( array $posted ) {
	$fields = array_keys( ai_calculator_weather_label_fields() );
	$langs  = ai_calculator_weather_label_lang_slugs();
	$clean  = array();

	foreach ( $langs as $lang ) {
		$rows = isset( $posted[ $lang ] ) && is_array( $posted[ $lang ] ) ? $posted[ $lang ] : array();
		foreach ( $fields as $key ) {
			$value = isset( $rows[ $key ] ) ? sanitize_text_field( (string) $rows[ $key ] ) : '';
			$value = trim( $value );
			if ( '' === $value ) {
				continue;
			}
			if ( ! isset( $clean[ $lang ] ) ) {
				$clean[ $lang ] = array();
			}
			$clean[ $lang ][ $key ] = $value;
		}
	}

	update_option( 'ai_calculator_weather_labels', $clean, false );

	return $clean;
}

/**
 * Оверрайд одной строки для языка (или текущего Polylang).
 *
 * @param string $key
 * @param string $lang Пусто = текущий язык.
 * @return string Пустая строка = нет оверрайда.
 */
function ai_calculator_weather_label_override( $key, $lang = '' ) {
	$key = (string) $key;
	if ( '' === $key || 0 !== strpos( $key, 'weather_' ) ) {
		return '';
	}

	if ( '' === $lang ) {
		$lang = function_exists( 'ai_calculator_polylang_slug' ) ? ai_calculator_polylang_slug() : '';
	}
	$lang = strtolower( sanitize_key( (string) $lang ) );
	if ( '' === $lang ) {
		return '';
	}

	$saved = ai_calculator_get_weather_labels_saved();
	if ( ! empty( $saved[ $lang ][ $key ] ) ) {
		return (string) $saved[ $lang ][ $key ];
	}

	// Сайт на одном языке: если slug не совпал — берём единственный настроенный.
	$site_langs = ai_calculator_weather_label_lang_slugs();
	if ( count( $site_langs ) === 1 ) {
		$only = (string) $site_langs[0];
		if ( $only !== $lang && ! empty( $saved[ $only ][ $key ] ) ) {
			return (string) $saved[ $only ][ $key ];
		}
	}

	return '';
}

/**
 * Дефолт из languages-data для языка (без оверрайдов админки).
 *
 * @param string $key
 * @param string $lang
 * @return string
 */
function ai_calculator_weather_label_default( $key, $lang ) {
	$key  = (string) $key;
	$lang = strtolower( sanitize_key( (string) $lang ) );
	if ( '' === $key || '' === $lang || ! function_exists( 'ai_calculator_load_language_data' ) ) {
		return '';
	}
	$data = ai_calculator_load_language_data( $lang );
	if ( isset( $data[ $key ] ) && '' !== (string) $data[ $key ] ) {
		return (string) $data[ $key ];
	}

	return '';
}

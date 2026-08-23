<?php
/**
 * Публичный API: сохранённый URL Laravel для темы, ACF и контента.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once AI_CALCULATOR_PATH . 'inc/budget-swiss-regions.php';
require_once AI_CALCULATOR_PATH . 'inc/budget-images.php';
require_once AI_CALCULATOR_PATH . 'inc/budget-labels.php';
require_once AI_CALCULATOR_PATH . 'inc/budget-result-labels.php';

/**
 * URL, сохранённый на AI Calculator → Home (активный).
 *
 * @return string
 */
function ai_calculator_remote_url() {
	require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';
	return AI_Calculator_Settings::get_active_url();
}

/**
 * URL плагина AI Calculator (со слэшем на конце).
 *
 * @param string $path Путь относительно корня плагина, например img/calendar.svg.
 * @return string
 */
function ai_calculator_plugin_url( $path = '' ) {
	$url = trailingslashit( AI_CALCULATOR_URL );

	if ( '' !== (string) $path ) {
		$url .= ltrim( (string) $path, '/' );
	}

	return $url;
}

/**
 * URL папки img/ плагина. Без файла — база со слэшем; с файлом — полный URL картинки.
 *
 * @param string $file Имя файла, например calendar.svg.
 * @return string
 */
function ai_calculator_img_url( $file = '' ) {
	$url = ai_calculator_plugin_url( 'assets/img/' );

	if ( '' !== (string) $file ) {
		$url .= ltrim( (string) $file, '/' );
	}

	return $url;
}

/**
 * Алиасы slug Polylang → файл languages-data (iw → he и т.д.).
 *
 * @return array<string, string>
 */
function ai_calculator_language_slug_aliases() {
	return array(
		'iw' => 'he',
	);
}

/**
 * Нормализовать slug языка для languages-data и локали.
 *
 * @param string $slug
 * @return string
 */
function ai_calculator_normalize_lang_slug( $slug ) {
	$slug    = sanitize_key( (string) $slug );
	$aliases = ai_calculator_language_slug_aliases();

	return isset( $aliases[ $slug ] ) ? $aliases[ $slug ] : $slug;
}

/**
 * Slug текущего языка Polylang (en, ar, he …).
 *
 * @return string
 */
function ai_calculator_polylang_slug() {
	if ( function_exists( 'pll_current_language' ) ) {
		$slug = pll_current_language( 'slug' );
		if ( is_string( $slug ) && '' !== $slug ) {
			return ai_calculator_normalize_lang_slug( $slug );
		}
	}

	if ( function_exists( 'pll_default_language' ) ) {
		$slug = pll_default_language( 'slug' );
		if ( is_string( $slug ) && '' !== $slug ) {
			return ai_calculator_normalize_lang_slug( $slug );
		}
	}

	return 'ar';
}

/**
 * Локаль jquery.datetimepicker по языку Polylang.
 *
 * @param string $slug Код языка Polylang.
 * @return array{locale: string, rtl: bool, dayOfWeekStart: int}
 */
function ai_calculator_datetimepicker_lang( $slug = '' ) {
	$slug = '' !== (string) $slug ? ai_calculator_normalize_lang_slug( (string) $slug ) : ai_calculator_polylang_slug();

	$map = array(
		'en' => array(
			'locale'          => 'en',
			'rtl'             => false,
			'dayOfWeekStart'  => 1,
		),
		'ar' => array(
			'locale'          => 'he',
			'rtl'             => true,
			'dayOfWeekStart'  => 0,
		),
		'he' => array(
			'locale'          => 'he',
			'rtl'             => true,
			'dayOfWeekStart'  => 0,
		),
		'iw' => array(
			'locale'          => 'he',
			'rtl'             => true,
			'dayOfWeekStart'  => 0,
		),
	);

	if ( isset( $map[ $slug ] ) ) {
		return $map[ $slug ];
	}

	return $map['ar'];
}

/**
 * Загрузить массив переводов плагина для slug Polylang.
 *
 * @param string $slug
 * @return array<string, string>
 */
function ai_calculator_load_language_data( $slug ) {
	$slug = ai_calculator_normalize_lang_slug( $slug );
	if ( 'ar' === $slug ) {
		$slug = 'he';
	}
	$dir  = AI_CALCULATOR_PATH . 'languages-data/';
	$path = $dir . $slug . '.php';

	if ( is_readable( $path ) ) {
		$data = include $path;
		return is_array( $data ) ? $data : array();
	}

	$en_path = $dir . 'en.php';
	if ( is_readable( $en_path ) ) {
		$data = include $en_path;
		return is_array( $data ) ? $data : array();
	}

	return array();
}

/**
 * Перевод строки калькулятора по ключу (languages-data + Polylang slug).
 *
 * @param string $key
 * @param string $fallback Подставить, если ключ не найден.
 */
function ai_calculator_translate( $key, $fallback = '' ) {
	static $translations = array();
	static $loaded_slug  = '';

	$key = (string) $key;
	if ( '' === $key ) {
		return $fallback;
	}

	$slug = ai_calculator_polylang_slug();
	if ( $loaded_slug !== $slug ) {
		$loaded_slug  = $slug;
		$translations = ai_calculator_load_language_data( $slug );
	}

	if ( isset( $translations[ $key ] ) && '' !== (string) $translations[ $key ] ) {
		return (string) $translations[ $key ];
	}

	if ( function_exists( 'get_theme_translation' ) ) {
		$theme_text = get_theme_translation( $key );
		if ( $theme_text !== $key ) {
			return $theme_text;
		}
	}

	return '' !== (string) $fallback ? (string) $fallback : $key;
}

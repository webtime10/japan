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

	return 'he';
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
			'locale'          => 'ar',
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

/**
 * Пользовательский заголовок калькулятора (замена для h2).
 *
 * @param string $slug    Controller slug: weather|budget|family_comfort
 * @param string $default Заголовок по умолчанию
 * @return string
 */
function ai_calculator_get_custom_title( $slug, $default = '' ) {
	$slug = sanitize_key( (string) $slug );
	if ( '' === $slug ) {
		return (string) $default;
	}

	$titles = get_option( 'ai_calculator_titles', array() );
	if ( ! is_array( $titles ) ) {
		$titles = array();
	}

	if ( isset( $titles[ $slug ] ) ) {
		$title = trim( (string) $titles[ $slug ] );
		if ( '' !== $title ) {
			return $title;
		}
	}

	return (string) $default;
}

/**
 * Тексты кнопок Chat по умолчанию.
 *
 * @return array{summary: string, regions: string, cost: string, route: string}
 */
function ai_calculator_chat_label_defaults() {
	return array(
		'summary' => __( 'Получить краткое резюме', 'ai-calculator' ),
		'regions' => __( 'Найти лучшие регионы', 'ai-calculator' ),
		'cost'    => __( 'Узнать стоимость', 'ai-calculator' ),
		'route'   => __( 'Подобрать маршрут', 'ai-calculator' ),
	);
}

/**
 * Тексты Chat: заголовок + кнопки (из админки Главная).
 *
 * @return array{title: string, summary: string, regions: string, cost: string, route: string}
 */
function ai_calculator_get_chat_labels() {
	$defaults = ai_calculator_chat_label_defaults();
	$title    = ai_calculator_get_custom_title(
		'chat',
		__( 'Не хотите читать всю статью?', 'ai-calculator' )
	);

	$stored = get_option( 'ai_calculator_chat_labels', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	$labels = array( 'title' => $title );
	foreach ( $defaults as $key => $default ) {
		$value = isset( $stored[ $key ] ) ? trim( (string) $stored[ $key ] ) : '';
		$labels[ $key ] = '' !== $value ? $value : $default;
	}

	return $labels;
}

/**
 * Фон калькулятора Ideal Region: фото + надпись.
 *
 * @return array{image: string, label: string}
 */
function ai_calculator_get_ideal_region_background() {
	$stored = get_option( 'ai_calculator_ideal_region_bg', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	return array(
		'image' => isset( $stored['image'] ) ? esc_url_raw( (string) $stored['image'] ) : '',
		'label' => isset( $stored['label'] ) ? sanitize_text_field( (string) $stored['label'] ) : '',
	);
}

/**
 * ID производителя «Япония» на Laravel для Ideal Region (подбор направлений).
 * На japan.loc всегда 2 (Япония), не Швейцария.
 *
 * @return int
 */
function ai_calculator_get_ideal_region_laravel_manufacturer_id() {
	static $manufacturer_id = null;

	if ( null !== $manufacturer_id ) {
		return $manufacturer_id;
	}

	// Жёстко: этот сайт — Япония. Нельзя уехать в manufacturer_id=1 (Швейцария)
	// или в WP-каталожный id=5 из config['manufacturer_id'].
	$stored = (int) get_option( 'ai_calculator_ideal_region_laravel_manufacturer_id', 0 );
	if ( 2 === $stored ) {
		$manufacturer_id = 2;
		return $manufacturer_id;
	}

	$config_path = AI_CALCULATOR_PATH . 'front/calculators/ideal-region/config.php';
	if ( is_readable( $config_path ) ) {
		$config = include $config_path;
		if ( is_array( $config ) ) {
			$config_id = (int) ( $config['laravel_manufacturer_id'] ?? 0 );
			if ( 2 === $config_id ) {
				$manufacturer_id = 2;
				return $manufacturer_id;
			}
		}
	}

	$manufacturer_id = 2;

	return $manufacturer_id;
}

/**
 * Проверить, что название калькулятора относится к Family Comfort.
 *
 * @param string $name
 * @return bool
 */
function ai_calculator_family_comfort_name_matches( $name ) {
	$name = mb_strtolower( wp_strip_all_tags( (string) $name ) );
	$needles = array(
		'family comfort',
		'family-comfort',
		'семейн',
		'family_comfort',
	);

	foreach ( $needles as $needle ) {
		if ( '' !== $needle && false !== strpos( $name, $needle ) ) {
			return true;
		}
	}

	return false;
}

/**
 * ID калькулятора Family Comfort в каталоге.
 *
 * @return int
 */
function ai_calculator_get_family_comfort_manufacturer_id() {
	static $manufacturer_id = null;

	if ( null !== $manufacturer_id ) {
		return $manufacturer_id;
	}

	$manufacturer_id = 0;
	global $wpdb;

	$config_path = AI_CALCULATOR_PATH . 'front/calculators/family-comfort/config.php';
	if ( is_readable( $config_path ) ) {
		$config    = require $config_path;
		$config_id = (int) ( $config['manufacturer_id'] ?? 0 );
		if ( $config_id > 0 ) {
			$m_table = $wpdb->prefix . 'ai_calculator_manufacturer';
			$exists  = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT manufacturer_id FROM `{$m_table}` WHERE manufacturer_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$config_id
				)
			);
			if ( $exists > 0 ) {
				$manufacturer_id = $config_id;
				return $manufacturer_id;
			}
		}
	}

	$d_table = $wpdb->prefix . 'ai_calculator_manufacturer_description';
	$rows    = $wpdb->get_results( "SELECT manufacturer_id, name FROM `{$d_table}`" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	foreach ( (array) $rows as $row ) {
		if ( ai_calculator_family_comfort_name_matches( $row->name ?? '' ) ) {
			$manufacturer_id = (int) $row->manufacturer_id;
			return $manufacturer_id;
		}
	}

	return 0;
}

/**
 * Калькулятор с этим ID относится к Family Comfort.
 *
 * @param int $manufacturer_id
 * @return bool
 */
function ai_calculator_is_family_comfort_manufacturer_id( $manufacturer_id ) {
	$manufacturer_id = (int) $manufacturer_id;
	if ( $manufacturer_id <= 0 ) {
		return false;
	}

	$family_comfort_id = ai_calculator_get_family_comfort_manufacturer_id();
	if ( $family_comfort_id > 0 && $manufacturer_id === $family_comfort_id ) {
		return true;
	}

	return false;
}

/**
 * Категория относится к калькулятору Family Comfort (включая подкатегории).
 *
 * @param int $category_id
 * @return bool
 */
function ai_calculator_is_family_comfort_category( $category_id ) {
	$category_id = (int) $category_id;
	if ( $category_id <= 0 ) {
		return false;
	}

	global $wpdb;

	$c_table = $wpdb->prefix . 'ai_calculator_category';
	$mfr_id  = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT manufacturer_id FROM `{$c_table}` WHERE category_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$category_id
		)
	);

	if ( ai_calculator_is_family_comfort_manufacturer_id( $mfr_id ) ) {
		return true;
	}

	if ( $mfr_id <= 0 ) {
		return false;
	}

	$d_table = $wpdb->prefix . 'ai_calculator_manufacturer_description';
	$name    = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT name FROM `{$d_table}` WHERE manufacturer_id = %d LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$mfr_id
		)
	);

	return ai_calculator_family_comfort_name_matches( $name ?? '' );
}

/**
 * ID корневой категории Family Comfort (parent_id = 0).
 *
 * @param bool $refresh Сбросить кэш.
 * @return int
 */
function ai_calculator_get_family_comfort_root_category_id( $refresh = false ) {
	static $root_id = null;

	if ( $refresh ) {
		$root_id = null;
	}

	if ( null !== $root_id ) {
		return $root_id;
	}

	$manufacturer_id = ai_calculator_get_family_comfort_manufacturer_id();
	if ( $manufacturer_id <= 0 ) {
		$root_id = 0;
		return 0;
	}

	global $wpdb;

	$c_table = $wpdb->prefix . 'ai_calculator_category';
	$root_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT category_id FROM `{$c_table}`
			WHERE manufacturer_id = %d AND parent_id = 0
			ORDER BY sort_order ASC, category_id ASC
			LIMIT 1",
			$manufacturer_id
		)
	);

	return $root_id;
}

/**
 * @param int $category_id
 * @return bool
 */
function ai_calculator_is_family_comfort_root_category( $category_id ) {
	$category_id = (int) $category_id;
	if ( $category_id <= 0 ) {
		return false;
	}

	$root_id = ai_calculator_get_family_comfort_root_category_id();

	return $root_id > 0 && $category_id === $root_id;
}

/**
 * Создать корневую категорию Family Comfort и привязать к ней остальные.
 *
 * @return int
 */
function ai_calculator_ensure_family_comfort_root_category() {
	$manufacturer_id = ai_calculator_get_family_comfort_manufacturer_id();
	if ( $manufacturer_id <= 0 ) {
		return 0;
	}

	global $wpdb;

	$c_table  = $wpdb->prefix . 'ai_calculator_category';
	$d_table  = $wpdb->prefix . 'ai_calculator_category_description';
	$md_table = $wpdb->prefix . 'ai_calculator_manufacturer_description';

	$manufacturer_names = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT language_id, name FROM `{$md_table}` WHERE manufacturer_id = %d AND name <> ''",
			$manufacturer_id
		)
	);

	$root_id = 0;

	foreach ( (array) $manufacturer_names as $manufacturer_name ) {
		$language_id = (int) $manufacturer_name->language_id;
		$name        = trim( (string) $manufacturer_name->name );
		if ( '' === $name || $language_id <= 0 ) {
			continue;
		}

		$matched_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT c.category_id
				FROM `{$c_table}` c
				INNER JOIN `{$d_table}` d ON c.category_id = d.category_id AND d.language_id = %d
				WHERE c.manufacturer_id = %d AND c.parent_id = 0 AND d.name = %s
				ORDER BY c.category_id ASC
				LIMIT 1",
				$language_id,
				$manufacturer_id,
				$name
			)
		);

		if ( $matched_id > 0 ) {
			$root_id = $matched_id;
			break;
		}
	}

	if ( $root_id <= 0 ) {
		$wpdb->insert(
			$c_table,
			array(
				'manufacturer_id' => $manufacturer_id,
				'parent_id'       => 0,
				'image'           => '',
				'sort_order'      => 0,
				'status'          => 1,
			),
			array( '%d', '%d', '%s', '%d', '%d' )
		);
		$root_id = (int) $wpdb->insert_id;

		foreach ( (array) $manufacturer_names as $manufacturer_name ) {
			$language_id = (int) $manufacturer_name->language_id;
			$name        = trim( (string) $manufacturer_name->name );
			if ( '' === $name || $language_id <= 0 ) {
				continue;
			}

			$wpdb->insert(
				$d_table,
				array(
					'category_id'      => $root_id,
					'language_id'      => $language_id,
					'name'             => $name,
					'description'      => '',
					'meta_title'       => '',
					'meta_description' => '',
				),
				array( '%d', '%d', '%s', '%s', '%s', '%s' )
			);
		}
	}

	$wpdb->update(
		$c_table,
		array( 'parent_id' => 0 ),
		array(
			'category_id'     => $root_id,
			'manufacturer_id' => $manufacturer_id,
		),
		array( '%d' ),
		array( '%d', '%d' )
	);

	$wpdb->query(
		$wpdb->prepare(
			"UPDATE `{$c_table}`
			SET parent_id = %d
			WHERE manufacturer_id = %d AND category_id <> %d",
			$root_id,
			$manufacturer_id,
			$root_id
		)
	);

	ai_calculator_get_family_comfort_root_category_id( true );

	return $root_id;
}

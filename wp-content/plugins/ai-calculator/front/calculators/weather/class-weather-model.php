<?php
/**
 * Weather — Model.
 *
 * Только работа с данными (БД, config). Без HTML и без REST.
 * Месяцы: value = product_id, текст = name.
 * Регионы: value = «Наз. на русск.» из админки, текст = арабский Name.
 *
 * @package ai-calculator
 */

// Запрет прямого вызова файла из браузера.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Модель калькулятора погоды.
 * Наследует AI_Calculator_Model — оттуда $wpdb и table().
 */
class AI_Calculator_Weather_Model extends AI_Calculator_Model {

	/** @var int ID категории месяцев — из config.php */
	private $category_id_months;

	/** @var int ID категории регионов — из config.php */
	private $category_id_regions;

	/** @var int ID производителя-калькулятора (как filter_manufacturer в админке) */
	private $manufacturer_id;

	/** @var int language_id; 0 = Polylang */
	private $language_id;

	/**
	 * Конструктор: wpdb + один раз читает config.php.
	 */
	public function __construct() {
		parent::__construct();

		$config = require AI_CALCULATOR_FRONT_PATH . 'calculators/weather/config.php';

		$this->category_id_months  = (int) ( $config['category_id_months'] ?? 0 );
		$this->category_id_regions = (int) ( $config['category_id_regions'] ?? 0 );
		$this->manufacturer_id     = (int) ( $config['manufacturer_id'] ?? 0 );
		$this->language_id         = (int) ( $config['language_id'] ?? 0 );
	}

	/**
	 * Язык для калькулятора погоды: из config или Polylang.
	 *
	 * @return int
	 */
	private function get_weather_language_id() {
		return $this->language_id > 0 ? $this->language_id : $this->get_current_language_id();
	}

	/**
	 * Список месяцев для &lt;select&gt;.
	 *
	 * @return array<int, string> Ключ — product_id, значение — название из админки.
	 */
	public function get_months() {
		return $this->get_products_by_category( $this->category_id_months );
	}

	/**
	 * Список регионов для &lt;select&gt;.
	 * label = арабский Name, value = «Наз. на русск.» (block6 → description → name).
	 *
	 * @return array<int, array{label: string, value: string}>
	 */
	public function get_regions() {
		$category_id = $this->category_id_regions;
		if ( $category_id <= 0 ) {
			return array();
		}

		$language_id = $this->get_weather_language_id();
		$p_table     = $this->table( 'product' );
		$d_table     = $this->table( 'product_description' );
		$p2c         = $this->table( 'product_to_category' );

		$where  = 'p.status = 1';
		$params = array( $category_id, $language_id );

		if ( $this->manufacturer_id > 0 ) {
			$where   .= ' AND p.manufacturer_id = %d';
			$params[] = $this->manufacturer_id;
		}

		$sql = $this->wpdb->prepare(
			"SELECT p.product_id,
				NULLIF(TRIM(d.name), '') AS label_ar,
				COALESCE(NULLIF(TRIM(d.block6), ''), NULLIF(TRIM(d.description), ''), NULLIF(TRIM(d.name), '')) AS value_ru
			FROM `{$p_table}` p
			INNER JOIN `{$p2c}` p2c ON p.product_id = p2c.product_id AND p2c.category_id = %d
			LEFT JOIN `{$d_table}` d ON p.product_id = d.product_id AND d.language_id = %d
			WHERE {$where}
			ORDER BY p.sort_order ASC, label_ar ASC",
			$params
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$out  = array();

		foreach ( $rows as $row ) {
			$id    = (int) $row->product_id;
			$label = ! empty( $row->label_ar ) ? (string) $row->label_ar : '#' . $id;
			$value = ! empty( $row->value_ru ) ? (string) $row->value_ru : $label;
			$out[ $id ] = array(
				'label' => $label,
				'value' => $value,
			);
		}

		return $out;
	}

	/**
	 * Значения по умолчанию при первой загрузке виджета.
	 *
	 * @return array{month: int, region: string}
	 */
	public function get_defaults() {
		return array(
			'month'  => 0,
			'region' => '',
		);
	}

	/**
	 * Проверяет, что переданные значения есть в каталоге.
	 *
	 * @param string|int $region_value Русское название региона (option value) или product_id.
	 * @param int        $month_id     product_id месяца.
	 * @return array{month: int, region: string, region_id: int}
	 */
	public function resolve_selection( $region_value, $month_id ) {
		$defaults = $this->get_defaults();
		$months   = $this->get_months();
		$regions  = $this->get_regions();
		$month_id = (int) $month_id;

		if ( $month_id <= 0 || ! isset( $months[ $month_id ] ) ) {
			$month_id = (int) $defaults['month'];
		}

		$region_id    = 0;
		$region_ru    = '';
		$region_raw   = is_string( $region_value ) ? trim( $region_value ) : (string) (int) $region_value;

		if ( $region_raw !== '' && ctype_digit( $region_raw ) && isset( $regions[ (int) $region_raw ] ) ) {
			$region_id = (int) $region_raw;
			$region_ru = (string) $regions[ $region_id ]['value'];
		} else {
			foreach ( $regions as $id => $item ) {
				if ( (string) $item['value'] === $region_raw ) {
					$region_id = (int) $id;
					$region_ru = (string) $item['value'];
					break;
				}
			}
		}

		return array(
			'month'     => $month_id,
			'region'    => $region_ru,
			'region_id' => $region_id,
		);
	}

	/**
	 * Названия месяца и региона для Laravel.
	 *
	 * @param int        $month_id
	 * @param string|int $region_value
	 * @return array{month_name: string, region_name: string, region_id: int}
	 */
	public function get_selection_names( $month_id, $region_value ) {
		$months   = $this->get_months();
		$resolved = $this->resolve_selection( $region_value, $month_id );
		$month_id = (int) $resolved['month'];

		return array(
			'month_name'  => isset( $months[ $month_id ] ) ? (string) $months[ $month_id ] : '',
			'region_name' => (string) $resolved['region'],
			'region_id'   => (int) $resolved['region_id'],
		);
	}

	/**
	 * Код языка для Laravel: en, ar, he (Polylang slug или code из таблицы language).
	 *
	 * @return string
	 */
	public function get_current_language_code() {
		$allowed = array( 'en', 'ar', 'he' );
		$pll     = $this->get_polylang_current_language();

		if ( $pll['slug'] !== '' ) {
			$code = strtolower( $pll['slug'] );
			if ( 'iw' === $code ) {
				$code = 'he';
			}
			if ( in_array( $code, $allowed, true ) ) {
				return $code;
			}
		}

		if ( $pll['locale'] !== '' ) {
			$short = strtolower( (string) strtok( $pll['locale'], '_' ) );
			if ( 'iw' === $short ) {
				$short = 'he';
			}
			if ( in_array( $short, $allowed, true ) ) {
				return $short;
			}
		}

		$lang_id = $this->get_weather_language_id();
		if ( $lang_id > 0 ) {
			$table = $this->table( 'language' );
			$code  = (string) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT code FROM `{$table}` WHERE language_id = %d LIMIT 1",
					$lang_id
				)
			);
			$code = strtolower( sanitize_key( $code ) );
			if ( 'iw' === $code ) {
				$code = 'he';
			}
			if ( in_array( $code, $allowed, true ) ) {
				return $code;
			}
		}

		return 'en';
	}

	/**
	 * Тело запроса на Laravel: месяц, регион, язык.
	 *
	 * @param int        $month_id
	 * @param string|int $region_value Русское название из option value.
	 * @return array{month_name: string, region_name: string, month: int, region: string, language: string}
	 */
	public function build_laravel_payload( $month_id, $region_value ) {
		$names    = $this->get_selection_names( $month_id, $region_value );
		$month_id = (int) $month_id;

		return array(
			'month_name'  => $names['month_name'],
			'region_name' => $names['region_name'],
			'month'       => $month_id,
			'region'      => $names['region_name'],
			'language'    => $this->get_current_language_code(),
		);
	}

	/**
	 * Товары одной категории каталога — основа для get_months().
	 *
	 * @param int $category_id ID категории из config.php.
	 * @param int $language_id 0 = language_id из config или Polylang.
	 * @return array<int, string>  product_id => название.
	 */
	public function get_products_by_category( $category_id, $language_id = 0 ) {
		$category_id = (int) $category_id;
		if ( $category_id <= 0 ) {
			return array();
		}

		if ( $language_id <= 0 ) {
			$language_id = $this->get_weather_language_id();
		}

		$p_table = $this->table( 'product' );
		$d_table = $this->table( 'product_description' );
		$p2c     = $this->table( 'product_to_category' );

		$where  = 'p.status = 1';
		$params = array( $category_id, $language_id );

		if ( $this->manufacturer_id > 0 ) {
			$where   .= ' AND p.manufacturer_id = %d';
			$params[] = $this->manufacturer_id;
		}

		$sql = $this->wpdb->prepare(
			"SELECT p.product_id, d.name AS label
			FROM `{$p_table}` p
			INNER JOIN `{$p2c}` p2c ON p.product_id = p2c.product_id AND p2c.category_id = %d
			LEFT JOIN `{$d_table}` d ON p.product_id = d.product_id AND d.language_id = %d
			WHERE {$where}
			ORDER BY p.sort_order ASC, label ASC",
			$params
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$out  = array();

		foreach ( $rows as $row ) {
			$id         = (int) $row->product_id;
			$out[ $id ] = ! empty( $row->label ) ? (string) $row->label : '#' . $id;
		}

		return $out;
	}

}

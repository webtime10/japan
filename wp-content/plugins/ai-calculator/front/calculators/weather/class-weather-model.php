<?php
/**
 * Weather — Model.
 *
 * Только работа с данными (БД, config). Без HTML и без REST.
 * Месяцы и регионы — товары (product) из категорий config.php.
 * select: value = product_id, текст = name. Дальше AJAX шлёт названия в Laravel.
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
		// Все активные товары из категории месяцев (см. $category_id_months).
		return $this->get_products_by_category( $this->category_id_months );
	}

	/**
	 * Список регионов для &lt;select&gt;.
	 *
	 * @return array<int, string> Ключ — product_id, значение — название из админки.
	 */
	public function get_regions() {
		// Все активные товары из категории регионов (см. $category_id_regions).
		return $this->get_products_by_category( $this->category_id_regions );
	}

	/**
	 * Значения по умолчанию при первой загрузке виджета.
	 *
	 * @return array{month: int, region: int} Первый товар в каждом списке или 0, если списки пусты.
	 */
	public function get_defaults() {
		return array(
			'month'  => 0,
			'region' => 0,
		);
	}

	/**
	 * Проверяет, что переданные product_id есть в каталоге.
	 * Если нет — подставляет значения из get_defaults().
	 *
	 * @param int $region_id product_id региона (из GET/POST/REST).
	 * @param int $month_id  product_id месяца.
	 * @return array{month: int, region: int} Безопасная пара id для дальнейшей работы.
	 */
	public function resolve_selection( $region_id, $month_id ) {
		$defaults  = $this->get_defaults();
		$months    = $this->get_months();
		$regions   = $this->get_regions();
		$region_id = (int) $region_id;
		$month_id  = (int) $month_id;

		// Регион: пустой id или нет в списке → дефолтный регион.
		if ( $region_id <= 0 || ! isset( $regions[ $region_id ] ) ) {
			$region_id = (int) $defaults['region'];
		}
		// Месяц: пустой id или нет в списке → дефолтный месяц.
		if ( $month_id <= 0 || ! isset( $months[ $month_id ] ) ) {
			$month_id = (int) $defaults['month'];
		}

		return array(
			'region' => $region_id,
			'month'  => $month_id,
		);
	}

	/**
	 * Названия месяца и региона по product_id (для отправки в Laravel).
	 *
	 * @param int $month_id
	 * @param int $region_id
	 * @return array{month_name: string, region_name: string}
	 */
	public function get_selection_names( $month_id, $region_id ) {
		$months    = $this->get_months();
		$regions   = $this->get_regions();
		$month_id  = (int) $month_id;
		$region_id = (int) $region_id;

		return array(
			'month_name'  => isset( $months[ $month_id ] ) ? (string) $months[ $month_id ] : '',
			'region_name' => isset( $regions[ $region_id ] ) ? (string) $regions[ $region_id ] : '',
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
	 * @param int $month_id
	 * @param int $region_id
	 * @return array{month_name: string, region_name: string, month: int, region: int, language: string}
	 */
	public function build_laravel_payload( $month_id, $region_id ) {
		$month_id  = (int) $month_id;
		$region_id = (int) $region_id;
		$names     = $this->get_selection_names( $month_id, $region_id );

		return array(
			'month_name'  => $names['month_name'],
			'region_name' => $names['region_name'],
			'month'       => $month_id,
			'region'      => $region_id,
			'language'    => $this->get_current_language_code(),
		);
	}

	/**
	 * Товары одной категории каталога — основа для get_months() и get_regions().
	 *
	 * @param int $category_id     ID категории из config.php (месяцы или регионы).
	 * @param int $language_id     0 = language_id из config (сейчас 1) или Polylang, если в config 0.
	 * @return array<int, string>  product_id => название на выбранном языке.
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

		// Только включённые товары, привязанные к категории, с названием на языке.
		$sql = $this->wpdb->prepare(
			"SELECT p.product_id, d.name
			FROM `{$p_table}` p
			INNER JOIN `{$p2c}` p2c ON p.product_id = p2c.product_id AND p2c.category_id = %d
			LEFT JOIN `{$d_table}` d ON p.product_id = d.product_id AND d.language_id = %d
			WHERE p.status = 1
			ORDER BY p.sort_order ASC, d.name ASC",
			$category_id,
			$language_id
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$out  = array();

		foreach ( $rows as $row ) {
			$id         = (int) $row->product_id;
			$out[ $id ] = $row->name ? (string) $row->name : '#' . $id;
		}

		return $out;
	}

}

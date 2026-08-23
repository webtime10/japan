<?php
/**
 * Budget — Model.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Budget_Model extends AI_Calculator_Model {

	/** @var int */
	private $manufacturer_id;

	/** @var int 0 = язык из Polylang */
	private $language_id;

	public function __construct() {
		parent::__construct();

		$config = require AI_CALCULATOR_FRONT_PATH . 'calculators/budget/config.php';

		$this->manufacturer_id = (int) ( $config['manufacturer_id'] ?? 0 );
		$this->language_id     = (int) ( $config['language_id'] ?? 0 );
	}

	/**
	 * @return int
	 */
	public function get_manufacturer_id() {
		return $this->manufacturer_id;
	}

	/**
	 * language_id каталога: из config или текущий Polylang.
	 *
	 * @return int
	 */
	public function get_language_id() {
		return $this->language_id > 0 ? $this->language_id : $this->get_current_language_id();
	}

	/**
	 * Slug языка Polylang (en, ar, he …).
	 *
	 * @return string
	 */
	public function get_polylang_slug() {
		$pll = $this->get_polylang_current_language();

		return '' !== $pll['slug'] ? $pll['slug'] : ai_calculator_polylang_slug();
	}

	/**
	 * Карточки каталога: одна категория = одна карточка калькулятора.
	 * block1…block6 — заголовки пунктов на карточке (из товаров по sort_order).
	 * products — только id и название (варианты выбора).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_catalog_cards_data() {
		$manufacturer_id = $this->get_manufacturer_id();
		$language_id     = $this->get_language_id();

		if ( $manufacturer_id <= 0 ) {
			return array();
		}

		$c_table = $this->table( 'category' );
		$cd_table = $this->table( 'category_description' );

		$categories = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT c.category_id, c.sort_order, d.name AS category_title
				FROM `{$c_table}` c
				LEFT JOIN `{$cd_table}` d ON c.category_id = d.category_id AND d.language_id = %d
				WHERE c.manufacturer_id = %d AND c.status = 1
				ORDER BY c.sort_order ASC, c.category_id ASC",
				$language_id,
				$manufacturer_id
			)
		);

		if ( empty( $categories ) ) {
			return array();
		}

		$result_data = array();
		$sort        = 0;

		foreach ( $categories as $category ) {
			++$sort;
			$category_id = (int) $category->category_id;
			$products    = $this->get_category_products_rows( $category_id, $language_id );

			$card = array(
				'category_id'    => $category_id,
				'category_title' => $category->category_title ? (string) $category->category_title : '#' . $category_id,
				'sort'           => $sort,
				'block1'         => '',
				'block2'         => '',
				'block3'         => '',
				'block4'         => '',
				'block5'         => '',
				'block6'         => '',
				'products'       => array(),
			);

			$this->fill_card_headings_and_products( $card, $products );

			$result_data[] = $card;
		}

		return $result_data;
	}

	/**
	 * @param int $category_id
	 * @param int $language_id
	 * @return array<int, object>
	 */
	private function get_category_products_rows( $category_id, $language_id ) {
		$p_table  = $this->table( 'product' );
		$pd_table = $this->table( 'product_description' );
		$p2c      = $this->table( 'product_to_category' );

		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT p.product_id, p.sort_order,
					d.name, d.description,
					d.block1, d.block2, d.block3, d.block4, d.block5, d.block6
				FROM `{$p2c}` p2c
				INNER JOIN `{$p_table}` p ON p.product_id = p2c.product_id AND p.status = 1
				LEFT JOIN `{$pd_table}` d ON d.product_id = p.product_id AND d.language_id = %d
				WHERE p2c.category_id = %d
				ORDER BY p.sort_order ASC, p.product_id ASC",
				$language_id,
				$category_id
			)
		);

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Заголовки карточки (block1…block6) и список товаров-пунктов.
	 *
	 * @param array<string, mixed> $card
	 * @param array<int, object>   $rows
	 */
	private function fill_card_headings_and_products( array &$card, array $rows ) {
		if ( empty( $rows ) ) {
			return;
		}

		// block1…block6 карточки = block1…block6 товара (1:1), не сдвиг по пустым слотам.
		for ( $n = 1; $n <= 6; ++$n ) {
			$key = 'block' . $n;
			foreach ( $rows as $row ) {
				$value = isset( $row->$key ) ? trim( (string) $row->$key ) : '';
				if ( '' !== $value ) {
					$card[ $key ] = $value;
					break;
				}
			}
		}

		foreach ( $rows as $row ) {
			$product_id = (int) $row->product_id;

			$card['products'][] = array(
				'product_id'   => $product_id,
				'product_name' => $row->name ? (string) $row->name : '#' . $product_id,
				'name'         => $row->name ? (string) $row->name : '#' . $product_id,
				'age_from'     => isset( $row->block5 ) ? trim( (string) $row->block5 ) : '',
				'age_to'       => isset( $row->block6 ) ? trim( (string) $row->block6 ) : '',
			);
		}
	}
}

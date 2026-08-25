<?php
/**
 * Your Ideal Region — Model.
 *
 * Категория = шаг квиза, товары = варианты ответа (название + картинка).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Ideal_Region_Model extends AI_Calculator_Model {

	/** @var int */
	private $manufacturer_id;

	/** @var int */
	private $language_id;

	public function __construct() {
		parent::__construct();

		$config = require AI_CALCULATOR_FRONT_PATH . 'calculators/ideal-region/config.php';

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
	 * @return int
	 */
	public function get_language_id() {
		return $this->language_id > 0 ? $this->language_id : $this->get_current_language_id();
	}

	/**
	 * Карточки каталога для шагов квиза.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_catalog_cards_data() {
		$manufacturer_id = $this->get_manufacturer_id();
		$language_id     = $this->get_language_id();

		if ( $manufacturer_id <= 0 ) {
			return array();
		}

		$c_table  = $this->table( 'category' );
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
				'dop1'           => '',
				'max_answers'    => self::max_answers_for_step( $sort ),
				'block1'         => '',
				'block2'         => '',
				'block3'         => '',
				'block4'         => '',
				'block5'         => '',
				'block6_1'       => '',
				'options'        => array(),
				'products'       => array(),
			);

			$this->fill_card_headings_and_products( $card, $products );

			if ( '' === trim( (string) $card['dop1'] ) ) {
				$card['dop1'] = self::default_dop1_for_step( $sort );
			}

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
				"SELECT p.product_id, p.sort_order, p.image, p.image2, p.image3, p.image4, p.image5, p.image6,
					d.name, d.description,
					d.block1, d.block2, d.block3, d.block4, d.block5, d.block7, d.dop1
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
	 * Один товар на шаг: Name = вопрос, Блок1–5 + Блок 6_1 + Фото1–6 = варианты.
	 *
	 * @param array<string, mixed> $card
	 * @param array<int, object>   $rows
	 */
	private function fill_card_headings_and_products( array &$card, array $rows ) {
		if ( empty( $rows ) ) {
			return;
		}

		// Берём только первый товар категории — лишние товары не смешиваем в карточки.
		$row          = $rows[0];
		$product_id   = (int) $row->product_id;
		$product_name = $row->name ? (string) $row->name : '#' . $product_id;
		// Варианты: block1–5 + block7 (в форме «Блок 6_1»), фото image…image6.
		$block_keys = array( 'block1', 'block2', 'block3', 'block4', 'block5', 'block7' );
		$image_keys = array( 'image', 'image2', 'image3', 'image4', 'image5', 'image6' );
		$slots      = array();

		for ( $n = 1; $n <= 5; ++$n ) {
			$key   = 'block' . $n;
			$value = isset( $row->$key ) ? trim( (string) $row->$key ) : '';
			if ( '' !== $value ) {
				$card[ $key ] = $value;
			}
		}
		$block6_1 = isset( $row->block7 ) ? trim( (string) $row->block7 ) : '';
		if ( '' !== $block6_1 ) {
			$card['block6_1'] = $block6_1;
		}

		$dop1 = isset( $row->dop1 ) ? trim( (string) $row->dop1 ) : '';
		if ( '' !== $dop1 ) {
			$card['dop1'] = $dop1;
		} else {
			$card['dop1'] = self::default_dop1_for_step( (int) $card['sort'] );
		}

		for ( $n = 1; $n <= 6; $n++ ) {
			$block_key = $block_keys[ $n - 1 ];
			$image_key = $image_keys[ $n - 1 ];
			$label     = isset( $row->$block_key ) ? trim( (string) $row->$block_key ) : '';
			$image     = ! empty( $row->$image_key ) ? esc_url_raw( trim( (string) $row->$image_key ) ) : '';

			if ( '' === $label && '' === $image ) {
				continue;
			}

			if ( '' === $label ) {
				$label = $product_name . ' ' . $n;
			}

			$slots[] = array(
				'slot'       => $n,
				'label'      => $label,
				'image'      => $image,
				'value'      => $product_id . '-' . $n,
				'product_id' => $product_id,
			);
		}

		$card['options']  = $slots;
		$card['products'] = array(
			array(
				'product_id'   => $product_id,
				'product_name' => $product_name,
				'name'         => $product_name,
				'value'        => (string) $product_id,
				'label'        => $product_name,
				'image'        => ! empty( $slots[0]['image'] ) ? $slots[0]['image'] : '',
				'dop1'         => isset( $card['dop1'] ) ? (string) $card['dop1'] : '',
				'slots'        => $slots,
			),
		);
	}

	/**
	 * Лимит ответов по номеру шага: 1–4 и 7 → 1; 5–6 → 2; 8 → 3.
	 *
	 * @param int $step
	 * @return int
	 */
	public static function max_answers_for_step( $step ) {
		$step = (int) $step;
		if ( 5 === $step || 6 === $step ) {
			return 2;
		}
		if ( 8 === $step ) {
			return 3;
		}

		return 1;
	}

	/**
	 * Подпись по умолчанию, если dop1 в товаре пустой.
	 *
	 * @param int $step
	 * @return string
	 */
	public static function default_dop1_for_step( $step ) {
		$max = self::max_answers_for_step( $step );
		if ( 2 === $max ) {
			return 'До двух вариантов ответа';
		}
		if ( 3 === $max ) {
			return 'До трёх вариантов ответа';
		}

		return 'Один вариант ответа';
	}
}

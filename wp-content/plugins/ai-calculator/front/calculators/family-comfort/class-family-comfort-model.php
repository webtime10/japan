<?php
/**
 * Family Comfort — Model.
 *
 * Categories = interests, products = cities, attributes = age groups.
 * Card content from product fields (image, description, blocks).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Family_Comfort_Model extends AI_Calculator_Model {

	/** @var int */
	private $manufacturer_id;

	/** @var int */
	private $language_id;

	public function __construct() {
		parent::__construct();

		$config = require AI_CALCULATOR_FRONT_PATH . 'calculators/family-comfort/config.php';

		$this->manufacturer_id = function_exists( 'ai_calculator_get_family_comfort_manufacturer_id' )
			? ai_calculator_get_family_comfort_manufacturer_id()
			: (int) ( $config['manufacturer_id'] ?? 0 );
		$this->language_id     = (int) ( $config['language_id'] ?? 0 );
	}

	/**
	 * @return int
	 */
	public function get_language_id() {
		return $this->language_id > 0 ? $this->language_id : $this->get_current_language_id();
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_view_data() {
		$language_id     = $this->get_language_id();
		$manufacturer_id = $this->manufacturer_id;
		$root_id         = function_exists( 'ai_calculator_get_family_comfort_root_category_id' )
			? ai_calculator_get_family_comfort_root_category_id()
			: 0;

		$attribute_options = array();
		$category_options  = array();
		$cards             = array();

		if ( $manufacturer_id <= 0 || $language_id <= 0 ) {
			return compact( 'attribute_options', 'category_options', 'cards', 'root_id' );
		}

		$attributes = $this->get_attributes( $language_id );
		$categories = $this->get_categories( $manufacturer_id, $language_id, $root_id );
		$products   = $this->get_products( $manufacturer_id, $language_id, $root_id );

		foreach ( $attributes as $row ) {
			$attribute_options[ (string) $row['attribute_id'] ] = $row['title'];
		}

		foreach ( $categories as $row ) {
			$category_options[ (string) $row['category_id'] ] = $row['title'];
		}

		if ( empty( $products ) ) {
			return array(
				'attribute_options' => $attribute_options,
				'category_options'  => $category_options,
				'cards'             => $cards,
				'root_id'           => $root_id,
			);
		}

		$product_ids   = array_column( $products, 'product_id' );
		$attribute_map = $this->get_product_attribute_ids_map( $product_ids );

		foreach ( $products as $product ) {
			$product_id    = (int) $product['product_id'];
			$attribute_ids = isset( $attribute_map[ $product_id ] ) ? $attribute_map[ $product_id ] : array();
			if ( empty( $attribute_ids ) ) {
				continue;
			}
			$cards[] = $this->build_card( $product, (int) $product['category_id'], $attribute_ids );
		}

		return array(
			'attribute_options' => $attribute_options,
			'category_options'  => $category_options,
			'cards'             => $cards,
			'root_id'           => $root_id,
		);
	}

	/**
	 * @param array<string, mixed> $product
	 * @param int                  $category_id
	 * @param array<int>           $attribute_ids
	 * @return array<string, mixed>
	 */
	private function build_card( $product, $category_id, $attribute_ids ) {
		$product_id = (int) $product['product_id'];
		$title      = isset( $product['name'] ) ? trim( (string) $product['name'] ) : '';
		$subtitle   = isset( $product['block6'] ) ? trim( (string) $product['block6'] ) : '';

		$tags = array();
		for ( $i = 2; $i <= 8; $i++ ) {
			if ( 6 === $i ) {
				continue;
			}
			$key = 'block' . $i;
			if ( ! empty( $product[ $key ] ) ) {
				$tags[] = (string) $product[ $key ];
			}
		}

		$text   = isset( $product['description'] ) ? (string) $product['description'] : '';
		$teaser = wp_trim_words( wp_strip_all_tags( $text ), 28, '…' );
		$rating = $this->get_card_rating( $product_id );

		return array(
			'product_id'      => $product_id,
			'category_id'     => $category_id,
			'attribute_ids'   => array_values( array_map( 'intval', $attribute_ids ) ),
			'title'           => $title,
			'subtitle'        => $subtitle,
			'text'            => $teaser,
			'content'         => $text,
			'image'           => isset( $product['image'] ) ? (string) $product['image'] : '',
			'url'             => isset( $product['block1'] ) ? (string) $product['block1'] : '',
			'tags'            => $tags,
			'rating'          => $rating,
			'rating_percent'  => (int) round( ( $rating / 5 ) * 100 ),
		);
	}

	/**
	 * Стабильный рейтинг 4.0–5.0 для карточки товара.
	 *
	 * @param int $product_id
	 * @return float
	 */
	private function get_card_rating( $product_id ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return 4.5;
		}

		$tenths = 40 + ( ( $product_id * 17 + 13 ) % 11 );

		return round( $tenths / 10, 1 );
	}

	/**
	 * @param int $language_id
	 * @return array<int, array{attribute_id: int, title: string}>
	 */
	private function get_attributes( $language_id ) {
		$a_table  = $this->table( 'attribute' );
		$ad_table = $this->table( 'attribute_description' );

		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT a.attribute_id, d.name
				FROM `{$a_table}` a
				LEFT JOIN `{$ad_table}` d ON a.attribute_id = d.attribute_id AND d.language_id = %d
				ORDER BY a.sort_order ASC, a.attribute_id ASC",
				$language_id
			)
		);

		$out = array();
		foreach ( (array) $rows as $row ) {
			$id = (int) $row->attribute_id;
			if ( $id <= 0 ) {
				continue;
			}
			$out[] = array(
				'attribute_id' => $id,
				'title'        => $row->name ? (string) $row->name : '#' . $id,
			);
		}

		return $out;
	}

	/**
	 * @param int $manufacturer_id
	 * @param int $language_id
	 * @param int $root_id
	 * @return array<int, array{category_id: int, title: string}>
	 */
	private function get_categories( $manufacturer_id, $language_id, $root_id = 0 ) {
		$c_table  = $this->table( 'category' );
		$cd_table = $this->table( 'category_description' );
		$root_id  = (int) $root_id;

		if ( $root_id <= 0 ) {
			$root_id = function_exists( 'ai_calculator_get_family_comfort_root_category_id' )
				? ai_calculator_get_family_comfort_root_category_id()
				: 0;
		}

		$sql = "SELECT c.category_id, d.name
			FROM `{$c_table}` c
			LEFT JOIN `{$cd_table}` d ON c.category_id = d.category_id AND d.language_id = %d
			WHERE c.manufacturer_id = %d AND c.status = 1";
		$params = array( $language_id, $manufacturer_id );

		if ( $root_id > 0 ) {
			$sql     .= ' AND c.parent_id = %d';
			$params[] = $root_id;
		} else {
			$sql .= ' AND c.parent_id > 0';
		}

		$sql .= ' ORDER BY c.sort_order ASC, c.category_id ASC';

		$rows = $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$out = array();
		foreach ( (array) $rows as $row ) {
			$id = (int) $row->category_id;
			if ( $id <= 0 ) {
				continue;
			}
			$out[] = array(
				'category_id' => $id,
				'title'       => $row->name ? (string) $row->name : '#' . $id,
			);
		}

		return $out;
	}

	/**
	 * @param int $manufacturer_id
	 * @param int $language_id
	 * @param int $root_id
	 * @return array<int, array<string, mixed>>
	 */
	private function get_products( $manufacturer_id, $language_id, $root_id = 0 ) {
		$p_table  = $this->table( 'product' );
		$pd_table = $this->table( 'product_description' );
		$p2c      = $this->table( 'product_to_category' );
		$c_table  = $this->table( 'category' );
		$root_id  = (int) $root_id;

		if ( $root_id <= 0 ) {
			$root_id = function_exists( 'ai_calculator_get_family_comfort_root_category_id' )
				? ai_calculator_get_family_comfort_root_category_id()
				: 0;
		}

		$sql = "SELECT p.product_id, p.image, p2c.category_id,
				d.name, d.description, d.block1, d.block2, d.block3, d.block4, d.block5, d.block6, d.block7, d.block8
			FROM `{$p_table}` p
			INNER JOIN `{$p2c}` p2c ON p2c.product_id = p.product_id
			INNER JOIN `{$c_table}` c ON c.category_id = p2c.category_id AND c.manufacturer_id = %d AND c.status = 1
			LEFT JOIN `{$pd_table}` d ON d.product_id = p.product_id AND d.language_id = %d
			WHERE p.manufacturer_id = %d AND p.status = 1";
		$params = array( $manufacturer_id, $language_id, $manufacturer_id );

		if ( $root_id > 0 ) {
			$sql     .= ' AND c.parent_id = %d';
			$params[] = $root_id;
		} else {
			$sql .= ' AND c.parent_id > 0';
		}

		$sql .= ' ORDER BY p.sort_order ASC, p.product_id ASC';

		$rows = $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$out = array();
		foreach ( (array) $rows as $row ) {
			$id = (int) $row->product_id;
			if ( $id <= 0 ) {
				continue;
			}
			$out[] = array(
				'product_id'  => $id,
				'category_id' => (int) $row->category_id,
				'image'       => isset( $row->image ) ? (string) $row->image : '',
				'name'        => $row->name ? (string) $row->name : '#' . $id,
				'description' => isset( $row->description ) ? (string) $row->description : '',
				'block1'      => isset( $row->block1 ) ? (string) $row->block1 : '',
				'block2'      => isset( $row->block2 ) ? (string) $row->block2 : '',
				'block3'      => isset( $row->block3 ) ? (string) $row->block3 : '',
				'block4'      => isset( $row->block4 ) ? (string) $row->block4 : '',
				'block5'      => isset( $row->block5 ) ? (string) $row->block5 : '',
				'block6'      => isset( $row->block6 ) ? (string) $row->block6 : '',
				'block7'      => isset( $row->block7 ) ? (string) $row->block7 : '',
				'block8'      => isset( $row->block8 ) ? (string) $row->block8 : '',
			);
		}

		return $out;
	}

	/**
	 * @param array<int> $product_ids
	 * @return array<int, array<int>> product_id => attribute_id[]
	 */
	private function get_product_attribute_ids_map( $product_ids ) {
		$product_ids = array_values( array_filter( array_map( 'intval', (array) $product_ids ) ) );
		if ( empty( $product_ids ) ) {
			return array();
		}

		$pa_table     = $this->table( 'product_attribute' );
		$placeholders = implode( ',', array_fill( 0, count( $product_ids ), '%d' ) );

		$sql  = $this->wpdb->prepare(
			"SELECT DISTINCT product_id, attribute_id FROM `{$pa_table}`
			WHERE product_id IN ({$placeholders})",
			$product_ids
		);
		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$out = array();
		foreach ( (array) $rows as $row ) {
			$product_id   = (int) $row->product_id;
			$attribute_id = (int) $row->attribute_id;
			if ( $product_id <= 0 || $attribute_id <= 0 ) {
				continue;
			}
			if ( ! isset( $out[ $product_id ] ) ) {
				$out[ $product_id ] = array();
			}
			$out[ $product_id ][] = $attribute_id;
		}

		return $out;
	}
}

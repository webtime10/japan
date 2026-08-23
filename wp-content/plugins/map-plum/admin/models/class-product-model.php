<?php
/**
 * Product model.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Product_Model extends Map_Plum_Model {

	/** @var array<int, string>|null */
	private $product_columns = null;

	/** @var array<int, string>|null */
	private $product_desc_columns = null;

	/**
	 * @return array<int, string>
	 */
	private function get_product_columns() {
		if ( null === $this->product_columns ) {
			$table                 = $this->table( 'product' );
			$this->product_columns = $this->wpdb->get_col( "SHOW COLUMNS FROM `{$table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
		return $this->product_columns;
	}

	/**
	 * @return array<int, string>
	 */
	private function get_product_desc_columns() {
		if ( null === $this->product_desc_columns ) {
			$table                      = $this->table( 'product_description' );
			$this->product_desc_columns = $this->wpdb->get_col( "SHOW COLUMNS FROM `{$table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
		return $this->product_desc_columns;
	}

	/**
	 * @param array<string, mixed> $main
	 * @return array{0: array<string, mixed>, 1: array<int, string>}
	 */
	private function filter_product_main( $main ) {
		$format_map = array(
			'manufacturer_id' => '%d',
			'image'           => '%s',
			'image_id'        => '%d',
			'polylink'        => '%s',
			'price'           => '%f',
			'sort_order'      => '%d',
			'status'          => '%d',
			'model'           => '%s',
			'sku'             => '%s',
			'quantity'        => '%d',
		);
		$columns    = $this->get_product_columns();
		$data       = array();
		$formats    = array();

		foreach ( $format_map as $key => $format ) {
			if ( ! in_array( $key, $columns, true ) || ! array_key_exists( $key, $main ) ) {
				continue;
			}
			$data[ $key ] = $main[ $key ];
			$formats[]    = $format;
		}

		return array( $data, $formats );
	}

	/**
	 * @param int   $language_id
	 * @param int   $manufacturer_id 0 — все регионы.
	 * @param int   $category_id     0 — все категории.
	 * @return array<int, object>
	 */
	public function get_list( $language_id, $manufacturer_id = 0, $category_id = 0 ) {
		$manufacturer_desc = $this->table( 'manufacturer_description' );
		$category_desc     = $this->table( 'category_description' );
		$p2c_table         = $this->table( 'product_to_category' );

		$sql = "SELECT p.*, pd.name,
				man.manufacturer_name,
				GROUP_CONCAT(DISTINCT cd.name ORDER BY cd.name SEPARATOR ', ') AS category_names
			FROM {$this->table( 'product' )} p
			LEFT JOIN {$this->table( 'product_description' )} pd
				ON p.product_id = pd.product_id AND pd.language_id = %d
			LEFT JOIN (
				SELECT manufacturer_id,
					SUBSTRING_INDEX(
						GROUP_CONCAT(name ORDER BY (language_id = %d) DESC, language_id ASC SEPARATOR '\n'),
						'\n',
						1
					) AS manufacturer_name
				FROM `{$manufacturer_desc}`
				GROUP BY manufacturer_id
			) man ON man.manufacturer_id = p.manufacturer_id
			LEFT JOIN `{$p2c_table}` p2c
				ON p.product_id = p2c.product_id
			LEFT JOIN (
				SELECT category_id,
					SUBSTRING_INDEX(
						GROUP_CONCAT(name ORDER BY (language_id = %d) DESC, language_id ASC SEPARATOR '\n'),
						'\n',
						1
					) AS name
				FROM `{$category_desc}`
				GROUP BY category_id
			) cd ON p2c.category_id = cd.category_id
			WHERE 1=1";

		$params = array( $language_id, $language_id, $language_id );

		if ( $manufacturer_id > 0 ) {
			$sql     .= ' AND p.manufacturer_id = %d';
			$params[] = $manufacturer_id;
		}

		if ( $category_id > 0 ) {
			$sql     .= " AND EXISTS (
				SELECT 1 FROM `{$p2c_table}` p2c_f
				WHERE p2c_f.product_id = p.product_id AND p2c_f.category_id = %d
			)";
			$params[] = $category_id;
		}

		$sql .= ' GROUP BY p.product_id ORDER BY p.product_id DESC';

		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
	}

	/**
	 * @param int $product_id
	 */
	public function get_product( $product_id ) {
		return $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'product' )} WHERE product_id = %d",
				$product_id
			)
		);
	}

	/**
	 * @param int $product_id
	 * @return array<int, object>
	 */
	public function get_descriptions( $product_id ) {
		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'product_description' )} WHERE product_id = %d",
				$product_id
			)
		);
		$out = array();
		foreach ( $rows as $row ) {
			$out[ (int) $row->language_id ] = $row;
		}
		return $out;
	}

	/**
	 * @param int $product_id
	 * @return array<int>
	 */
	public function get_category_ids( $product_id ) {
		$rows = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT category_id FROM {$this->table( 'product_to_category' )} WHERE product_id = %d",
				$product_id
			)
		);
		return array_map( 'intval', $rows );
	}

	/**
	 * @param array<string, mixed>              $main
	 * @param array<int, array<string, string>> $descriptions
	 * @param array<int>                        $category_ids
	 * @return int
	 */
	public function add( $main, $descriptions, $category_ids ) {
		list( $product_data, $product_formats ) = $this->filter_product_main( $main );
		$this->wpdb->insert( $this->table( 'product' ), $product_data, $product_formats );
		$id = (int) $this->wpdb->insert_id;
		$this->save_descriptions( $id, $descriptions );
		$this->save_categories( $id, $category_ids );
		return $id;
	}

	/**
	 * @param int                               $id
	 * @param array<string, mixed>              $main
	 * @param array<int, array<string, string>> $descriptions
	 * @param array<int>                        $category_ids
	 */
	public function edit( $id, $main, $descriptions, $category_ids ) {
		list( $product_data, $product_formats ) = $this->filter_product_main( $main );
		$this->wpdb->update(
			$this->table( 'product' ),
			$product_data,
			array( 'product_id' => $id ),
			$product_formats,
			array( '%d' )
		);
		$this->wpdb->delete( $this->table( 'product_description' ), array( 'product_id' => $id ), array( '%d' ) );
		$this->save_descriptions( $id, $descriptions );
		$this->wpdb->delete( $this->table( 'product_to_category' ), array( 'product_id' => $id ), array( '%d' ) );
		$this->save_categories( $id, $category_ids );
	}

	/**
	 * @param int                               $id
	 * @param array<int, array<string, string>> $descriptions
	 */
	private function save_descriptions( $id, $descriptions ) {
		$columns    = $this->get_product_desc_columns();
		$format_map = array(
			'product_id'       => '%d',
			'language_id'      => '%d',
			'name'             => '%s',
			'description'      => '%s',
			'meta_title'       => '%s',
			'meta_description' => '%s',
			'meta_keyword'     => '%s',
		);

		foreach ( $descriptions as $language_id => $row ) {
			if ( empty( $row['name'] ) ) {
				continue;
			}

			$desc_data = array(
				'product_id'       => $id,
				'language_id'      => (int) $language_id,
				'name'             => isset( $row['name'] ) ? $row['name'] : '',
				'description'      => isset( $row['description'] ) ? $row['description'] : '',
				'meta_title'       => isset( $row['meta_title'] ) ? $row['meta_title'] : '',
				'meta_description' => isset( $row['meta_description'] ) ? $row['meta_description'] : '',
				'meta_keyword'     => isset( $row['meta_keyword'] ) ? $row['meta_keyword'] : '',
			);

			$desc_data    = array_intersect_key( $desc_data, array_flip( $columns ) );
			$desc_formats = array();
			foreach ( $desc_data as $key => $unused ) {
				$desc_formats[] = isset( $format_map[ $key ] ) ? $format_map[ $key ] : '%s';
			}

			$this->wpdb->insert( $this->table( 'product_description' ), $desc_data, $desc_formats );
		}
	}

	/**
	 * @param int        $id
	 * @param array<int> $category_ids
	 */
	private function save_categories( $id, $category_ids ) {
		foreach ( $category_ids as $cid ) {
			$cid = (int) $cid;
			if ( $cid <= 0 ) {
				continue;
			}
			$this->wpdb->insert(
				$this->table( 'product_to_category' ),
				array(
					'product_id'  => $id,
					'category_id' => $cid,
				),
				array( '%d', '%d' )
			);
		}
	}

	/**
	 * @param int $product_id
	 * @return array<int>
	 */
	public function get_marker_ids( $product_id ) {
		$rows = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT marker_id FROM {$this->table( 'product_to_marker' )} WHERE product_id = %d",
				$product_id
			)
		);
		return array_map( 'intval', $rows );
	}

	/**
	 * @param int        $product_id
	 * @param array<int> $marker_ids
	 */
	public function save_markers( $product_id, $marker_ids ) {
		$this->wpdb->delete( $this->table( 'product_to_marker' ), array( 'product_id' => $product_id ), array( '%d' ) );
		$marker_ids = array_unique( array_filter( array_map( 'intval', $marker_ids ) ) );
		foreach ( $marker_ids as $marker_id ) {
			if ( $marker_id <= 0 ) {
				continue;
			}
			$this->wpdb->insert(
				$this->table( 'product_to_marker' ),
				array(
					'product_id' => $product_id,
					'marker_id'  => $marker_id,
				),
				array( '%d', '%d' )
			);
		}
	}

	/**
	 * @param array<int> $ids
	 */
	public function delete( $ids ) {
		foreach ( $ids as $id ) {
			$id = (int) $id;
			if ( $id <= 0 ) {
				continue;
			}
			$this->wpdb->delete( $this->table( 'product_to_category' ), array( 'product_id' => $id ), array( '%d' ) );
			$this->wpdb->delete( $this->table( 'product_to_marker' ), array( 'product_id' => $id ), array( '%d' ) );
			$this->wpdb->delete( $this->table( 'product_description' ), array( 'product_id' => $id ), array( '%d' ) );
			$this->wpdb->delete( $this->table( 'product' ), array( 'product_id' => $id ), array( '%d' ) );
		}
	}
}

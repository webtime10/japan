<?php
/**
 * Product model.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Product_Model extends AI_Calculator_Model {

	const PER_PAGE = 20;

	/**
	 * @param array<string, mixed> $filters language_id, category_id, page.
	 * @return array<int, object>
	 */
	public function get_list( $filters ) {
		$language_id     = isset( $filters['language_id'] ) ? (int) $filters['language_id'] : 0;
		$category_id     = isset( $filters['category_id'] ) ? (int) $filters['category_id'] : 0;
		$manufacturer_id = isset( $filters['manufacturer_id'] ) ? (int) $filters['manufacturer_id'] : 0;
		$page            = max( 1, isset( $filters['page'] ) ? (int) $filters['page'] : 1 );
		$offset          = ( $page - 1 ) * self::PER_PAGE;

		$p_table  = $this->table( 'product' );
		$d_table  = $this->table( 'product_description' );
		$p2c      = $this->table( 'product_to_category' );
		$cd_table = $this->table( 'category_description' );
		$md_table = $this->table( 'manufacturer_description' );

		$join  = '';
		$where = '1=1';
		$args  = array();

		if ( $category_id > 0 ) {
			$join  .= " INNER JOIN `{$p2c}` p2c ON p.product_id = p2c.product_id ";
			$where .= ' AND p2c.category_id = %d';
			$args[] = $category_id;
		}

		if ( $manufacturer_id > 0 ) {
			$where .= ' AND p.manufacturer_id = %d';
			$args[] = $manufacturer_id;
		}

		$cat_names_join = "LEFT JOIN (
			SELECT p2c_all.product_id,
				GROUP_CONCAT(DISTINCT cd.name ORDER BY cd.name SEPARATOR ', ') AS category_name
			FROM `{$p2c}` p2c_all
			INNER JOIN `{$cd_table}` cd ON p2c_all.category_id = cd.category_id AND cd.language_id = %d
			GROUP BY p2c_all.product_id
		) cat_names ON cat_names.product_id = p.product_id";

		$prepare_args   = array( $language_id, $language_id, $language_id );
		$prepare_args   = array_merge( $prepare_args, $args );
		$prepare_args[] = self::PER_PAGE;
		$prepare_args[] = $offset;
		$args           = $prepare_args;

		$sql = "SELECT p.*, d.name, COALESCE(NULLIF(d.block6, ''), d.description) AS russian_name, md.name AS manufacturer_name, cat_names.category_name
			FROM `{$p_table}` p
			{$join}
			{$cat_names_join}
			LEFT JOIN `{$d_table}` d ON p.product_id = d.product_id AND d.language_id = %d
			LEFT JOIN `{$md_table}` md ON p.manufacturer_id = md.manufacturer_id AND md.language_id = %d
			WHERE {$where}
			ORDER BY p.sort_order ASC, d.name ASC
			LIMIT %d OFFSET %d";

		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $args ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * @param array<string, mixed> $filters
	 */
	public function count_list( $filters ) {
		$category_id     = isset( $filters['category_id'] ) ? (int) $filters['category_id'] : 0;
		$manufacturer_id = isset( $filters['manufacturer_id'] ) ? (int) $filters['manufacturer_id'] : 0;
		$p_table         = $this->table( 'product' );
		$p2c             = $this->table( 'product_to_category' );

		$join  = '';
		$where = '1=1';
		$args  = array();

		if ( $category_id > 0 ) {
			$join  = " INNER JOIN `{$p2c}` p2c ON p.product_id = p2c.product_id ";
			$where .= ' AND p2c.category_id = %d';
			$args[] = $category_id;
		}

		if ( $manufacturer_id > 0 ) {
			$where .= ' AND p.manufacturer_id = %d';
			$args[] = $manufacturer_id;
		}

		if ( empty( $args ) ) {
			return (int) $this->wpdb->get_var( "SELECT COUNT(*) FROM `{$p_table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$sql = "SELECT COUNT(DISTINCT p.product_id) FROM `{$p_table}` p {$join} WHERE {$where}";
		return (int) $this->wpdb->get_var( $this->wpdb->prepare( $sql, $args ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Язык каталога на фронте (Polylang или 1).
	 *
	 * @return int
	 */
	public function get_catalog_language_id() {
		$lang_id = $this->get_current_language_id();
		return $lang_id > 0 ? $lang_id : 1;
	}

	/**
	 * @param int $product_id
	 * @return array{product: object|null, descriptions: array<int, object>, category_ids: array<int>, related_product_ids: array<int>}
	 */
	public function get( $product_id ) {
		$product_id = (int) $product_id;
		$p_table    = $this->table( 'product' );
		$d_table    = $this->table( 'product_description' );
		$p2c        = $this->table( 'product_to_category' );

		$product = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$p_table}` WHERE product_id = %d",
				$product_id
			)
		);

		$descriptions = array();
		$category_ids = array();

		if ( $product ) {
			$rows = $this->wpdb->get_results(
				$this->wpdb->prepare(
					"SELECT * FROM `{$d_table}` WHERE product_id = %d",
					$product_id
				)
			);
			foreach ( $rows as $row ) {
				$descriptions[ (int) $row->language_id ] = $row;
			}

			$cat_rows = $this->wpdb->get_col(
				$this->wpdb->prepare(
					"SELECT category_id FROM `{$p2c}` WHERE product_id = %d",
					$product_id
				)
			);
			$category_ids = array_map( 'intval', $cat_rows );
		}

		return array(
			'product'              => $product,
			'descriptions'         => $descriptions,
			'category_ids'         => $category_ids,
			'related_product_ids'  => $this->get_related_product_ids( $product_id ),
		);
	}

	/**
	 * ID связанных товаров (порядок sort_order).
	 *
	 * @param int $product_id
	 * @return array<int, int>
	 */
	public function get_related_product_ids( $product_id ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return array();
		}

		$table = $this->table( 'product_related' );
		$rows  = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT related_product_id FROM `{$table}` WHERE product_id = %d ORDER BY sort_order ASC, related_product_id ASC",
				$product_id
			)
		);

		return array_map( 'intval', $rows );
	}

	/**
	 * Связанные товары через JOIN product_related + product.
	 *
	 * @param int $product_id
	 * @param int $language_id 0 = текущий язык (Polylang / language_id=1).
	 * @return array<int, object>
	 */
	public function getRelatedProducts( $product_id, $language_id = 0 ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return array();
		}

		if ( $language_id <= 0 ) {
			$language_id = $this->get_current_language_id();
			if ( $language_id <= 0 ) {
				$language_id = 1;
			}
		}

		$pr_table = $this->table( 'product_related' );
		$p_table  = $this->table( 'product' );
		$d_table  = $this->table( 'product_description' );
		$p2c      = $this->table( 'product_to_category' );

		$sql = "SELECT p.*, d.name, d.description, pr.sort_order AS related_sort_order
			FROM `{$pr_table}` pr
			INNER JOIN `{$p_table}` p ON p.product_id = pr.related_product_id AND p.status = 1
			INNER JOIN `{$p2c}` p2c_src ON p2c_src.product_id = %d
			INNER JOIN `{$p2c}` p2c_rel ON p2c_rel.product_id = p.product_id AND p2c_rel.category_id = p2c_src.category_id
			LEFT JOIN `{$d_table}` d ON d.product_id = p.product_id AND d.language_id = %d
			WHERE pr.product_id = %d
			ORDER BY pr.sort_order ASC, d.name ASC";

		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare( $sql, $product_id, $language_id, $product_id ) // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		);

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Товар привязан к категории.
	 *
	 * @param int $product_id
	 * @param int $category_id
	 * @return bool
	 */
	public function product_in_category( $product_id, $category_id ) {
		$product_id  = (int) $product_id;
		$category_id = (int) $category_id;
		if ( $product_id <= 0 || $category_id <= 0 ) {
			return false;
		}

		$table = $this->table( 'product_to_category' );

		return (bool) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT 1 FROM `{$table}` WHERE product_id = %d AND category_id = %d LIMIT 1",
				$product_id,
				$category_id
			)
		);
	}

	/**
	 * Поиск товаров в категории (от 2 символов) для автодополнения.
	 *
	 * @param int    $category_id
	 * @param int    $language_id
	 * @param string $query
	 * @param int    $exclude_product_id
	 * @param int[]  $exclude_ids
	 * @return array<int, array{id: int, name: string}>
	 */
	public function search_products_in_category( $category_id, $language_id, $query, $exclude_product_id = 0, $exclude_ids = array() ) {
		$category_id        = (int) $category_id;
		$language_id        = (int) $language_id;
		$exclude_product_id = (int) $exclude_product_id;
		$query              = trim( (string) $query );

		if ( $category_id <= 0 || strlen( $query ) < 2 ) {
			return array();
		}

		$p_table = $this->table( 'product' );
		$d_table = $this->table( 'product_description' );
		$p2c     = $this->table( 'product_to_category' );

		$like = '%' . $this->wpdb->esc_like( $query ) . '%';

		$exclude_sql   = '';
		$prepare_args  = array( $category_id, $language_id, $like );

		if ( $exclude_product_id > 0 ) {
			$exclude_sql   .= ' AND p.product_id != %d';
			$prepare_args[] = $exclude_product_id;
		}

		if ( ! empty( $exclude_ids ) ) {
			$exclude_ids = array_filter( array_map( 'intval', $exclude_ids ) );
			if ( ! empty( $exclude_ids ) ) {
				$placeholders   = implode( ',', array_fill( 0, count( $exclude_ids ), '%d' ) );
				$exclude_sql   .= " AND p.product_id NOT IN ({$placeholders})";
				$prepare_args   = array_merge( $prepare_args, $exclude_ids );
			}
		}

		$sql = "SELECT p.product_id, d.name
			FROM `{$p_table}` p
			INNER JOIN `{$p2c}` p2c ON p2c.product_id = p.product_id AND p2c.category_id = %d
			LEFT JOIN `{$d_table}` d ON d.product_id = p.product_id AND d.language_id = %d
			WHERE p.status = 1 AND d.name LIKE %s {$exclude_sql}
			ORDER BY p.sort_order ASC, d.name ASC
			LIMIT 20";

		$rows = $this->wpdb->get_results( $this->wpdb->prepare( $sql, $prepare_args ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$out  = array();

		foreach ( $rows as $row ) {
			$pid = (int) $row->product_id;
			$out[] = array(
				'id'   => $pid,
				'name' => $row->name ? (string) $row->name : '#' . $pid,
			);
		}

		return $out;
	}

	/**
	 * Выбранные рекомендуемые товары для плашек в форме (только та же категория).
	 *
	 * @param int        $language_id
	 * @param int        $category_id
	 * @param int        $product_id
	 * @param array|null $related_ids
	 * @return array<int, array{id: int, name: string}>
	 */
	public function get_related_chip_items( $language_id, $category_id, $product_id, $related_ids = null ) {
		$language_id = (int) $language_id;
		$category_id = (int) $category_id;
		$product_id  = (int) $product_id;

		if ( null === $related_ids ) {
			$related_ids = $product_id > 0 ? $this->get_related_product_ids( $product_id ) : array();
		}

		if ( $category_id <= 0 || empty( $related_ids ) ) {
			return array();
		}

		$out = array();
		foreach ( array_map( 'intval', $related_ids ) as $related_id ) {
			if ( $related_id <= 0 || $related_id === $product_id ) {
				continue;
			}
			if ( ! $this->product_in_category( $related_id, $category_id ) ) {
				continue;
			}
			$name = $this->get_product_name( $related_id, $language_id );
			$out[] = array(
				'id'   => $related_id,
				'name' => '' !== $name ? $name : '#' . $related_id,
			);
		}

		return $out;
	}

	/**
	 * @param int $product_id
	 * @param int $language_id
	 * @return string
	 */
	public function get_product_name( $product_id, $language_id = 0 ) {
		$product_id  = (int) $product_id;
		$language_id = (int) $language_id;
		if ( $product_id <= 0 ) {
			return '';
		}
		if ( $language_id <= 0 ) {
			$language_id = $this->get_catalog_language_id();
		}

		$table = $this->table( 'product_description' );

		return (string) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT name FROM `{$table}` WHERE product_id = %d AND language_id = %d",
				$product_id,
				$language_id
			)
		);
	}

	/**
	 * @param int   $product_id
	 * @param array $related_product_ids
	 * @param int   $category_id
	 */
	public function save_related_products( $product_id, $related_product_ids, $category_id = 0 ) {
		$product_id  = (int) $product_id;
		$category_id = (int) $category_id;
		if ( $product_id <= 0 ) {
			return;
		}

		$table = $this->table( 'product_related' );
		$this->wpdb->delete( $table, array( 'product_id' => $product_id ), array( '%d' ) );

		if ( ! is_array( $related_product_ids ) || empty( $related_product_ids ) ) {
			return;
		}

		$seen = array();
		$sort = 0;

		foreach ( $related_product_ids as $related_id ) {
			$related_id = (int) $related_id;
			if ( $related_id <= 0 || $related_id === $product_id ) {
				continue;
			}
			if ( $category_id > 0 && ! $this->product_in_category( $related_id, $category_id ) ) {
				continue;
			}
			if ( isset( $seen[ $related_id ] ) ) {
				continue;
			}
			$seen[ $related_id ] = true;

			$this->wpdb->insert(
				$table,
				array(
					'product_id'         => $product_id,
					'related_product_id' => $related_id,
					'sort_order'         => $sort,
				),
				array( '%d', '%d', '%d' )
			);
			++$sort;
		}
	}

	/**
	 * @param int               $product_id
	 * @param array             $data
	 * @param array<int, array> $descriptions
	 * @param array<int>        $category_ids
	 * @param array<int>        $category_ids
	 * @param array<int>        $related_product_ids
	 * @param array             $product_attributes attribute_id => [ language_id => text ].
	 * @return int
	 */
	public function save( $product_id, $data, $descriptions, $category_ids, $related_product_ids = array(), $product_attributes = array() ) {
		$p_table = $this->table( 'product' );
		$d_table = $this->table( 'product_description' );
		$p2c     = $this->table( 'product_to_category' );

		$row = array(
			'manufacturer_id' => isset( $data['manufacturer_id'] ) ? (int) $data['manufacturer_id'] : 0,
			'image'           => isset( $data['image'] ) ? $data['image'] : '',
			'image2'          => isset( $data['image2'] ) ? $data['image2'] : '',
			'image3'          => isset( $data['image3'] ) ? $data['image3'] : '',
			'image4'          => isset( $data['image4'] ) ? $data['image4'] : '',
			'image5'          => isset( $data['image5'] ) ? $data['image5'] : '',
			'image6'          => isset( $data['image6'] ) ? $data['image6'] : '',
			'sort_order'      => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
			'status'          => ! empty( $data['status'] ) ? 1 : 0,
		);

		$formats = array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d' );

		if ( $product_id > 0 ) {
			$this->wpdb->update( $p_table, $row, array( 'product_id' => $product_id ), $formats, array( '%d' ) );
		} else {
			$inserted = $this->wpdb->insert( $p_table, $row, $formats );
			if ( false === $inserted ) {
				return 0;
			}
			$product_id = (int) $this->wpdb->insert_id;
		}

		if ( $product_id <= 0 ) {
			return 0;
		}

		$this->wpdb->delete( $d_table, array( 'product_id' => $product_id ), array( '%d' ) );
		foreach ( $descriptions as $language_id => $desc ) {
			if ( empty( $desc['name'] ) ) {
				continue;
			}
			$this->wpdb->insert(
				$d_table,
				array(
					'product_id'   => $product_id,
					'language_id'  => (int) $language_id,
					'name'         => $desc['name'],
					'description'  => isset( $desc['description'] ) ? $desc['description'] : '',
					'block1'       => isset( $desc['block1'] ) ? $desc['block1'] : '',
					'block2'       => isset( $desc['block2'] ) ? $desc['block2'] : '',
					'block3'       => isset( $desc['block3'] ) ? $desc['block3'] : '',
					'block4'       => isset( $desc['block4'] ) ? $desc['block4'] : '',
					'block5'       => isset( $desc['block5'] ) ? $desc['block5'] : '',
					'block6'       => isset( $desc['block6'] ) ? $desc['block6'] : '',
					'block7'       => isset( $desc['block7'] ) ? $desc['block7'] : '',
					'block8'       => isset( $desc['block8'] ) ? $desc['block8'] : '',
					'dop1'         => isset( $desc['dop1'] ) ? $desc['dop1'] : '',
				),
				array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
			);
		}

		$this->wpdb->delete( $p2c, array( 'product_id' => $product_id ), array( '%d' ) );
		$primary_category = 0;
		foreach ( array_unique( array_map( 'intval', $category_ids ) ) as $cid ) {
			if ( $cid <= 0 ) {
				continue;
			}
			if ( $primary_category <= 0 ) {
				$primary_category = $cid;
			}
			$this->wpdb->insert(
				$p2c,
				array(
					'product_id'  => $product_id,
					'category_id' => $cid,
				),
				array( '%d', '%d' )
			);
		}

		$this->save_related_products( $product_id, $related_product_ids, $primary_category );

		$attribute_model = new AI_Calculator_Attribute_Model();
		$attribute_model->save_product_attributes( $product_id, $product_attributes );

		return $product_id;
	}

	/**
	 * @param int $product_id
	 */
	public function delete( $product_id ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return;
		}

		$this->wpdb->delete( $this->table( 'product_description' ), array( 'product_id' => $product_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'product_attribute' ), array( 'product_id' => $product_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'product_to_category' ), array( 'product_id' => $product_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'product_related' ), array( 'product_id' => $product_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'product_related' ), array( 'related_product_id' => $product_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'product' ), array( 'product_id' => $product_id ), array( '%d' ) );
	}

	/**
	 * @param int $product_id
	 * @param int $sort_order
	 */
	public function update_sort_order( $product_id, $sort_order ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return false;
		}

		return false !== $this->wpdb->update(
			$this->table( 'product' ),
			array( 'sort_order' => (int) $sort_order ),
			array( 'product_id' => $product_id ),
			array( '%d' ),
			array( '%d' )
		);
	}

	/**
	 * @param int    $product_id
	 * @param int    $language_id
	 * @param string $field
	 * @param string $value
	 */
	public function update_description_field( $product_id, $language_id, $field, $value ) {
		$product_id  = (int) $product_id;
		$language_id = (int) $language_id;
		$field       = (string) $field;

		if ( $product_id <= 0 || $language_id <= 0 || ! in_array( $field, array( 'name', 'description', 'block6' ), true ) ) {
			return false;
		}

		$table  = $this->table( 'product_description' );
		$exists = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$table}` WHERE product_id = %d AND language_id = %d",
				$product_id,
				$language_id
			)
		);

		if ( $exists > 0 ) {
			return false !== $this->wpdb->update(
				$table,
				array( $field => $value ),
				array(
					'product_id'  => $product_id,
					'language_id' => $language_id,
				),
				array( '%s' ),
				array( '%d', '%d' )
			);
		}

		return false !== $this->wpdb->insert(
			$table,
			array(
				'product_id'  => $product_id,
				'language_id' => $language_id,
				'name'        => 'name' === $field ? $value : '',
				'description' => 'description' === $field ? $value : '',
				'block1'      => '',
				'block2'      => '',
				'block3'      => '',
				'block4'      => '',
				'block5'      => '',
				'block6'      => '',
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}
}

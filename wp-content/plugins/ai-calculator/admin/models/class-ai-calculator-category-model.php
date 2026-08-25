<?php
/**
 * Category model.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Category_Model extends AI_Calculator_Model {

	const PER_PAGE = 30;

	/**
	 * @param int $language_id
	 * @param int $manufacturer_id 0 = all calculators.
	 * @param int $page 0 = all rows; 1+ = paginated slice.
	 * @return array<int, object>
	 */
	public function get_list( $language_id, $manufacturer_id = 0, $page = 0 ) {
		$language_id     = (int) $language_id;
		$manufacturer_id = (int) $manufacturer_id;
		$c_table         = $this->table( 'category' );
		$d_table         = $this->table( 'category_description' );
		$m_table         = $this->table( 'manufacturer' );
		$md_table        = $this->table( 'manufacturer_description' );

		$where  = '1=1';
		$params = array( $language_id, $language_id );

		if ( $manufacturer_id > 0 ) {
			$where   .= ' AND c.manufacturer_id = %d';
			$params[] = $manufacturer_id;
		}

		$sql = $this->wpdb->prepare(
			"SELECT c.*, d.name, md.name AS manufacturer_name
			FROM `{$c_table}` c
			LEFT JOIN `{$d_table}` d ON c.category_id = d.category_id AND d.language_id = %d
			LEFT JOIN `{$m_table}` m ON c.manufacturer_id = m.manufacturer_id
			LEFT JOIN `{$md_table}` md ON m.manufacturer_id = md.manufacturer_id AND md.language_id = %d
			WHERE {$where}
			ORDER BY c.parent_id ASC, c.sort_order ASC, d.name ASC",
			$params
		);

		$rows  = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$by_id = array();
		foreach ( $rows as $row ) {
			$by_id[ (int) $row->category_id ] = $row;
		}

		$tree = array();
		foreach ( $rows as $row ) {
			$path = $this->build_path_name( $row, $by_id );
			$row->path_name = $path;
			$tree[]         = $row;
		}

		$page = (int) $page;
		if ( $page > 0 ) {
			$offset = ( $page - 1 ) * self::PER_PAGE;
			return array_slice( $tree, $offset, self::PER_PAGE );
		}

		return $tree;
	}

	/**
	 * @param int $language_id
	 * @param int $manufacturer_id 0 = all calculators.
	 * @return int
	 */
	public function count_list( $language_id, $manufacturer_id = 0 ) {
		$manufacturer_id = (int) $manufacturer_id;
		$c_table         = $this->table( 'category' );

		if ( $manufacturer_id > 0 ) {
			return (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT COUNT(*) FROM `{$c_table}` WHERE manufacturer_id = %d",
					$manufacturer_id
				)
			);
		}

		return (int) $this->wpdb->get_var( "SELECT COUNT(*) FROM `{$c_table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * @param object              $row
	 * @param array<int, object>  $by_id
	 */
	private function build_path_name( $row, $by_id ) {
		$parts   = array();
		$visited = array();
		$current = $row;
		while ( $current ) {
			$category_id = (int) $current->category_id;
			if ( isset( $visited[ $category_id ] ) ) {
				break;
			}
			$visited[ $category_id ] = true;
			array_unshift( $parts, $current->name ? $current->name : '#' . $category_id );
			$parent_id = (int) $current->parent_id;
			if ( $parent_id <= 0 || ! isset( $by_id[ $parent_id ] ) ) {
				break;
			}
			$current = $by_id[ $parent_id ];
		}
		return implode( ' > ', $parts );
	}

	/**
	 * @param int $category_id 0 = new category.
	 * @param int $parent_id
	 * @param int $manufacturer_id
	 */
	public function is_valid_parent( $category_id, $parent_id, $manufacturer_id = 0 ) {
		$category_id     = (int) $category_id;
		$parent_id       = (int) $parent_id;
		$manufacturer_id = (int) $manufacturer_id;

		if (
			function_exists( 'ai_calculator_is_family_comfort_manufacturer_id' )
			&& ai_calculator_is_family_comfort_manufacturer_id( $manufacturer_id )
		) {
			$root_id = function_exists( 'ai_calculator_get_family_comfort_root_category_id' )
				? ai_calculator_get_family_comfort_root_category_id()
				: 0;

			if ( $root_id > 0 && $category_id === $root_id ) {
				return 0 === $parent_id;
			}

			if ( $root_id > 0 ) {
				return $parent_id === $root_id;
			}

			return 0 === $parent_id;
		}

		if ( $parent_id <= 0 ) {
			return true;
		}
		if ( $category_id > 0 && $parent_id === $category_id ) {
			return false;
		}

		$c_table    = $this->table( 'category' );
		$visited    = array();
		$current_id = $parent_id;

		while ( $current_id > 0 ) {
			if ( $category_id > 0 && $current_id === $category_id ) {
				return false;
			}
			if ( isset( $visited[ $current_id ] ) ) {
				break;
			}
			$visited[ $current_id ] = true;
			$current_id             = (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT parent_id FROM `{$c_table}` WHERE category_id = %d",
					$current_id
				)
			);
		}

		return true;
	}

	/**
	 * @param int                  $category_id
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	public function normalize_family_comfort_parent( $category_id, $data ) {
		$manufacturer_id = isset( $data['manufacturer_id'] ) ? (int) $data['manufacturer_id'] : 0;
		if (
			! function_exists( 'ai_calculator_is_family_comfort_manufacturer_id' )
			|| ! ai_calculator_is_family_comfort_manufacturer_id( $manufacturer_id )
		) {
			return $data;
		}

		if ( function_exists( 'ai_calculator_ensure_family_comfort_root_category' ) ) {
			ai_calculator_ensure_family_comfort_root_category();
		}

		$root_id     = function_exists( 'ai_calculator_get_family_comfort_root_category_id' )
			? ai_calculator_get_family_comfort_root_category_id()
			: 0;
		$category_id = (int) $category_id;

		if ( $root_id > 0 && $category_id === $root_id ) {
			$data['parent_id'] = 0;
		} elseif ( $root_id > 0 ) {
			$data['parent_id'] = $root_id;
		} else {
			$data['parent_id'] = 0;
		}

		return $data;
	}

	/**
	 * @param int $language_id
	 * @param int $editing_id
	 * @param int $manufacturer_id
	 * @return array<int, object>
	 */
	public function get_parent_categories_for_form( $language_id, $editing_id = 0, $manufacturer_id = 0 ) {
		$list        = $this->get_list( $language_id, $manufacturer_id );
		$editing_id  = (int) $editing_id;
		$manufacturer_id = (int) $manufacturer_id;

		if (
			function_exists( 'ai_calculator_is_family_comfort_manufacturer_id' )
			&& ai_calculator_is_family_comfort_manufacturer_id( $manufacturer_id )
		) {
			if ( function_exists( 'ai_calculator_ensure_family_comfort_root_category' ) ) {
				ai_calculator_ensure_family_comfort_root_category();
			}

			$root_id = function_exists( 'ai_calculator_get_family_comfort_root_category_id' )
				? ai_calculator_get_family_comfort_root_category_id()
				: 0;

			if ( $root_id <= 0 || $editing_id === $root_id ) {
				return array();
			}

			foreach ( $list as $row ) {
				if ( (int) $row->category_id === $root_id ) {
					return array( $row );
				}
			}

			return array();
		}

		$parents = array();
		foreach ( $list as $row ) {
			if ( (int) $row->category_id === $editing_id ) {
				continue;
			}
			$parents[] = $row;
		}

		return $parents;
	}

	/**
	 * @param int $category_id
	 * @return array{category: object|null, descriptions: array<int, object>}
	 */
	public function get( $category_id ) {
		$category_id = (int) $category_id;
		$c_table     = $this->table( 'category' );
		$d_table     = $this->table( 'category_description' );

		$category = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$c_table}` WHERE category_id = %d",
				$category_id
			)
		);

		$descriptions = array();
		if ( $category ) {
			$rows = $this->wpdb->get_results(
				$this->wpdb->prepare(
					"SELECT * FROM `{$d_table}` WHERE category_id = %d",
					$category_id
				)
			);
			foreach ( $rows as $row ) {
				$descriptions[ (int) $row->language_id ] = $row;
			}
		}

		return array(
			'category'     => $category,
			'descriptions' => $descriptions,
		);
	}

	/**
	 * Options for select fields (same pattern as manufacturers).
	 *
	 * @param int $language_id
	 * @param int $manufacturer_id 0 = all calculators.
	 * @return array<int, string>
	 */
	public function get_options( $language_id, $manufacturer_id = 0 ) {
		$list    = $this->get_list( $language_id, $manufacturer_id );
		$options = array( 0 => __( '— None —', 'ai-calculator' ) );
		foreach ( $list as $row ) {
			$options[ (int) $row->category_id ] = wp_strip_all_tags( $row->path_name );
		}
		return $options;
	}

	/**
	 * @param int $language_id
	 * @param int $exclude_id
	 * @param int $manufacturer_id Limit parents to this calculator (0 = all).
	 * @return array<int, string>
	 */
	public function get_parent_options( $language_id, $exclude_id = 0, $manufacturer_id = 0 ) {
		$list    = $this->get_list( $language_id, $manufacturer_id );
		$options = array( 0 => __( '— None —', 'ai-calculator' ) );
		foreach ( $list as $row ) {
			if ( (int) $row->category_id === (int) $exclude_id ) {
				continue;
			}
			$options[ (int) $row->category_id ] = wp_strip_all_tags( $row->path_name );
		}
		return $options;
	}

	/**
	 * @param int                  $category_id 0 = new.
	 * @param array<string, mixed> $data
	 * @param array<int, array>    $descriptions
	 * @return int
	 */
	public function save( $category_id, $data, $descriptions ) {
		$c_table = $this->table( 'category' );
		$d_table = $this->table( 'category_description' );

		$row = array(
			'manufacturer_id' => isset( $data['manufacturer_id'] ) ? (int) $data['manufacturer_id'] : 0,
			'parent_id'       => isset( $data['parent_id'] ) ? (int) $data['parent_id'] : 0,
			'image'           => isset( $data['image'] ) ? $data['image'] : '',
			'sort_order'      => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
			'status'          => ! empty( $data['status'] ) ? 1 : 0,
		);

		if ( $category_id > 0 ) {
			$this->wpdb->update( $c_table, $row, array( 'category_id' => $category_id ), array( '%d', '%d', '%s', '%d', '%d' ), array( '%d' ) );
		} else {
			$this->wpdb->insert( $c_table, $row, array( '%d', '%d', '%s', '%d', '%d' ) );
			$category_id = (int) $this->wpdb->insert_id;
		}

		$this->wpdb->delete( $d_table, array( 'category_id' => $category_id ), array( '%d' ) );

		foreach ( $descriptions as $language_id => $desc ) {
			if ( empty( $desc['name'] ) ) {
				continue;
			}
			$this->wpdb->insert(
				$d_table,
				array(
					'category_id'      => $category_id,
					'language_id'      => (int) $language_id,
					'name'             => $desc['name'],
					'description'      => isset( $desc['description'] ) ? $desc['description'] : '',
					'meta_title'       => isset( $desc['meta_title'] ) ? $desc['meta_title'] : '',
					'meta_description' => isset( $desc['meta_description'] ) ? $desc['meta_description'] : '',
				),
				array( '%d', '%d', '%s', '%s', '%s', '%s' )
			);
		}

		return $category_id;
	}

	/**
	 * @param int $category_id
	 * @return string Error message or empty on success.
	 */
	public function delete( $category_id ) {
		$category_id = (int) $category_id;
		if ( $category_id <= 0 ) {
			return __( 'Invalid category.', 'ai-calculator' );
		}

		$c_table = $this->table( 'category' );
		$child   = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$c_table}` WHERE parent_id = %d",
				$category_id
			)
		);
		if ( $child > 0 ) {
			return __( 'Delete child categories first.', 'ai-calculator' );
		}

		$p2c = $this->table( 'product_to_category' );
		$used = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$p2c}` WHERE category_id = %d",
				$category_id
			)
		);
		if ( $used > 0 ) {
			return __( 'Category is assigned to products.', 'ai-calculator' );
		}

		$this->wpdb->delete( $this->table( 'category_description' ), array( 'category_id' => $category_id ), array( '%d' ) );
		$this->wpdb->delete( $c_table, array( 'category_id' => $category_id ), array( '%d' ) );

		return '';
	}
}

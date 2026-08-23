<?php
/**
 * Category model.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Category_Model extends Map_Plum_Model {

	/**
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_list( $language_id ) {
		$sql = "SELECT c.*, cd.name
			FROM {$this->table( 'category' )} c
			LEFT JOIN {$this->table( 'category_description' )} cd
				ON c.category_id = cd.category_id AND cd.language_id = %d
			ORDER BY c.parent_id ASC, c.sort_order ASC, cd.name ASC";

		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $language_id ) );
	}

	/**
	 * @param int $language_id
	 * @return array<int, string>
	 */
	public function get_options( $language_id, $exclude_id = 0 ) {
		$items = $this->get_list( $language_id );
		$options = array( 0 => __( '— None —', 'map-plum' ) );
		foreach ( $items as $item ) {
			if ( (int) $item->category_id === (int) $exclude_id ) {
				continue;
			}
			$prefix = $item->parent_id > 0 ? '— ' : '';
			$options[ (int) $item->category_id ] = $prefix . ( $item->name ? $item->name : '#' . $item->category_id );
		}
		return $options;
	}

	/**
	 * Количество активных маркеров с выбранной категорией (category_id в маркере).
	 *
	 * @return array<int, int> category_id => count
	 */
	public function get_active_marker_counts() {
		$table = $this->table( 'marker' );
		$rows  = $this->wpdb->get_results(
			"SELECT category_id, COUNT(*) AS marker_count
			FROM `{$table}`
			WHERE status = 1 AND category_id > 0
			GROUP BY category_id" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);

		$counts = array();
		foreach ( (array) $rows as $row ) {
			$counts[ (int) $row->category_id ] = (int) $row->marker_count;
		}

		return $counts;
	}

	/**
	 * @param int $category_id
	 */
	public function get_category( $category_id ) {
		return $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'category' )} WHERE category_id = %d",
				$category_id
			)
		);
	}

	/**
	 * @param int $category_id
	 * @return array<int, object>
	 */
	public function get_descriptions( $category_id ) {
		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'category_description' )} WHERE category_id = %d",
				$category_id
			)
		);
		$out = array();
		foreach ( $rows as $row ) {
			$out[ (int) $row->language_id ] = $row;
		}
		return $out;
	}

	/**
	 * @param array<string, mixed>              $main
	 * @param array<int, array<string, string>> $descriptions
	 * @return int
	 */
	public function add( $main, $descriptions ) {
		$this->wpdb->insert(
			$this->table( 'category' ),
			array(
				'parent_id'  => $main['parent_id'],
				'image'      => $main['image'],
				'sort_order' => $main['sort_order'],
				'status'     => $main['status'],
			),
			array( '%d', '%s', '%d', '%d' )
		);
		$id = (int) $this->wpdb->insert_id;
		$this->save_descriptions( $id, $descriptions );
		return $id;
	}

	/**
	 * @param int                               $id
	 * @param array<string, mixed>              $main
	 * @param array<int, array<string, string>> $descriptions
	 */
	public function edit( $id, $main, $descriptions ) {
		$this->wpdb->update(
			$this->table( 'category' ),
			array(
				'parent_id'  => $main['parent_id'],
				'image'      => $main['image'],
				'sort_order' => $main['sort_order'],
				'status'     => $main['status'],
			),
			array( 'category_id' => $id ),
			array( '%d', '%s', '%d', '%d' ),
			array( '%d' )
		);
		$this->wpdb->delete( $this->table( 'category_description' ), array( 'category_id' => $id ), array( '%d' ) );
		$this->save_descriptions( $id, $descriptions );
	}

	/**
	 * @param int                               $id
	 * @param array<int, array<string, string>> $descriptions
	 */
	private function save_descriptions( $id, $descriptions ) {
		foreach ( $descriptions as $language_id => $row ) {
			if ( empty( $row['name'] ) ) {
				continue;
			}
			$this->wpdb->insert(
				$this->table( 'category_description' ),
				array(
					'category_id'        => $id,
					'language_id'        => $language_id,
					'name'               => isset( $row['name'] ) ? $row['name'] : '',
					'description'        => isset( $row['description'] ) ? $row['description'] : '',
					'meta_title'         => isset( $row['meta_title'] ) ? $row['meta_title'] : '',
					'meta_description'   => isset( $row['meta_description'] ) ? $row['meta_description'] : '',
				),
				array( '%d', '%d', '%s', '%s', '%s', '%s' )
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
			$this->wpdb->delete( $this->table( 'product_to_category' ), array( 'category_id' => $id ), array( '%d' ) );
			$this->wpdb->update(
				$this->table( 'marker' ),
				array( 'category_id' => 0 ),
				array( 'category_id' => $id ),
				array( '%d' ),
				array( '%d' )
			);
			$this->wpdb->delete( $this->table( 'category_description' ), array( 'category_id' => $id ), array( '%d' ) );
			$this->wpdb->delete( $this->table( 'category' ), array( 'category_id' => $id ), array( '%d' ) );
		}
	}
}

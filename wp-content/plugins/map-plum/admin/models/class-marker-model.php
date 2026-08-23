<?php
/**
 * Marker model.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Marker_Model extends Map_Plum_Model {

	/** @var array<int, string>|null */
	private $marker_columns = null;

	/** @var array<int, string>|null */
	private $marker_desc_columns = null;

	/**
	 * @return array<int, string>
	 */
	private function get_marker_columns() {
		if ( null === $this->marker_columns ) {
			$table               = $this->table( 'marker' );
			$this->marker_columns = $this->wpdb->get_col( "SHOW COLUMNS FROM `{$table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
		return $this->marker_columns;
	}

	/**
	 * @return array<int, string>
	 */
	private function get_marker_desc_columns() {
		if ( null === $this->marker_desc_columns ) {
			$table                    = $this->table( 'marker_description' );
			$this->marker_desc_columns = $this->wpdb->get_col( "SHOW COLUMNS FROM `{$table}`", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}
		return $this->marker_desc_columns;
	}

	/**
	 * @param array<string, mixed> $main
	 * @return array{0: array<string, mixed>, 1: array<int, string>}
	 */
	private function filter_marker_main( $main ) {
		$format_map = array(
			'manufacturer_id' => '%d',
			'category_id'     => '%d',
			'coordinates'     => '%s',
			'image'           => '%s',
			'image_id'        => '%d',
			'polylink'        => '%s',
			'sort_order'      => '%d',
			'status'          => '%d',
		);
		$columns = $this->get_marker_columns();
		$data    = array();
		$formats = array();

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
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_list( $language_id ) {
		return $this->get_list_paginated( $language_id, 0, 1, PHP_INT_MAX );
	}

	/**
	 * @param int $manufacturer_id 0 — все регионы.
	 * @return int
	 */
	public function count_list( $manufacturer_id = 0 ) {
		$table = $this->table( 'marker' );
		if ( $manufacturer_id > 0 ) {
			return (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT COUNT(*) FROM `{$table}` WHERE manufacturer_id = %d",
					$manufacturer_id
				)
			);
		}

		return (int) $this->wpdb->get_var( "SELECT COUNT(*) FROM `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * @param int $language_id
	 * @param int $manufacturer_id
	 * @param int $page
	 * @param int $per_page
	 * @return array<int, object>
	 */
	public function get_list_paginated( $language_id, $manufacturer_id = 0, $page = 1, $per_page = 20 ) {
		$page     = max( 1, (int) $page );
		$per_page = max( 1, (int) $per_page );
		$offset   = ( $page - 1 ) * $per_page;

		$sql = "SELECT m.*, md.name, man.name AS manufacturer_name, cat.name AS category_name
			FROM {$this->table( 'marker' )} m
			LEFT JOIN {$this->table( 'marker_description' )} md
				ON m.marker_id = md.marker_id AND md.language_id = %d
			LEFT JOIN {$this->table( 'manufacturer_description' )} man
				ON m.manufacturer_id = man.manufacturer_id AND man.language_id = %d
			LEFT JOIN {$this->table( 'category_description' )} cat
				ON m.category_id = cat.category_id AND cat.language_id = %d
			WHERE 1=1";

		$params = array( $language_id, $language_id, $language_id );

		if ( $manufacturer_id > 0 ) {
			$sql     .= ' AND m.manufacturer_id = %d';
			$params[] = $manufacturer_id;
		}

		$sql     .= ' ORDER BY m.sort_order ASC, md.name ASC LIMIT %d OFFSET %d';
		$params[] = $per_page;
		$params[] = $offset;

		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
	}

	/**
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_all_for_select( $language_id ) {
		return $this->get_list( $language_id );
	}

	/**
	 * @param int $marker_id
	 */
	public function get_marker( $marker_id ) {
		return $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'marker' )} WHERE marker_id = %d",
				$marker_id
			)
		);
	}

	/**
	 * @param int $marker_id
	 * @return array<int, object>
	 */
	public function get_descriptions( $marker_id ) {
		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'marker_description' )} WHERE marker_id = %d",
				$marker_id
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
	 * @param int $language_id
	 * @return array<int, object>
	 */
	/**
	 * @param array<int> $marker_ids
	 * @param int        $language_id
	 * @return array<int, object>
	 */
	public function get_by_ids( $marker_ids, $language_id ) {
		$marker_ids = array_values(
			array_filter(
				array_map( 'intval', (array) $marker_ids ),
				static function ( $id ) {
					return $id > 0;
				}
			)
		);

		if ( empty( $marker_ids ) ) {
			return array();
		}

		$placeholders = implode( ',', array_fill( 0, count( $marker_ids ), '%d' ) );
		$sql          = "SELECT m.marker_id, m.coordinates, md.name, man.name AS manufacturer_name
			FROM {$this->table( 'marker' )} m
			LEFT JOIN {$this->table( 'marker_description' )} md
				ON m.marker_id = md.marker_id AND md.language_id = %d
			LEFT JOIN {$this->table( 'manufacturer_description' )} man
				ON m.manufacturer_id = man.manufacturer_id AND man.language_id = %d
			WHERE m.marker_id IN ({$placeholders})
			ORDER BY md.name ASC";

		$params = array_merge( array( $language_id, $language_id ), $marker_ids );

		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $params ) );
	}

	/**
	 * @param int $product_id
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_by_product( $product_id, $language_id ) {
		$sql = "SELECT m.marker_id, m.coordinates, md.name, man.name AS manufacturer_name
			FROM {$this->table( 'product_to_marker' )} ptm
			INNER JOIN {$this->table( 'marker' )} m ON m.marker_id = ptm.marker_id
			LEFT JOIN {$this->table( 'marker_description' )} md
				ON m.marker_id = md.marker_id AND md.language_id = %d
			LEFT JOIN {$this->table( 'manufacturer_description' )} man
				ON m.manufacturer_id = man.manufacturer_id AND man.language_id = %d
			WHERE ptm.product_id = %d
			ORDER BY md.name ASC";

		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $language_id, $language_id, $product_id ) );
	}

	/**
	 * @param int $product_id
	 * @return array<int>
	 */
	public function get_marker_ids_by_product( $product_id ) {
		$rows = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT marker_id FROM {$this->table( 'product_to_marker' )} WHERE product_id = %d",
				$product_id
			)
		);
		return array_map( 'intval', $rows );
	}

	/**
	 * @param array<string, mixed>              $main
	 * @param array<int, array<string, string>> $descriptions
	 * @return int
	 */
	public function add( $main, $descriptions ) {
		list( $marker_data, $marker_formats ) = $this->filter_marker_main( $main );
		$this->wpdb->insert( $this->table( 'marker' ), $marker_data, $marker_formats );
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
		list( $marker_data, $marker_formats ) = $this->filter_marker_main( $main );
		$this->wpdb->update(
			$this->table( 'marker' ),
			$marker_data,
			array( 'marker_id' => $id ),
			$marker_formats,
			array( '%d' )
		);
		$this->wpdb->delete( $this->table( 'marker_description' ), array( 'marker_id' => $id ), array( '%d' ) );
		$this->save_descriptions( $id, $descriptions );
	}

	/**
	 * @param int                               $id
	 * @param array<int, array<string, string>> $descriptions
	 */
	private function save_descriptions( $id, $descriptions ) {
		$columns    = $this->get_marker_desc_columns();
		$format_map = array(
			'marker_id'   => '%d',
			'language_id' => '%d',
			'name'        => '%s',
			'description' => '%s',
			'arabic_name' => '%s',
		);

		foreach ( $descriptions as $language_id => $row ) {
			if ( empty( $row['name'] ) ) {
				continue;
			}

			$desc_data = array(
				'marker_id'   => $id,
				'language_id' => (int) $language_id,
				'name'        => isset( $row['name'] ) ? $row['name'] : '',
				'description' => isset( $row['description'] ) ? $row['description'] : '',
				'arabic_name' => isset( $row['arabic_name'] ) ? $row['arabic_name'] : '',
			);

			$desc_data    = array_intersect_key( $desc_data, array_flip( $columns ) );
			$desc_formats = array();
			foreach ( $desc_data as $key => $unused ) {
				$desc_formats[] = isset( $format_map[ $key ] ) ? $format_map[ $key ] : '%s';
			}

			$this->wpdb->insert( $this->table( 'marker_description' ), $desc_data, $desc_formats );
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
			$this->wpdb->delete( $this->table( 'product_to_marker' ), array( 'marker_id' => $id ), array( '%d' ) );
			$this->wpdb->delete( $this->table( 'marker_description' ), array( 'marker_id' => $id ), array( '%d' ) );
			$this->wpdb->delete( $this->table( 'marker' ), array( 'marker_id' => $id ), array( '%d' ) );
		}
	}
}

<?php
/**
 * Manufacturer model.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Manufacturer_Model extends Map_Plum_Model {

	/**
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_list( $language_id ) {
		$sql = "SELECT m.*, md.name
			FROM {$this->table( 'manufacturer' )} m
			LEFT JOIN {$this->table( 'manufacturer_description' )} md
				ON m.manufacturer_id = md.manufacturer_id AND md.language_id = %d
			ORDER BY m.sort_order ASC, md.name ASC";

		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $language_id ) );
	}

	/**
	 * @param int $manufacturer_id
	 */
	public function get_manufacturer( $manufacturer_id ) {
		return $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'manufacturer' )} WHERE manufacturer_id = %d",
				$manufacturer_id
			)
		);
	}

	/**
	 * @param int $manufacturer_id
	 * @return array<int, object> keyed by language_id
	 */
	public function get_descriptions( $manufacturer_id ) {
		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'manufacturer_description' )} WHERE manufacturer_id = %d",
				$manufacturer_id
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
			$this->table( 'manufacturer' ),
			array(
				'image'      => $main['image'],
				'sort_order' => $main['sort_order'],
				'status'     => $main['status'],
			),
			array( '%s', '%d', '%d' )
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
			$this->table( 'manufacturer' ),
			array(
				'image'      => $main['image'],
				'sort_order' => $main['sort_order'],
				'status'     => $main['status'],
			),
			array( 'manufacturer_id' => $id ),
			array( '%s', '%d', '%d' ),
			array( '%d' )
		);
		$this->wpdb->delete( $this->table( 'manufacturer_description' ), array( 'manufacturer_id' => $id ), array( '%d' ) );
		$this->save_descriptions( $id, $descriptions );
	}

	/**
	 * @param int                               $id
	 * @param array<int, array<string, string>> $descriptions
	 */
	private function save_descriptions( $id, $descriptions ) {
		foreach ( $descriptions as $language_id => $row ) {
			if ( empty( $row['name'] ) && empty( $row['description'] ) ) {
				continue;
			}
			$this->wpdb->insert(
				$this->table( 'manufacturer_description' ),
				array(
					'manufacturer_id' => $id,
					'language_id'       => $language_id,
					'name'              => $row['name'],
					'description'       => $row['description'],
				),
				array( '%d', '%d', '%s', '%s' )
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
			$this->wpdb->delete( $this->table( 'manufacturer_description' ), array( 'manufacturer_id' => $id ), array( '%d' ) );
			$this->wpdb->delete( $this->table( 'manufacturer' ), array( 'manufacturer_id' => $id ), array( '%d' ) );
		}
	}
}

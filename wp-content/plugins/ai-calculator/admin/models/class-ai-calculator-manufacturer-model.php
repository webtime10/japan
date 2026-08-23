<?php
/**
 * Manufacturer model.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Manufacturer_Model extends AI_Calculator_Model {

	/**
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_list( $language_id ) {
		$language_id = (int) $language_id;
		$m_table     = $this->table( 'manufacturer' );
		$d_table     = $this->table( 'manufacturer_description' );

		$sql = $this->wpdb->prepare(
			"SELECT m.*, d.name
			FROM `{$m_table}` m
			LEFT JOIN `{$d_table}` d ON m.manufacturer_id = d.manufacturer_id AND d.language_id = %d
			ORDER BY m.sort_order ASC, d.name ASC",
			$language_id
		);

		return $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Options for select fields.
	 *
	 * @param int $language_id
	 * @return array<int, string>
	 */
	public function get_options( $language_id ) {
		$list    = $this->get_list( $language_id );
		$options = array( 0 => __( '— None —', 'ai-calculator' ) );
		foreach ( $list as $row ) {
			$label = $row->name ? $row->name : '#' . $row->manufacturer_id;
			$options[ (int) $row->manufacturer_id ] = $label;
		}
		return $options;
	}

	/**
	 * @param int $manufacturer_id
	 * @return array{manufacturer: object|null, descriptions: array<int, object>}
	 */
	public function get( $manufacturer_id ) {
		$manufacturer_id = (int) $manufacturer_id;
		$m_table         = $this->table( 'manufacturer' );
		$d_table         = $this->table( 'manufacturer_description' );

		$manufacturer = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$m_table}` WHERE manufacturer_id = %d",
				$manufacturer_id
			)
		);

		$descriptions = array();
		if ( $manufacturer ) {
			$rows = $this->wpdb->get_results(
				$this->wpdb->prepare(
					"SELECT * FROM `{$d_table}` WHERE manufacturer_id = %d",
					$manufacturer_id
				)
			);
			foreach ( $rows as $row ) {
				$descriptions[ (int) $row->language_id ] = $row;
			}
		}

		return array(
			'manufacturer' => $manufacturer,
			'descriptions' => $descriptions,
		);
	}

	/**
	 * @param int               $manufacturer_id
	 * @param array             $data
	 * @param array<int, array> $descriptions
	 * @return int
	 */
	public function save( $manufacturer_id, $data, $descriptions ) {
		$m_table = $this->table( 'manufacturer' );
		$d_table = $this->table( 'manufacturer_description' );

		$row = array(
			'image'      => isset( $data['image'] ) ? $data['image'] : '',
			'sort_order' => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
			'status'     => ! empty( $data['status'] ) ? 1 : 0,
		);

		if ( $manufacturer_id > 0 ) {
			$this->wpdb->update( $m_table, $row, array( 'manufacturer_id' => $manufacturer_id ), array( '%s', '%d', '%d' ), array( '%d' ) );
		} else {
			$this->wpdb->insert( $m_table, $row, array( '%s', '%d', '%d' ) );
			$manufacturer_id = (int) $this->wpdb->insert_id;
		}

		$this->wpdb->delete( $d_table, array( 'manufacturer_id' => $manufacturer_id ), array( '%d' ) );

		foreach ( $descriptions as $language_id => $desc ) {
			if ( empty( $desc['name'] ) ) {
				continue;
			}
			$this->wpdb->insert(
				$d_table,
				array(
					'manufacturer_id' => $manufacturer_id,
					'language_id'     => (int) $language_id,
					'name'            => $desc['name'],
					'description'     => isset( $desc['description'] ) ? $desc['description'] : '',
				),
				array( '%d', '%d', '%s', '%s' )
			);
		}

		return $manufacturer_id;
	}

	/**
	 * @param int $manufacturer_id
	 * @return string Error message or empty on success.
	 */
	public function delete( $manufacturer_id ) {
		$manufacturer_id = (int) $manufacturer_id;
		if ( $manufacturer_id <= 0 ) {
			return __( 'Некорректный калькулятор.', 'ai-calculator' );
		}

		$p_table = $this->table( 'product' );
		$used    = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$p_table}` WHERE manufacturer_id = %d",
				$manufacturer_id
			)
		);
		if ( $used > 0 ) {
			return __( 'Калькулятор привязан к продуктам.', 'ai-calculator' );
		}

		$this->wpdb->delete( $this->table( 'manufacturer_description' ), array( 'manufacturer_id' => $manufacturer_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'manufacturer' ), array( 'manufacturer_id' => $manufacturer_id ), array( '%d' ) );

		return '';
	}
}

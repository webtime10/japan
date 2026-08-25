<?php
/**
 * Attribute group model (OpenCart-style).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Attribute_Group_Model extends AI_Calculator_Model {

	/**
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_list( $language_id ) {
		$language_id = (int) $language_id;
		$g_table     = $this->table( 'attribute_group' );
		$d_table     = $this->table( 'attribute_group_description' );

		$sql = $this->wpdb->prepare(
			"SELECT g.*, d.name
			FROM `{$g_table}` g
			LEFT JOIN `{$d_table}` d ON g.attribute_group_id = d.attribute_group_id AND d.language_id = %d
			ORDER BY g.sort_order ASC, d.name ASC",
			$language_id
		);

		return $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * @param int $language_id
	 * @return array<int, string>
	 */
	public function get_options( $language_id ) {
		$list    = $this->get_list( $language_id );
		$options = array( 0 => __( '— None —', 'ai-calculator' ) );
		foreach ( $list as $row ) {
			$label = $row->name ? $row->name : '#' . $row->attribute_group_id;
			$options[ (int) $row->attribute_group_id ] = $label;
		}
		return $options;
	}

	/**
	 * @param int $attribute_group_id
	 * @return array{group: object|null, descriptions: array<int, object>}
	 */
	public function get( $attribute_group_id ) {
		$attribute_group_id = (int) $attribute_group_id;
		$g_table            = $this->table( 'attribute_group' );
		$d_table            = $this->table( 'attribute_group_description' );

		$group = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$g_table}` WHERE attribute_group_id = %d",
				$attribute_group_id
			)
		);

		$descriptions = array();
		if ( $group ) {
			$rows = $this->wpdb->get_results(
				$this->wpdb->prepare(
					"SELECT * FROM `{$d_table}` WHERE attribute_group_id = %d",
					$attribute_group_id
				)
			);
			foreach ( $rows as $row ) {
				$descriptions[ (int) $row->language_id ] = $row;
			}
		}

		return array(
			'group'        => $group,
			'descriptions' => $descriptions,
		);
	}

	/**
	 * @param int               $attribute_group_id
	 * @param array             $data
	 * @param array<int, array> $descriptions
	 * @return int
	 */
	public function save( $attribute_group_id, $data, $descriptions ) {
		$g_table = $this->table( 'attribute_group' );
		$d_table = $this->table( 'attribute_group_description' );

		$row = array(
			'sort_order' => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
		);

		if ( $attribute_group_id > 0 ) {
			$this->wpdb->update( $g_table, $row, array( 'attribute_group_id' => $attribute_group_id ), array( '%d' ), array( '%d' ) );
		} else {
			$this->wpdb->insert( $g_table, $row, array( '%d' ) );
			$attribute_group_id = (int) $this->wpdb->insert_id;
		}

		if ( $attribute_group_id <= 0 ) {
			return 0;
		}

		$this->wpdb->delete( $d_table, array( 'attribute_group_id' => $attribute_group_id ), array( '%d' ) );

		foreach ( $descriptions as $language_id => $desc ) {
			if ( empty( $desc['name'] ) ) {
				continue;
			}
			$this->wpdb->insert(
				$d_table,
				array(
					'attribute_group_id' => $attribute_group_id,
					'language_id'        => (int) $language_id,
					'name'               => $desc['name'],
				),
				array( '%d', '%d', '%s' )
			);
		}

		return $attribute_group_id;
	}

	/**
	 * @param int $attribute_group_id
	 * @return string Error message or empty on success.
	 */
	public function delete( $attribute_group_id ) {
		$attribute_group_id = (int) $attribute_group_id;
		if ( $attribute_group_id <= 0 ) {
			return __( 'Некорректная группа атрибутов.', 'ai-calculator' );
		}

		$a_table = $this->table( 'attribute' );
		$used    = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$a_table}` WHERE attribute_group_id = %d",
				$attribute_group_id
			)
		);
		if ( $used > 0 ) {
			return __( 'В группе есть атрибуты. Сначала удалите или перенесите их.', 'ai-calculator' );
		}

		$this->wpdb->delete( $this->table( 'attribute_group_description' ), array( 'attribute_group_id' => $attribute_group_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'attribute_group' ), array( 'attribute_group_id' => $attribute_group_id ), array( '%d' ) );

		return '';
	}
}

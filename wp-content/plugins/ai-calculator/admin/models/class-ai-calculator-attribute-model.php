<?php
/**
 * Attribute model (OpenCart-style).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Attribute_Model extends AI_Calculator_Model {

	/**
	 * @param int $language_id
	 * @param int $attribute_group_id 0 = all groups.
	 * @return array<int, object>
	 */
	public function get_list( $language_id, $attribute_group_id = 0 ) {
		$language_id        = (int) $language_id;
		$attribute_group_id = (int) $attribute_group_id;
		$a_table            = $this->table( 'attribute' );
		$d_table            = $this->table( 'attribute_description' );
		$gd_table           = $this->table( 'attribute_group_description' );

		$where  = '1=1';
		$params = array( $language_id, $language_id );

		if ( $attribute_group_id > 0 ) {
			$where   .= ' AND a.attribute_group_id = %d';
			$params[] = $attribute_group_id;
		}

		$sql = $this->wpdb->prepare(
			"SELECT a.*, d.name, gd.name AS group_name
			FROM `{$a_table}` a
			LEFT JOIN `{$d_table}` d ON a.attribute_id = d.attribute_id AND d.language_id = %d
			LEFT JOIN `{$gd_table}` gd ON a.attribute_group_id = gd.attribute_group_id AND gd.language_id = %d
			WHERE {$where}
			ORDER BY a.attribute_group_id ASC, a.sort_order ASC, d.name ASC",
			$params
		);

		return $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Grouped options for product form selects.
	 *
	 * @param int $language_id
	 * @return array<int, array{name: string, attributes: array<int, string>}>
	 */
	public function get_grouped_options( $language_id ) {
		$list    = $this->get_list( $language_id );
		$groups  = array();

		foreach ( $list as $row ) {
			$gid = (int) $row->attribute_group_id;
			if ( ! isset( $groups[ $gid ] ) ) {
				$groups[ $gid ] = array(
					'name'       => $row->group_name ? $row->group_name : ( $gid > 0 ? '#' . $gid : __( 'Без группы', 'ai-calculator' ) ),
					'attributes' => array(),
				);
			}
			$label = $row->name ? $row->name : '#' . $row->attribute_id;
			$groups[ $gid ]['attributes'][ (int) $row->attribute_id ] = $label;
		}

		return $groups;
	}

	/**
	 * Flat checklist for product form (all attributes, global for every product).
	 *
	 * @param int $language_id
	 * @return array<int, string> attribute_id => label
	 */
	public function get_flat_checkbox_options( $language_id ) {
		$grouped = $this->get_grouped_options( $language_id );
		$flat    = array();

		foreach ( $grouped as $group ) {
			if ( empty( $group['attributes'] ) || ! is_array( $group['attributes'] ) ) {
				continue;
			}
			foreach ( $group['attributes'] as $attribute_id => $label ) {
				$flat[ (int) $attribute_id ] = $label;
			}
		}

		return $flat;
	}

	/**
	 * Create one attribute for a group that still has none (name copies group name).
	 * Used only right after creating a new attribute group — not on every admin request.
	 *
	 * @param int $attribute_group_id
	 */
	public function ensure_attribute_for_group( $attribute_group_id ) {
		$attribute_group_id = (int) $attribute_group_id;
		if ( $attribute_group_id <= 0 ) {
			return;
		}

		$a_table = $this->table( 'attribute' );
		$count   = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$a_table}` WHERE attribute_group_id = %d",
				$attribute_group_id
			)
		);
		if ( $count > 0 ) {
			return;
		}

		if ( ! class_exists( 'AI_Calculator_Attribute_Group_Model' ) ) {
			require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-attribute-group-model.php';
		}

		$group_model = new AI_Calculator_Attribute_Group_Model();
		$group_data  = $group_model->get( $attribute_group_id );
		if ( empty( $group_data['descriptions'] ) ) {
			return;
		}

		$sort_order = 0;
		if ( ! empty( $group_data['group'] ) && isset( $group_data['group']->sort_order ) ) {
			$sort_order = (int) $group_data['group']->sort_order;
		}

		$descriptions = array();
		foreach ( $group_data['descriptions'] as $language_id => $desc ) {
			$name = isset( $desc->name ) ? trim( (string) $desc->name ) : '';
			if ( '' === $name ) {
				continue;
			}
			$descriptions[ (int) $language_id ] = array( 'name' => $name );
		}

		if ( empty( $descriptions ) ) {
			return;
		}

		$this->save(
			0,
			array(
				'attribute_group_id' => $attribute_group_id,
				'sort_order'         => $sort_order,
			),
			$descriptions
		);
	}

	/**
	 * Create one attribute per group that has no attributes yet (group name = attribute name).
	 *
	 * @deprecated Prefer ensure_attribute_for_group() after creating a group.
	 */
	public function sync_missing_attributes_from_groups() {
		$g_table = $this->table( 'attribute_group' );

		$groups = $this->wpdb->get_results(
			"SELECT attribute_group_id FROM `{$g_table}` ORDER BY sort_order ASC, attribute_group_id ASC"
		); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $groups ) ) {
			return;
		}

		foreach ( $groups as $group ) {
			$this->ensure_attribute_for_group( (int) $group->attribute_group_id );
		}
	}

	/**
	 * @param int $attribute_id
	 * @return array{attribute: object|null, descriptions: array<int, object>}
	 */
	public function get( $attribute_id ) {
		$attribute_id = (int) $attribute_id;
		$a_table      = $this->table( 'attribute' );
		$d_table      = $this->table( 'attribute_description' );

		$attribute = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$a_table}` WHERE attribute_id = %d",
				$attribute_id
			)
		);

		$descriptions = array();
		if ( $attribute ) {
			$rows = $this->wpdb->get_results(
				$this->wpdb->prepare(
					"SELECT * FROM `{$d_table}` WHERE attribute_id = %d",
					$attribute_id
				)
			);
			foreach ( $rows as $row ) {
				$descriptions[ (int) $row->language_id ] = $row;
			}
		}

		return array(
			'attribute'    => $attribute,
			'descriptions' => $descriptions,
		);
	}

	/**
	 * @param int               $attribute_id
	 * @param array             $data
	 * @param array<int, array> $descriptions
	 * @return int
	 */
	public function save( $attribute_id, $data, $descriptions ) {
		$a_table = $this->table( 'attribute' );
		$d_table = $this->table( 'attribute_description' );

		$row = array(
			'attribute_group_id' => isset( $data['attribute_group_id'] ) ? (int) $data['attribute_group_id'] : 0,
			'sort_order'         => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
		);

		if ( $attribute_id > 0 ) {
			$this->wpdb->update( $a_table, $row, array( 'attribute_id' => $attribute_id ), array( '%d', '%d' ), array( '%d' ) );
		} else {
			$this->wpdb->insert( $a_table, $row, array( '%d', '%d' ) );
			$attribute_id = (int) $this->wpdb->insert_id;
		}

		if ( $attribute_id <= 0 ) {
			return 0;
		}

		$this->wpdb->delete( $d_table, array( 'attribute_id' => $attribute_id ), array( '%d' ) );

		foreach ( $descriptions as $language_id => $desc ) {
			if ( empty( $desc['name'] ) ) {
				continue;
			}
			$this->wpdb->insert(
				$d_table,
				array(
					'attribute_id' => $attribute_id,
					'language_id'  => (int) $language_id,
					'name'         => $desc['name'],
					'description'  => isset( $desc['description'] ) ? (string) $desc['description'] : '',
				),
				array( '%d', '%d', '%s', '%s' )
			);
		}

		return $attribute_id;
	}

	/**
	 * @param int $attribute_id
	 * @return string Error message or empty on success.
	 */
	public function delete( $attribute_id ) {
		$attribute_id = (int) $attribute_id;
		if ( $attribute_id <= 0 ) {
			return __( 'Некорректный атрибут.', 'ai-calculator' );
		}

		$pa_table = $this->table( 'product_attribute' );
		$used     = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$pa_table}` WHERE attribute_id = %d",
				$attribute_id
			)
		);
		if ( $used > 0 ) {
			return __( 'Атрибут назначен товарам.', 'ai-calculator' );
		}

		$this->wpdb->delete( $this->table( 'attribute_description' ), array( 'attribute_id' => $attribute_id ), array( '%d' ) );
		$this->wpdb->delete( $this->table( 'attribute' ), array( 'attribute_id' => $attribute_id ), array( '%d' ) );

		return '';
	}

	/**
	 * Product attributes for admin form.
	 *
	 * @param int $product_id
	 * @param int $language_id
	 * @return array<int, array{attribute_id: int, name: string, group_name: string, texts: array<int, string>}>
	 */
	public function get_product_attributes( $product_id, $language_id ) {
		$product_id  = (int) $product_id;
		$language_id = (int) $language_id;
		if ( $product_id <= 0 ) {
			return array();
		}

		$pa_table = $this->table( 'product_attribute' );
		$a_table  = $this->table( 'attribute' );
		$ad_table = $this->table( 'attribute_description' );
		$gd_table = $this->table( 'attribute_group_description' );

		$attribute_ids = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT DISTINCT attribute_id FROM `{$pa_table}` WHERE product_id = %d ORDER BY attribute_id ASC",
				$product_id
			)
		);

		if ( empty( $attribute_ids ) ) {
			return array();
		}

		$out = array();
		foreach ( array_map( 'intval', $attribute_ids ) as $attribute_id ) {
			$meta = $this->wpdb->get_row(
				$this->wpdb->prepare(
					"SELECT a.attribute_id, ad.name, gd.name AS group_name
					FROM `{$a_table}` a
					LEFT JOIN `{$ad_table}` ad ON a.attribute_id = ad.attribute_id AND ad.language_id = %d
					LEFT JOIN `{$gd_table}` gd ON a.attribute_group_id = gd.attribute_group_id AND gd.language_id = %d
					WHERE a.attribute_id = %d",
					$language_id,
					$language_id,
					$attribute_id
				)
			);

			$text_rows = $this->wpdb->get_results(
				$this->wpdb->prepare(
					"SELECT language_id, text FROM `{$pa_table}` WHERE product_id = %d AND attribute_id = %d",
					$product_id,
					$attribute_id
				)
			);

			$texts = array();
			foreach ( $text_rows as $text_row ) {
				$texts[ (int) $text_row->language_id ] = (string) $text_row->text;
			}

			$out[ $attribute_id ] = array(
				'attribute_id' => $attribute_id,
				'name'         => $meta && $meta->name ? (string) $meta->name : '#' . $attribute_id,
				'group_name'   => $meta && $meta->group_name ? (string) $meta->group_name : '',
				'texts'        => $texts,
			);
		}

		return $out;
	}

	/**
	 * @param int $product_id
	 * @return array<int>
	 */
	public function get_product_attribute_ids( $product_id ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return array();
		}

		$pa_table = $this->table( 'product_attribute' );
		$rows     = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT DISTINCT attribute_id FROM `{$pa_table}` WHERE product_id = %d ORDER BY attribute_id ASC",
				$product_id
			)
		);

		if ( empty( $rows ) ) {
			return array();
		}

		return array_map( 'intval', $rows );
	}

	/**
	 * @param int $product_id
	 * @return array<int, array<int, array<string, string>>> attribute_id => language_id => fields
	 */
	public function get_product_attribute_data_map( $product_id ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return array();
		}

		$pa_table = $this->table( 'product_attribute' );
		$rows     = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM `{$pa_table}` WHERE product_id = %d",
				$product_id
			)
		);

		if ( empty( $rows ) ) {
			return array();
		}

		$out = array();
		foreach ( $rows as $row ) {
			$attribute_id = (int) $row->attribute_id;
			$language_id  = (int) $row->language_id;
			if ( ! isset( $out[ $attribute_id ] ) ) {
				$out[ $attribute_id ] = array();
			}
			$out[ $attribute_id ][ $language_id ] = array(
				'text'   => isset( $row->text ) ? (string) $row->text : '',
				'label'  => isset( $row->label ) ? (string) $row->label : '',
				'image'  => isset( $row->image ) ? (string) $row->image : '',
				'block1' => isset( $row->block1 ) ? (string) $row->block1 : '',
				'block2' => isset( $row->block2 ) ? (string) $row->block2 : '',
				'block3' => isset( $row->block3 ) ? (string) $row->block3 : '',
				'block4' => isset( $row->block4 ) ? (string) $row->block4 : '',
				'block5' => isset( $row->block5 ) ? (string) $row->block5 : '',
				'block6' => isset( $row->block6 ) ? (string) $row->block6 : '',
				'block7' => isset( $row->block7 ) ? (string) $row->block7 : '',
				'block8' => isset( $row->block8 ) ? (string) $row->block8 : '',
			);
		}

		return $out;
	}

	/**
	 * @param int $product_id
	 * @return array<int, array<int, string>> attribute_id => [ language_id => text ]
	 */
	public function get_product_attribute_text_map( $product_id ) {
		$data = $this->get_product_attribute_data_map( $product_id );
		$out  = array();

		foreach ( $data as $attribute_id => $langs ) {
			$out[ $attribute_id ] = array();
			foreach ( $langs as $language_id => $fields ) {
				$out[ $attribute_id ][ $language_id ] = isset( $fields['text'] ) ? (string) $fields['text'] : '';
			}
		}

		return $out;
	}

	/**
	 * @param int   $product_id
	 * @param array $product_attributes attribute_id => [ language_id => array|string ].
	 */
	public function save_product_attributes( $product_id, $product_attributes ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return;
		}

		$table = $this->table( 'product_attribute' );
		$this->wpdb->delete( $table, array( 'product_id' => $product_id ), array( '%d' ) );

		if ( ! is_array( $product_attributes ) || empty( $product_attributes ) ) {
			return;
		}

		foreach ( $product_attributes as $attribute_id => $langs ) {
			$attribute_id = (int) $attribute_id;
			if ( $attribute_id <= 0 || ! is_array( $langs ) ) {
				continue;
			}

			foreach ( $langs as $language_id => $fields ) {
				$language_id = (int) $language_id;
				if ( $language_id <= 0 ) {
					continue;
				}

				if ( is_string( $fields ) ) {
					$fields = array( 'text' => $fields );
				}
				if ( ! is_array( $fields ) ) {
					continue;
				}

				$row = array(
					'product_id'   => $product_id,
					'attribute_id' => $attribute_id,
					'language_id'  => $language_id,
					'text'         => isset( $fields['text'] ) ? sanitize_textarea_field( (string) $fields['text'] ) : '',
					'label'        => isset( $fields['label'] ) ? sanitize_text_field( (string) $fields['label'] ) : '',
					'image'        => isset( $fields['image'] ) ? esc_url_raw( (string) $fields['image'] ) : '',
					'block1'       => isset( $fields['block1'] ) ? esc_url_raw( (string) $fields['block1'] ) : '',
					'block2'       => isset( $fields['block2'] ) ? sanitize_text_field( (string) $fields['block2'] ) : '',
					'block3'       => isset( $fields['block3'] ) ? sanitize_text_field( (string) $fields['block3'] ) : '',
					'block4'       => isset( $fields['block4'] ) ? sanitize_text_field( (string) $fields['block4'] ) : '',
					'block5'       => isset( $fields['block5'] ) ? sanitize_text_field( (string) $fields['block5'] ) : '',
					'block6'       => isset( $fields['block6'] ) ? sanitize_text_field( (string) $fields['block6'] ) : '',
					'block7'       => isset( $fields['block7'] ) ? sanitize_text_field( (string) $fields['block7'] ) : '',
					'block8'       => isset( $fields['block8'] ) ? sanitize_text_field( (string) $fields['block8'] ) : '',
				);

				$this->wpdb->insert(
					$table,
					$row,
					array( '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
				);
			}
		}
	}

	/**
	 * @param int        $product_id
	 * @param array<int> $attribute_ids
	 * @param int        $language_id
	 * @deprecated Use save_product_attributes().
	 */
	public function save_product_attribute_ids( $product_id, $attribute_ids, $language_id ) {
		$language_id = (int) $language_id;
		if ( $language_id <= 0 ) {
			$language_id = 1;
		}

		$product_attributes = array();
		foreach ( (array) $attribute_ids as $attribute_id ) {
			$attribute_id = (int) $attribute_id;
			if ( $attribute_id <= 0 ) {
				continue;
			}
			$product_attributes[ $attribute_id ] = array( $language_id => '' );
		}

		$this->save_product_attributes( $product_id, $product_attributes );
	}
}

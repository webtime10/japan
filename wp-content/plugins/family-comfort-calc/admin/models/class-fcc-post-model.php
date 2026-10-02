<?php
/**
 * Internal post model (OpenCart-style).
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Post_Model extends FCC_Model {

	const PER_PAGE = 30;

	/**
	 * @param int $language_id
	 * @param int $page
	 * @return array<int, object>
	 */
	public function get_list( $language_id, $page = 0 ) {
		$language_id = (int) $language_id;
		$p_table     = $this->table( 'post' );
		$d_table     = $this->table( 'post_description' );
		$c_table     = $this->table( 'category' );
		$cd_table    = $this->table( 'category_description' );

		$sql = $this->wpdb->prepare(
			"SELECT p.*, d.name, d.description, cd.name AS direction_name
			FROM `{$p_table}` p
			LEFT JOIN `{$d_table}` d ON p.post_id = d.post_id AND d.language_id = %d
			LEFT JOIN `{$c_table}` c ON p.direction_id = c.category_id AND c.group_type = 'direction'
			LEFT JOIN `{$cd_table}` cd ON c.category_id = cd.category_id AND cd.language_id = %d
			ORDER BY p.sort_order ASC, d.name ASC",
			$language_id,
			$language_id
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$page = (int) $page;
		if ( $page > 0 ) {
			$offset = ( $page - 1 ) * self::PER_PAGE;
			return array_slice( $rows, $offset, self::PER_PAGE );
		}

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * @return int
	 */
	public function count_list() {
		$p_table = $this->table( 'post' );
		return (int) $this->wpdb->get_var( "SELECT COUNT(*) FROM `{$p_table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * @param int $post_id
	 * @return object|null
	 */
	public function get( $post_id ) {
		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			return null;
		}

		$p_table = $this->table( 'post' );
		$row     = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$p_table}` WHERE post_id = %d",
				$post_id
			)
		);

		return $row ? $row : null;
	}

	/**
	 * @param int $post_id
	 * @return array<int, object>
	 */
	public function get_descriptions( $post_id ) {
		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			return array();
		}

		$d_table = $this->table( 'post_description' );
		$rows    = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM `{$d_table}` WHERE post_id = %d",
				$post_id
			)
		);

		$out = array();
		foreach ( $rows as $row ) {
			$out[ (int) $row->language_id ] = $row;
		}
		return $out;
	}

	/**
	 * @param int   $post_id
	 * @param array $data
	 * @param array $descriptions
	 * @return int
	 */
	public function save( $post_id, $data, $descriptions ) {
		$post_id = (int) $post_id;
		$p_table = $this->table( 'post' );
		$d_table = $this->table( 'post_description' );

		$row = array(
			'direction_id' => isset( $data['direction_id'] ) ? (int) $data['direction_id'] : 0,
			'image'        => isset( $data['image'] ) ? (string) $data['image'] : '',
			'url'          => isset( $data['url'] ) ? (string) $data['url'] : '',
			'places'       => isset( $data['places'] ) ? (string) $data['places'] : '[]',
			'age_ids'      => isset( $data['age_ids'] ) ? (string) $data['age_ids'] : '[]',
			'interest_ids' => isset( $data['interest_ids'] ) ? (string) $data['interest_ids'] : '[]',
			'sort_order'   => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
			'status'       => ! empty( $data['status'] ) ? 1 : 0,
		);

		$formats = array( '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d' );

		if ( $post_id > 0 ) {
			$this->wpdb->update( $p_table, $row, array( 'post_id' => $post_id ), $formats, array( '%d' ) );
		} else {
			$this->wpdb->insert( $p_table, $row, $formats );
			$post_id = (int) $this->wpdb->insert_id;
		}

		if ( $post_id <= 0 ) {
			return 0;
		}

		foreach ( $descriptions as $language_id => $desc ) {
			$language_id = (int) $language_id;
			if ( $language_id <= 0 ) {
				continue;
			}

			$exists = (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT COUNT(*) FROM `{$d_table}` WHERE post_id = %d AND language_id = %d",
					$post_id,
					$language_id
				)
			);

			$desc_row = array(
				'name'        => isset( $desc['name'] ) ? (string) $desc['name'] : '',
				'description' => isset( $desc['description'] ) ? (string) $desc['description'] : '',
			);

			if ( $exists ) {
				$this->wpdb->update(
					$d_table,
					$desc_row,
					array(
						'post_id'     => $post_id,
						'language_id' => $language_id,
					),
					array( '%s', '%s' ),
					array( '%d', '%d' )
				);
			} else {
				$this->wpdb->insert(
					$d_table,
					array_merge(
						array(
							'post_id'     => $post_id,
							'language_id' => $language_id,
						),
						$desc_row
					),
					array( '%d', '%d', '%s', '%s' )
				);
			}
		}

		return $post_id;
	}

	/**
	 * @param int $post_id
	 * @return string|null
	 */
	public function delete( $post_id ) {
		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			return __( 'Invalid post.', 'family-comfort-calc' );
		}

		$p_table = $this->table( 'post' );
		$d_table = $this->table( 'post_description' );

		$exists = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$p_table}` WHERE post_id = %d",
				$post_id
			)
		);

		if ( ! $exists ) {
			return __( 'Post not found.', 'family-comfort-calc' );
		}

		$this->wpdb->delete( $d_table, array( 'post_id' => $post_id ), array( '%d' ) );
		$this->wpdb->delete( $p_table, array( 'post_id' => $post_id ), array( '%d' ) );

		return null;
	}

	/**
	 * @param array<int> $ids
	 * @return void
	 */
	public function bulk_delete( $ids ) {
		foreach ( $ids as $id ) {
			$this->delete( (int) $id );
		}
	}

	/**
	 * Enabled posts for front calculator.
	 *
	 * @param int $language_id
	 * @return array<int, object>
	 */
	public function get_enabled_for_front( $language_id ) {
		$language_id = (int) $language_id;
		$p_table     = $this->table( 'post' );
		$d_table     = $this->table( 'post_description' );
		$c_table     = $this->table( 'category' );
		$cd_table    = $this->table( 'category_description' );

		$sql = $this->wpdb->prepare(
			"SELECT p.*, d.name AS post_name, d.description AS post_description, cd.name AS direction_name
			FROM `{$p_table}` p
			LEFT JOIN `{$d_table}` d ON p.post_id = d.post_id AND d.language_id = %d
			INNER JOIN `{$c_table}` c ON p.direction_id = c.category_id AND c.group_type = 'direction' AND c.status = 1
			LEFT JOIN `{$cd_table}` cd ON c.category_id = cd.category_id AND cd.language_id = %d
			WHERE p.status = 1 AND p.direction_id > 0
			ORDER BY p.sort_order ASC, cd.name ASC",
			$language_id,
			$language_id
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return is_array( $rows ) ? $rows : array();
	}
}

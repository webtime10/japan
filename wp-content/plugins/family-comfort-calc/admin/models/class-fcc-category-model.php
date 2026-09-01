<?php
/**
 * Category model.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Category_Model extends FCC_Model {

	const PER_PAGE = 30;

	/** @var string */
	private $group_type = 'age';

	public function __construct( $group_type = 'age' ) {
		parent::__construct();
		if ( fcc_is_valid_group( $group_type ) ) {
			$this->group_type = $group_type;
		}
	}

	/**
	 * @param int $language_id
	 * @param int $page
	 * @return array<int, object>
	 */
	public function get_list( $language_id, $page = 0 ) {
		$language_id = (int) $language_id;
		$c_table     = $this->table( 'category' );
		$d_table     = $this->table( 'category_description' );

		$sql = $this->wpdb->prepare(
			"SELECT c.*, d.name, d.description
			FROM `{$c_table}` c
			LEFT JOIN `{$d_table}` d ON c.category_id = d.category_id AND d.language_id = %d
			WHERE c.group_type = %s
			ORDER BY c.sort_order ASC, d.name ASC",
			$language_id,
			$this->group_type
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$page = (int) $page;
		if ( $page > 0 ) {
			$offset = ( $page - 1 ) * self::PER_PAGE;
			return array_slice( $rows, $offset, self::PER_PAGE );
		}

		return $rows;
	}

	/**
	 * @param int $language_id
	 * @return int
	 */
	public function count_list( $language_id ) {
		$c_table = $this->table( 'category' );
		return (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$c_table}` WHERE group_type = %s",
				$this->group_type
			)
		);
	}

	/**
	 * @param int $category_id
	 * @return object|null
	 */
	public function get( $category_id ) {
		$category_id = (int) $category_id;
		if ( $category_id <= 0 ) {
			return null;
		}

		$c_table = $this->table( 'category' );
		$row     = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$c_table}` WHERE category_id = %d AND group_type = %s",
				$category_id,
				$this->group_type
			)
		);

		return $row ? $row : null;
	}

	/**
	 * @param int $category_id
	 * @return array<int, object>
	 */
	public function get_descriptions( $category_id ) {
		$category_id = (int) $category_id;
		if ( $category_id <= 0 ) {
			return array();
		}

		$d_table = $this->table( 'category_description' );
		$rows    = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM `{$d_table}` WHERE category_id = %d",
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
	 * @param int   $category_id
	 * @param array $data
	 * @param array $descriptions
	 * @return int
	 */
	public function save( $category_id, $data, $descriptions ) {
		$category_id = (int) $category_id;
		$c_table     = $this->table( 'category' );
		$d_table     = $this->table( 'category_description' );

		$row = array(
			'group_type' => $this->group_type,
			'sort_order' => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
			'status'     => ! empty( $data['status'] ) ? 1 : 0,
		);

		if ( $category_id > 0 ) {
			$this->wpdb->update( $c_table, $row, array( 'category_id' => $category_id ), array( '%s', '%d', '%d' ), array( '%d' ) );
		} else {
			$this->wpdb->insert( $c_table, $row, array( '%s', '%d', '%d' ) );
			$category_id = (int) $this->wpdb->insert_id;
		}

		if ( $category_id <= 0 ) {
			return 0;
		}

		foreach ( $descriptions as $language_id => $desc ) {
			$language_id = (int) $language_id;
			if ( $language_id <= 0 ) {
				continue;
			}

			$exists = (int) $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT COUNT(*) FROM `{$d_table}` WHERE category_id = %d AND language_id = %d",
					$category_id,
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
						'category_id' => $category_id,
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
							'category_id' => $category_id,
							'language_id' => $language_id,
						),
						$desc_row
					),
					array( '%d', '%d', '%s', '%s' )
				);
			}
		}

		return $category_id;
	}

	/**
	 * @param int $category_id
	 * @return string|null Error message.
	 */
	public function delete( $category_id ) {
		$category_id = (int) $category_id;
		if ( $category_id <= 0 ) {
			return __( 'Invalid category.', 'family-comfort-calc' );
		}

		$c_table = $this->table( 'category' );
		$d_table = $this->table( 'category_description' );

		$exists = (int) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM `{$c_table}` WHERE category_id = %d AND group_type = %s",
				$category_id,
				$this->group_type
			)
		);

		if ( ! $exists ) {
			return __( 'Category not found.', 'family-comfort-calc' );
		}

		$this->wpdb->delete( $d_table, array( 'category_id' => $category_id ), array( '%d' ) );
		$this->wpdb->delete( $c_table, array( 'category_id' => $category_id ), array( '%d' ) );

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
}

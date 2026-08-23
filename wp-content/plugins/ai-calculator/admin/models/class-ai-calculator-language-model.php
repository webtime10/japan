<?php
/**
 * Language model.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Language_Model extends AI_Calculator_Model {

	/**
	 * @param bool $active_only
	 * @return array<int, object>
	 */
	public function get_list( $active_only = false ) {
		$table = $this->table( 'language' );
		$sql   = "SELECT * FROM `{$table}`";
		if ( $active_only ) {
			$sql .= ' WHERE status = 1';
		}
		$sql .= ' ORDER BY sort_order ASC, name ASC';
		return $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * @param int $language_id
	 * @return object|null
	 */
	public function get( $language_id ) {
		$table = $this->table( 'language' );
		return $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM `{$table}` WHERE language_id = %d",
				$language_id
			)
		);
	}

	/**
	 * @param array<string, mixed> $data
	 * @return int|false
	 */
	public function save( $data ) {
		$table = $this->table( 'language' );
		$row   = array(
			'name'       => isset( $data['name'] ) ? $data['name'] : '',
			'code'       => isset( $data['code'] ) ? $data['code'] : '',
			'locale'     => isset( $data['locale'] ) ? $data['locale'] : '',
			'sort_order' => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
			'status'     => ! empty( $data['status'] ) ? 1 : 0,
		);
		$format = array( '%s', '%s', '%s', '%d', '%d' );

		if ( ! empty( $data['language_id'] ) ) {
			$id = (int) $data['language_id'];
			$this->wpdb->update( $table, $row, array( 'language_id' => $id ), $format, array( '%d' ) );
			return $id;
		}

		$this->wpdb->insert( $table, $row, $format );
		return (int) $this->wpdb->insert_id;
	}

	/**
	 * @param int $language_id
	 * @return bool
	 */
	public function delete( $language_id ) {
		$language_id = (int) $language_id;
		if ( $language_id <= 0 ) {
			return false;
		}

		$count = count( $this->get_list( false ) );
		if ( $count <= 1 ) {
			return false;
		}

		$table = $this->table( 'language' );
		$this->wpdb->delete( $table, array( 'language_id' => $language_id ), array( '%d' ) );
		return true;
	}

	/**
	 * Import languages from Polylang when available.
	 *
	 * @return int Number of languages added.
	 */
	public function sync_from_polylang() {
		if ( ! function_exists( 'pll_languages_list' ) ) {
			return 0;
		}

		$codes = pll_languages_list( array( 'fields' => 'slug' ) );
		if ( ! is_array( $codes ) || empty( $codes ) ) {
			return 0;
		}

		$added = 0;
		foreach ( $codes as $code ) {
			$exists = $this->wpdb->get_var(
				$this->wpdb->prepare(
					'SELECT language_id FROM `' . $this->table( 'language' ) . '` WHERE code = %s',
					$code
				)
			);
			if ( $exists ) {
				continue;
			}

			$name = strtoupper( $code );
			if ( function_exists( 'pll_languages_list' ) ) {
				$names = pll_languages_list( array( 'fields' => 'name' ) );
				$slugs = pll_languages_list( array( 'fields' => 'slug' ) );
				$idx   = array_search( $code, $slugs, true );
				if ( false !== $idx && isset( $names[ $idx ] ) ) {
					$name = $names[ $idx ];
				}
			}

			$this->save(
				array(
					'name'       => $name,
					'code'       => $code,
					'locale'     => $code,
					'sort_order' => 0,
					'status'     => 1,
				)
			);
			++$added;
		}

		return $added;
	}
}

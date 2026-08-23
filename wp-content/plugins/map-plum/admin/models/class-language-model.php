<?php
/**
 * Language model.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Language_Model extends Map_Plum_Model {

	/**
	 * @return array<int, object>
	 */
	public function get_all_active() {
		return $this->wpdb->get_results( "SELECT * FROM {$this->table( 'language' )} WHERE status = 1 ORDER BY sort_order ASC, language_id ASC" );
	}

	/**
	 * @return array<int, object>
	 */
	public function get_all() {
		return $this->wpdb->get_results( "SELECT * FROM {$this->table( 'language' )} ORDER BY sort_order ASC, language_id ASC" );
	}

	/**
	 * @param int $language_id
	 */
	public function get_by_id( $language_id ) {
		foreach ( $this->get_all() as $language ) {
			if ( (int) $language->language_id === (int) $language_id ) {
				return $language;
			}
		}
		return null;
	}

	/**
	 * @param string $code
	 */
	public function get_by_code( $code ) {
		$code = sanitize_key( (string) $code );
		return $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table( 'language' )} WHERE code = %s LIMIT 1",
				$code
			)
		);
	}

	public function get_default_language_id() {
		$row = $this->get_by_code( 'ar' );
		if ( $row ) {
			return (int) $row->language_id;
		}
		$all = $this->get_all_active();
		return ! empty( $all ) ? (int) $all[0]->language_id : 1;
	}

	public function get_all_active_sorted() {
		$languages = $this->get_all_active();
		if ( count( $languages ) < 2 ) {
			return $languages;
		}

		$default_id = $this->get_default_language_id();
		usort(
			$languages,
			function ( $a, $b ) use ( $default_id ) {
				$aid = (int) $a->language_id;
				$bid = (int) $b->language_id;
				if ( $aid === $default_id && $bid !== $default_id ) {
					return -1;
				}
				if ( $bid === $default_id && $aid !== $default_id ) {
					return 1;
				}
				return (int) $a->sort_order <=> (int) $b->sort_order;
			}
		);

		return $languages;
	}

	/**
	 * @param array{name: string, code: string, locale: string, sort_order: int, status: int, language_id?: int} $data
	 * @return int
	 */
	public function save( array $data ) {
		$row = array(
			'name'       => isset( $data['name'] ) ? (string) $data['name'] : '',
			'code'       => isset( $data['code'] ) ? sanitize_key( (string) $data['code'] ) : '',
			'locale'     => isset( $data['locale'] ) ? (string) $data['locale'] : '',
			'sort_order' => isset( $data['sort_order'] ) ? (int) $data['sort_order'] : 0,
			'status'     => ! empty( $data['status'] ) ? 1 : 0,
		);
		$formats = array( '%s', '%s', '%s', '%d', '%d' );

		if ( ! empty( $data['language_id'] ) ) {
			$id = (int) $data['language_id'];
			$this->wpdb->update( $this->table( 'language' ), $row, array( 'language_id' => $id ), $formats, array( '%d' ) );
			return $id;
		}

		$this->wpdb->insert( $this->table( 'language' ), $row, $formats );
		return (int) $this->wpdb->insert_id;
	}

	/**
	 * @param int $language_id
	 * @return bool
	 */
	public function delete( $language_id ) {
		$language_id = (int) $language_id;
		if ( $language_id <= 0 || count( $this->get_all() ) <= 1 ) {
			return false;
		}

		foreach ( array( 'manufacturer_description', 'category_description', 'product_description', 'marker_description' ) as $table ) {
			$this->wpdb->delete( $this->table( $table ), array( 'language_id' => $language_id ), array( '%d' ) );
		}
		$this->wpdb->delete( $this->table( 'language' ), array( 'language_id' => $language_id ), array( '%d' ) );
		return true;
	}
}

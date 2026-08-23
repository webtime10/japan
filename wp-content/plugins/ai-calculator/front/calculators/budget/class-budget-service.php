<?php
/**
 * Budget — Service.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Budget_Service {

	/** @var AI_Calculator_Budget_Model */
	private $model;

	public function __construct( AI_Calculator_Budget_Model $model ) {
		$this->model = $model;
	}

	/**
	 * @return int
	 */
	public function get_manufacturer_id() {
		return $this->model->get_manufacturer_id();
	}

	/**
	 * @return int
	 */
	public function get_language_id() {
		return $this->model->get_language_id();
	}

	/**
	 * @return string
	 */
	public function get_polylang_slug() {
		return $this->model->get_polylang_slug();
	}

	/**
	 * @param array<string, mixed> $raw
	 * @return array<string, mixed>
	 */
	public function process( array $raw ) {
		unset( $raw );

		return array(
			'current_step'    => 1,
			'total_steps'     => 10,
			'manufacturer_id' => $this->get_manufacturer_id(),
			'language_id'     => $this->get_language_id(),
			'polylang_slug'   => $this->get_polylang_slug(),
			'catalog_cards'   => $this->model->get_catalog_cards_data(),
		);
	}

	/**
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	public function to_api( array $data ) {
		return array(
			'current_step'    => isset( $data['current_step'] ) ? (int) $data['current_step'] : 1,
			'total_steps'     => isset( $data['total_steps'] ) ? (int) $data['total_steps'] : 10,
			'manufacturer_id' => isset( $data['manufacturer_id'] ) ? (int) $data['manufacturer_id'] : $this->get_manufacturer_id(),
			'language_id'     => isset( $data['language_id'] ) ? (int) $data['language_id'] : $this->get_language_id(),
			'polylang_slug'   => isset( $data['polylang_slug'] ) ? (string) $data['polylang_slug'] : $this->get_polylang_slug(),
			'catalog_cards'   => isset( $data['catalog_cards'] ) && is_array( $data['catalog_cards'] ) ? $data['catalog_cards'] : $this->model->get_catalog_cards_data(),
		);
	}
}

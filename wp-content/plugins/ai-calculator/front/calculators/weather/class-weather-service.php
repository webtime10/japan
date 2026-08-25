<?php
/**
 * Weather — Service.
 *
 * Списки месяцев/регионов для шорткода. Отправка в Laravel — class-weather-ajax.php.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Weather_Service {

	/** @var AI_Calculator_Weather_Model */
	private $model;

	public function __construct( AI_Calculator_Weather_Model $model ) {
		$this->model = $model;
	}

	/**
	 * Данные для template.php (два select).
	 *
	 * @param array<string, mixed> $raw region, month (product_id).
	 * @return array<string, mixed>
	 */
	public function process( array $raw ) {
		$defaults = $this->model->get_defaults();
		$region   = isset( $raw['region'] ) ? $raw['region'] : $defaults['region'];
		$month    = isset( $raw['month'] ) ? (int) $raw['month'] : (int) $defaults['month'];
		$resolved = $this->model->resolve_selection( $region, $month );

		return array(
			'region'   => (string) $resolved['region'],
			'month'    => (int) $resolved['month'],
			'months'   => $this->model->get_months(),
			'regions'  => $this->model->get_regions(),
			'defaults' => $this->model->get_defaults(),
		);
	}

	/**
	 * REST: то же, что уходит в Laravel при выборе month+region.
	 *
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	public function to_api( array $data ) {
		$month  = isset( $data['month'] ) ? (int) $data['month'] : 0;
		$region = isset( $data['region'] ) ? $data['region'] : '';

		if ( $month <= 0 || '' === (string) $region ) {
			return array(
				'months'  => isset( $data['months'] ) && is_array( $data['months'] ) ? $data['months'] : array(),
				'regions' => isset( $data['regions'] ) && is_array( $data['regions'] ) ? $data['regions'] : array(),
			);
		}

		return $this->model->build_laravel_payload( $month, $region );
	}
}

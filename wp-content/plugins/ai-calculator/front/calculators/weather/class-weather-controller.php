<?php
/**
 * Weather — Controller (шорткод, REST, связка Model + Service).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Weather_Controller extends AI_Calculator_Controller_Base {

	/** @var AI_Calculator_Weather_Model */
	private $model;

	/** @var AI_Calculator_Weather_Service */
	private $service;

	public function __construct() {
		$this->model   = new AI_Calculator_Weather_Model();
		$this->service = new AI_Calculator_Weather_Service( $this->model );
	}

	public function slug(): string {
		return 'weather';
	}

	/**
	 * @param array<string, mixed> $input
	 */
	public function collect( array $input ): array {
		$defaults = $this->model->get_defaults();
		$region   = isset( $input['region'] ) ? absint( $input['region'] ) : (int) $defaults['region'];
		$month    = isset( $input['month'] ) ? absint( $input['month'] ) : (int) $defaults['month'];

		return $this->model->resolve_selection( $region, $month );
	}

	/**
	 * @param array<string, mixed> $input
	 */
	public function run( array $input ): array {
		return $this->service->process( $this->collect( $input ) );
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function to_api( array $data ): array {
		return $this->service->to_api( $data );
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function render( array $data ): string {
		ob_start();
		$ai_weather = $data;
		include AI_CALCULATOR_FRONT_PATH . 'calculators/weather/template.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed>|null $data
	 */
	public function enqueue_assets( $data = null ) {
		if ( null === $data ) {
			$data = $this->run( array() );
		}

		wp_enqueue_style( 'ai_calculator_style' );

		wp_enqueue_script(
			'ai_calculator_weather',
			plugins_url( 'assets/js/front/weather-calculator.js', AI_CALCULATOR_FILE ),
			array( 'jquery' ),
			AI_CALCULATOR_VERSION,
			true
		);

		wp_localize_script(
			'ai_calculator_weather',
			'aiCalculatorWeather',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ai_calculator_weather' ),
			)
		);
	}
}

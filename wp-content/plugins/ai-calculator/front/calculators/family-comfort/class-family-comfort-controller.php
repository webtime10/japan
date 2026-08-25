<?php
/**
 * Family Comfort Calculator controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Family_Comfort_Controller extends AI_Calculator_Controller_Base {

	/** @var AI_Calculator_Family_Comfort_Model */
	private $model;

	public function __construct() {
		$this->model = new AI_Calculator_Family_Comfort_Model();
	}

	public function slug(): string {
		return 'family_comfort';
	}

	/**
	 * @param array<string, mixed> $input
	 */
	public function collect( array $input ): array {
		return array();
	}

	/**
	 * @param array<string, mixed> $input
	 */
	public function run( array $input ): array {
		return $this->model->get_view_data();
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function to_api( array $data ): array {
		return $data;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function render( array $data ): string {
		ob_start();
		$ai_family_comfort = $data;
		include AI_CALCULATOR_FRONT_PATH . 'calculators/family-comfort/template.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed>|null $data
	 */
	public function enqueue_assets( $data = null ) {
		wp_enqueue_style(
			'ai_calculator_family_comfort',
			plugins_url( 'assets/css/front/family-comfort-calculator.css', AI_CALCULATOR_FILE ),
			array( 'ai_calculator_style' ),
			AI_CALCULATOR_VERSION . '.family-comfort-slider-29'
		);

		wp_enqueue_script(
			'ai_calculator_family_comfort',
			plugins_url( 'assets/js/front/family-comfort-calculator.js', AI_CALCULATOR_FILE ),
			array(),
			AI_CALCULATOR_VERSION . '.family-comfort-slider-29',
			true
		);

		wp_add_inline_script(
			'ai_calculator_family_comfort',
			'if (typeof window.aiCalculatorFamilyComfortBoot === "function") { window.aiCalculatorFamilyComfortBoot(); }',
			'after'
		);
	}
}

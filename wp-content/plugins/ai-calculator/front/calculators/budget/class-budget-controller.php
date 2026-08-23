<?php
/**
 * Budget — Controller (шорткод, REST, связка Service).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Budget_Controller extends AI_Calculator_Controller_Base {

	/** @var AI_Calculator_Budget_Service */
	private $service;

	public function __construct() {
		$model         = new AI_Calculator_Budget_Model();
		$this->service = new AI_Calculator_Budget_Service( $model );
	}

	public function slug(): string {
		return 'budget';
	}

	/**
	 * @param array<string, mixed> $input
	 */
	public function collect( array $input ): array {
		return $input;
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
		$ai_budget = $data;
		include AI_CALCULATOR_FRONT_PATH . 'calculators/budget/template.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed>|null $data
	 */
	public function enqueue_assets( $data = null ) {
		unset( $data );

		wp_enqueue_style( 'ai_calculator_style' );

		wp_enqueue_style(
			'ai_calculator_datetimepicker',
			plugins_url( 'assets/css/front/jquery.datetimepicker.css', AI_CALCULATOR_FILE ),
			array(),
			AI_CALCULATOR_VERSION
		);

		wp_enqueue_script(
			'ai_calculator_datetimepicker',
			plugins_url( 'assets/js/front/jquery.datetimepicker.full.js', AI_CALCULATOR_FILE ),
			array( 'jquery' ),
			AI_CALCULATOR_VERSION,
			true
		);

		wp_enqueue_script(
			'ai_calculator_budget',
			plugins_url( 'assets/js/front/budget-calculator.js', AI_CALCULATOR_FILE ),
			array( 'jquery', 'ai_calculator_datetimepicker' ),
			AI_CALCULATOR_VERSION,
			true
		);

		$picker_lang = ai_calculator_datetimepicker_lang();

		wp_localize_script(
			'ai_calculator_budget',
			'aiCalculatorBudget',
			array(
				'ajaxUrl'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'ai_calculator_budget' ),
				'datePickerLocale'    => $picker_lang['locale'],
				'datePickerRtl'       => ! empty( $picker_lang['rtl'] ),
				'datePickerWeekStart' => (int) $picker_lang['dayOfWeekStart'],
				'manufacturerId'      => $this->service->get_manufacturer_id(),
				'languageId'          => $this->service->get_language_id(),
				'polylangSlug'        => $this->service->get_polylang_slug(),
				'labels'               => ai_calculator_budget_ui_labels(),
				'travelersPlaceholder'   => ai_calculator_budget_label( 'budget_travelers_placeholder', 'Выберите кол-во' ),
				'childrenAgePlaceholder' => ai_calculator_budget_label( 'budget_children_age_placeholder', 'Возраст 1 ребенка' ),
				'regionPlaceholder'      => ai_calculator_budget_label( 'budget_region_placeholder', 'Напишите регион' ),
				'swissRegions'         => ai_calculator_budget_swiss_regions(),
				'images'               => ai_calculator_budget_images(),
				'orderRouteLabel'      => ai_calculator_budget_order_route_label(),
			)
		);

		$vue_handle = AI_Calculator_Vue3_Assets::enqueue();
		$vue_base   = AI_Calculator_Vue3_Assets::calculator_assets_url( 'budget' );

		wp_enqueue_script(
			'ai-calculator-budget-constants',
			$vue_base . 'constants.js',
			array( 'ai_calculator_budget' ),
			AI_CALCULATOR_VERSION,
			true
		);

		wp_enqueue_script(
			'ai-calculator-budget-vue',
			$vue_base . 'components/Budget.js',
			array( $vue_handle, 'ai-calculator-budget-constants' ),
			AI_CALCULATOR_VERSION,
			true
		);

		wp_enqueue_script(
			'ai-calculator-budget-app',
			$vue_base . 'app.js',
			array( $vue_handle, 'ai-calculator-budget-vue', 'ai_calculator_budget' ),
			AI_CALCULATOR_VERSION,
			true
		);
	}
}

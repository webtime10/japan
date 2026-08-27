<?php
/**
 * Your Ideal Region calculator controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Ideal_Region_Controller extends AI_Calculator_Controller_Base {

	/** @var AI_Calculator_Ideal_Region_Model */
	private $model;

	public function __construct() {
		$this->model = new AI_Calculator_Ideal_Region_Model();
	}

	public function slug(): string {
		return 'ideal_region';
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
		unset( $input );

		$catalog_cards = $this->model->get_catalog_cards_data();
		$config        = require AI_CALCULATOR_FRONT_PATH . 'calculators/ideal-region/config.php';

		return array(
			'catalog_cards'   => $catalog_cards,
			'labels'          => $this->get_ui_labels(),
			'manufacturer_id' => $this->model->get_manufacturer_id(),
			'language_id'     => $this->model->get_language_id(),
			'max_step'        => max( 1, (int) ( $config['max_step'] ?? 1 ) ),
		);
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
		$ai_ideal_region = $data;
		include AI_CALCULATOR_FRONT_PATH . 'calculators/ideal-region/template.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed>|null $data
	 */
	public function enqueue_assets( $data = null ) {
		if ( null === $data ) {
			$data = $this->run( array() );
		}

		wp_enqueue_style(
			'ai_calculator_ideal_region',
			plugins_url( 'assets/css/front/ideal-region-calculator.css', AI_CALCULATOR_FILE ),
			array( 'ai_calculator_style' ),
			AI_CALCULATOR_VERSION . '.ideal-region-vue-29'
		);

		wp_enqueue_script(
			'ai_calculator_ideal_region',
			plugins_url( 'assets/js/front/ideal-region-calculator.js', AI_CALCULATOR_FILE ),
			array(),
			AI_CALCULATOR_VERSION . '.ir-results-2',
			true
		);

		$catalog_cards = isset( $data['catalog_cards'] ) && is_array( $data['catalog_cards'] )
			? $data['catalog_cards']
			: $this->model->get_catalog_cards_data();

		$labels = isset( $data['labels'] ) && is_array( $data['labels'] )
			? $data['labels']
			: $this->get_ui_labels();

		$max_step = isset( $data['max_step'] ) ? max( 1, (int) $data['max_step'] ) : 1;

		$polylang_slug = function_exists( 'ai_calculator_polylang_slug' )
			? (string) ai_calculator_polylang_slug()
			: ( function_exists( 'pll_current_language' ) ? (string) pll_current_language( 'slug' ) : 'he' );

		wp_localize_script(
			'ai_calculator_ideal_region',
			'aiCalculatorIdealRegion',
			array(
				'catalogCards'    => $catalog_cards,
				'labels'          => $labels,
				'manufacturerId'  => $this->model->get_manufacturer_id(),
				'languageId'      => $this->model->get_language_id(),
				'maxStep'         => $max_step,
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'laravelBase'     => class_exists( 'AI_Calculator_Settings' )
					? (string) AI_Calculator_Settings::get_laravel_origin_url()
					: '',
				'nonce'           => wp_create_nonce( 'ai_calculator_ideal_region' ),
				'polylangSlug'    => $polylang_slug,
			)
		);

		$vue_handle = AI_Calculator_Vue3_Assets::enqueue();
		$vue_base   = AI_Calculator_Vue3_Assets::calculator_assets_url( 'ideal-region' );

		wp_enqueue_script(
			'ai-calculator-ideal-region-constants',
			$vue_base . 'constants.js',
			array( 'ai_calculator_ideal_region' ),
			AI_CALCULATOR_VERSION,
			true
		);

		wp_enqueue_script(
			'ai-calculator-ideal-region-vue',
			$vue_base . 'components/IdealRegion.js',
			array( $vue_handle, 'ai-calculator-ideal-region-constants' ),
			AI_CALCULATOR_VERSION . '.ir-more-he-1',
			true
		);

		wp_enqueue_script(
			'ai-calculator-ideal-region-app',
			$vue_base . 'app.js',
			array( $vue_handle, 'ai-calculator-ideal-region-vue', 'ai_calculator_ideal_region' ),
			AI_CALCULATOR_VERSION . '.ir-more-he-1',
			true
		);
	}

	/**
	 * @return array<string, string>
	 */
	private function get_ui_labels(): array {
		if ( function_exists( 'ai_calculator_ideal_region_ui_labels' ) ) {
			return ai_calculator_ideal_region_ui_labels();
		}

		return function_exists( 'ai_calculator_ideal_region_label_defaults' )
			? ai_calculator_ideal_region_label_defaults()
			: array();
	}
}

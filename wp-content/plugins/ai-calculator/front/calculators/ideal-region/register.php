<?php
/**
 * Register Your Ideal Region calculator.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once AI_CALCULATOR_FRONT_PATH . 'calculators/ideal-region/class-ideal-region-ajax.php';
AI_Calculator_Ideal_Region_Ajax::register();

AI_Calculator_Manager::register(
	new AI_Calculator_Ideal_Region_Controller(),
	array(
		'title'      => 'Your Ideal Region',
		'shortcodes' => array( 'ai_ideal_region_calculator' ),
	)
);

add_action(
	'init',
	static function () {
		$render = static function () {
			AI_Calculator_Front_Assets::require_calculator( 'ideal_region' );

			$controller = AI_Calculator_Manager::get( 'ideal_region' );
			if ( ! $controller ) {
				return '';
			}

			return $controller->render( $controller->run( array() ) );
		};

		add_shortcode( 'ai_ideal_region_calculator', $render );
	}
);

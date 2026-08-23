<?php
/**
 * Register Family Comfort Calculator.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

AI_Calculator_Manager::register(
	new AI_Calculator_Family_Comfort_Controller(),
	array(
		'title'      => __( 'Family Comfort Calculator', 'ai-calculator' ),
		'shortcodes' => array( 'ai_family_comfort_calculator', 'family_comfort_calculator' ),
	)
);

add_action(
	'init',
	static function () {
		$render = static function () {
			AI_Calculator_Front_Assets::require_calculator( 'family_comfort' );

			$controller = AI_Calculator_Manager::get( 'family_comfort' );
			if ( ! $controller ) {
				return '';
			}

			return $controller->render( $controller->run( array() ) );
		};

		add_shortcode( 'ai_family_comfort_calculator', $render );
		add_shortcode( 'family_comfort_calculator', $render );
	}
);

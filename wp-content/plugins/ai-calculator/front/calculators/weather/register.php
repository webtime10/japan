<?php
/**
 * Подключение калькулятора weather к Manager + шорткоды.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

AI_Calculator_Manager::register(
	new AI_Calculator_Weather_Controller(),
	array(
		'title'      => __( 'Калькулятор погоды', 'ai-calculator' ),
		'shortcodes' => array( 'ai_weather_calculator' ),
	)
);

require_once AI_CALCULATOR_FRONT_PATH . 'calculators/weather/class-weather-ajax.php';
AI_Calculator_Weather_Ajax::register();

add_action(
	'init',
	static function () {
		$render = static function () {
			AI_Calculator_Front_Assets::require_calculator( 'weather' );

			$controller = AI_Calculator_Manager::get( 'weather' );
			if ( ! $controller ) {
				return '';
			}
			return $controller->render( $controller->run( array() ) );
		};

		add_shortcode( 'ai_weather_calculator', $render );
	}
);

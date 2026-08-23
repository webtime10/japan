<?php
/**
 * Подключение калькулятора budget к Manager + шорткоды.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once AI_CALCULATOR_FRONT_PATH . 'calculators/budget/class-budget-ajax.php';
AI_Calculator_Budget_Ajax::register();

AI_Calculator_Manager::register(
	new AI_Calculator_Budget_Controller(),
	array(
		'title'      => __( 'Калькулятор бюджета', 'ai-calculator' ),
		'shortcodes' => array( 'ai_budget_calculator' ),
	)
);

add_action(
	'init',
	static function () {
		$render = static function () {
			AI_Calculator_Front_Assets::require_calculator( 'budget' );

			$controller = AI_Calculator_Manager::get( 'budget' );
			if ( ! $controller ) {
				return '';
			}
			return $controller->render( $controller->run( array() ) );
		};

		add_shortcode( 'ai_budget_calculator', $render );
	}
);

<?php
/**
 * Register Chat calculator.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

AI_Calculator_Manager::register(
	new AI_Calculator_Chat_Controller(),
	array(
		'title'      => __( 'Chat', 'ai-calculator' ),
		'shortcodes' => array( 'ai_chat_calculator', 'chat_calculator' ),
	)
);

add_action(
	'init',
	static function () {
		$render = static function () {
			AI_Calculator_Front_Assets::require_calculator( 'chat' );

			$controller = AI_Calculator_Manager::get( 'chat' );
			if ( ! $controller ) {
				return '';
			}

			return $controller->render( $controller->run( array() ) );
		};

		add_shortcode( 'ai_chat_calculator', $render );
		add_shortcode( 'chat_calculator', $render );
	}
);

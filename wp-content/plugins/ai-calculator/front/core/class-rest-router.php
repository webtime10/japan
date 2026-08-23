<?php
/**
 * REST API: GET /wp-json/ai-calculator/v1/{calculator}
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Front_Rest_Router {

	const NAMESPACE = 'ai-calculator/v1';

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/(?P<calculator>[a-z0-9_-]+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'handle_calculator' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
				'args'                => array(
					'calculator' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_key',
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'handle_index' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);
	}

	/**
	 * @return bool
	 */
	public static function permission_check() {
		return (bool) apply_filters( 'ai_calculator_rest_allow_read', true );
	}

	/**
	 * @return WP_REST_Response
	 */
	public static function handle_index() {
		return new WP_REST_Response(
			array(
				'namespace'   => self::NAMESPACE,
				'calculators' => AI_Calculator_Manager::slugs(),
			),
			200
		);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response|WP_Error
	 */
	public static function handle_calculator( WP_REST_Request $request ) {
		$slug       = $request->get_param( 'calculator' );
		$controller = AI_Calculator_Manager::get( $slug );

		if ( ! $controller ) {
			return new WP_Error(
				'ai_calculator_not_found',
				sprintf(
					/* translators: %s: calculator slug */
					__( 'Calculator "%s" is not registered.', 'ai-calculator' ),
					$slug
				),
				array( 'status' => 404 )
			);
		}

		$input = $request->get_query_params();
		unset( $input['calculator'] );

		if ( $controller instanceof AI_Calculator_Controller_Base ) {
			$body = $controller->handle( $input );
		} else {
			$data = $controller->run( $input );
			$body = AI_Calculator_Api_Response::wrap( $slug, $input, $controller->to_api( $data ) );
		}

		return new WP_REST_Response( $body, 200 );
	}
}

<?php
/**
 * Admin router.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Router {

	/** @var array<string, string> */
	private static $page_to_route = array(
		'ai_calculator'               => 'dashboard',
		'ai_calculator_languages'     => 'language',
		'ai_calculator_categories'    => 'category',
		'ai_calculator_manufacturers' => 'manufacturer',
		'ai_calculator_products'      => 'product',
	);

	/**
	 * POST save before HTML output (Map Plum pattern).
	 */
	public static function handle_early_save() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $_GET['page'] ) || empty( $_GET['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( 'save' !== sanitize_key( wp_unslash( $_GET['action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			return;
		}

		$page  = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$route = self::route_for_page( $page );
		if ( ! $route || 'dashboard' === $route ) {
			return;
		}

		self::dispatch( $route );
	}

	/**
	 * @param string $page_slug
	 * @return string|null
	 */
	public static function route_for_page( $page_slug ) {
		return isset( self::$page_to_route[ $page_slug ] ) ? self::$page_to_route[ $page_slug ] : null;
	}

	/**
	 * @param string|null $forced_route Route from menu callback.
	 */
	public static function dispatch( $forced_route = null ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'ai-calculator' ) );
		}

		$route = $forced_route;
		if ( ! $route ) {
			$page  = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'ai_calculator'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$route = isset( self::$page_to_route[ $page ] ) ? self::$page_to_route[ $page ] : 'dashboard';
		}

		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'index'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		switch ( $route ) {
			case 'language':
				$controller = new AI_Calculator_Language_Controller( 'language' );
				break;
			case 'category':
				$controller = new AI_Calculator_Category_Controller( 'category' );
				break;
			case 'manufacturer':
				$controller = new AI_Calculator_Manufacturer_Controller( 'manufacturer' );
				break;
			case 'product':
				$controller = new AI_Calculator_Product_Controller( 'product' );
				break;
			default:
				$controller = new AI_Calculator_Dashboard_Controller( 'dashboard' );
				$action     = 'index';
		}

		if ( ! method_exists( $controller, $action ) ) {
			$action = 'index';
		}

		$controller->$action();
	}

	/**
	 * @param string $route
	 */
	public static function page_slug_for_route( $route ) {
		foreach ( self::$page_to_route as $slug => $r ) {
			if ( $r === $route ) {
				return $slug;
			}
		}
		return 'ai_calculator';
	}

	/**
	 * @param string $route
	 * @param string $action
	 * @param int    $id
	 * @param array  $extra
	 */
	public static function url( $route, $action = 'index', $id = 0, $extra = array() ) {
		$slug = self::page_slug_for_route( $route );
		$args = array_merge(
			array(
				'page'   => $slug,
				'action' => $action,
			),
			$extra
		);
		if ( $id > 0 ) {
			$args['id'] = $id;
		}
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}
}

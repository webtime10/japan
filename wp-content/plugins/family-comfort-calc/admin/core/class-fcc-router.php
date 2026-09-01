<?php
/**
 * Admin router.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Router {

	/** @var array<string, string> */
	private static $page_to_route = array(
		'family_comfort_calc'          => 'dashboard',
		'family_comfort_calc_age'      => 'age_category',
		'family_comfort_calc_interest' => 'interest_category',
		'family_comfort_calc_direction'=> 'direction_category',
	);

	/** @var array<string, string> */
	private static $route_to_group = array(
		'age_category'      => 'age',
		'interest_category' => 'interest',
		'direction_category'=> 'direction',
	);

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
	 * @param string $group
	 * @return string
	 */
	public static function page_slug_for_group( $group ) {
		$map = array(
			'age'       => 'family_comfort_calc_age',
			'interest'  => 'family_comfort_calc_interest',
			'direction' => 'family_comfort_calc_direction',
		);
		return isset( $map[ $group ] ) ? $map[ $group ] : 'family_comfort_calc';
	}

	/**
	 * @param string $route
	 * @return string
	 */
	public static function page_slug_for_route( $route ) {
		foreach ( self::$page_to_route as $slug => $r ) {
			if ( $r === $route ) {
				return $slug;
			}
		}
		return 'family_comfort_calc';
	}

	/**
	 * @param string|null $forced_route
	 */
	public static function dispatch( $forced_route = null ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'family-comfort-calc' ) );
		}

		$route = $forced_route;
		if ( ! $route ) {
			$page  = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'family_comfort_calc'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$route = isset( self::$page_to_route[ $page ] ) ? self::$page_to_route[ $page ] : 'dashboard';
		}

		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'index'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		switch ( $route ) {
			case 'age_category':
			case 'interest_category':
			case 'direction_category':
				$group      = self::$route_to_group[ $route ];
				$controller = new FCC_Category_Controller( $route, $group );
				break;
			default:
				$controller = new FCC_Dashboard_Controller( 'dashboard' );
				$action     = 'index';
		}

		if ( ! method_exists( $controller, $action ) ) {
			$action = 'index';
		}

		$controller->$action();
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

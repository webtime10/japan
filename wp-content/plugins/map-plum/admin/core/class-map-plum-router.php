<?php
/**
 * Admin router.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Router {

	/** @var array<string, string> */
	private static $page_to_route = array(
		'map_plum'              => 'dashboard',
		'map_plum_language'     => 'language',
		'map_plum_manufacturer' => 'manufacturer',
		'map_plum_category'     => 'category',
		'map_plum_product'      => 'product',
		'map_plum_marker'       => 'marker',
	);

	/**
	 * @param string $page_slug
	 * @return string|null
	 */
	public static function route_for_page( $page_slug ) {
		return isset( self::$page_to_route[ $page_slug ] ) ? self::$page_to_route[ $page_slug ] : null;
	}

	/**
	 * POST save до вывода HTML (чтобы сработал wp_safe_redirect).
	 */
	public static function handle_early_save() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $_GET['page'] ) || empty( $_GET['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( sanitize_key( wp_unslash( $_GET['action'] ) ) !== 'save' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) !== 'POST' ) {
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
	 * @param string|null $forced_route Route from submenu callback.
	 */
	public static function dispatch( $forced_route = null ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Недостаточно прав.' );
		}

		$route = $forced_route;
		if ( ! $route ) {
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'map_plum'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$route = isset( self::$page_to_route[ $page ] ) ? self::$page_to_route[ $page ] : 'dashboard';
		}

		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'index'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		switch ( $route ) {
			case 'language':
				$controller = new Map_Plum_Language_Controller( $route );
				break;
			case 'manufacturer':
				$controller = new Map_Plum_Manufacturer_Controller( $route );
				break;
			case 'category':
				$controller = new Map_Plum_Category_Controller( $route );
				break;
			case 'product':
				$controller = new Map_Plum_Product_Controller( $route );
				break;
			case 'marker':
				$controller = new Map_Plum_Marker_Controller( $route );
				break;
			default:
				$controller = new Map_Plum_Dashboard_Controller( 'dashboard' );
				$action     = 'index';
		}

		if ( ! method_exists( $controller, $action ) ) {
			$action = 'index';
		}

		$controller->$action();
	}

	/**
	 * @param string $route dashboard|manufacturer|category|product
	 */
	public static function page_slug_for_route( $route ) {
		foreach ( self::$page_to_route as $slug => $r ) {
			if ( $r === $route ) {
				return $slug;
			}
		}
		return 'map_plum';
	}

	/**
	 * @param string $route
	 * @param string $action
	 * @param int    $id
	 */
	public static function url( $route, $action = 'index', $id = 0 ) {
		$slug = self::page_slug_for_route( $route );
		$args = array(
			'page'   => $slug,
			'action' => $action,
		);
		if ( $id > 0 ) {
			$args['id'] = $id;
		}
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}
}

<?php
/**
 * Plugin Name: Map Plum
 * Plugin URI: https://test.com/
 * Description: Catalog admin (OpenCart-style): manufacturers, categories, products.
 * Version: 1.4.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Nordic
 * Author URI: https://test.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: map-plum
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAP_PLUM_FILE', __FILE__ );
define( 'MAP_PLUM_PATH', plugin_dir_path( __FILE__ ) );
define( 'MAP_PLUM_URL', plugin_dir_url( __FILE__ ) );
define( 'MAP_PLUM_VERSION', '1.4.3' );

require_once MAP_PLUM_PATH . 'admin/core/class-map-plum-model.php';
require_once MAP_PLUM_PATH . 'admin/models/class-language-model.php';
require_once MAP_PLUM_PATH . 'inc/map-plum-cantons-registry.php';
require_once MAP_PLUM_PATH . 'inc/map-plum-markers-front.php';
require_once MAP_PLUM_PATH . 'inc/class_map_plum_shortcodes.php';

if ( is_admin() ) {
	require_once MAP_PLUM_PATH . 'admin/bootstrap.php';
}

/**
 * Main plugin class.
 */
final class Map_Plum_Plugin {

	/** @var Map_Plum_Plugin|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		add_action( 'admin_init', array( $this, 'handle_early_save' ), 1 );
		add_action( 'init', array( $this, 'maybe_upgrade_db' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front' ) );
	}

	/**
	 * Сохранение формы до вывода админки.
	 */
	public function handle_early_save() {
		Map_Plum_Router::handle_early_save();
	}

	public function maybe_upgrade_db() {
		if ( get_option( 'map_plum_db_version' ) === MAP_PLUM_VERSION ) {
			return;
		}
		require_once MAP_PLUM_PATH . 'inc/active_bd.php';
		map_plum_active_bd();
		update_option( 'map_plum_db_version', MAP_PLUM_VERSION );
	}

	/**
	 * @param string $hook
	 */
	public function enqueue_admin( $hook ) {
		if ( strpos( $hook, 'map_plum' ) === false ) {
			return;
		}
		wp_enqueue_style(
			'map-plum-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
			array(),
			'4.7.0'
		);
		wp_enqueue_style(
			'map_plum_style_admin',
			plugins_url( 'assets/css/admin/style.css', MAP_PLUM_FILE ),
			array( 'map-plum-font-awesome' ),
			MAP_PLUM_VERSION
		);
		wp_enqueue_media();
		wp_enqueue_script(
			'map_plum_script_admin',
			plugins_url( 'assets/js/admin/scripts.js', MAP_PLUM_FILE ),
			array( 'jquery' ),
			MAP_PLUM_VERSION,
			true
		);

		$localize = array(
			'saveEscape' => '',
		);

		// Если всё же открылась пустая action=save — уводим в список.
		if ( isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'save' && isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page  = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$route = Map_Plum_Router::route_for_page( $page );
			if ( $route ) {
				$localize['saveEscape'] = Map_Plum_Router::url( $route, 'index' );
			}
		}

		wp_localize_script( 'map_plum_script_admin', 'mapPlumAdmin', $localize );
	}

	public function enqueue_front() {
		wp_enqueue_style(
			'map_plum_style',
			plugins_url( 'assets/css/front/style.css', MAP_PLUM_FILE ),
			array(),
			MAP_PLUM_VERSION
		);
		wp_enqueue_script(
			'map_plum_script',
			plugins_url( 'assets/js/front/scripts.js', MAP_PLUM_FILE ),
			array( 'jquery' ),
			MAP_PLUM_VERSION,
			true
		);
	}

	public function add_admin_menu() {
		add_menu_page(
			'Каталог Map Plum',
			'Map Plum',
			'manage_options',
			'map_plum',
			array( $this, 'admin_page' ),
			'dashicons-store',
			56
		);

		add_submenu_page(
			'map_plum',
			'Языки',
			'Языки',
			'manage_options',
			'map_plum_language',
			array( $this, 'admin_page_language' )
		);

		add_submenu_page(
			'map_plum',
			'Регионы',
			'Регионы',
			'manage_options',
			'map_plum_manufacturer',
			array( $this, 'admin_page_manufacturer' )
		);

		add_submenu_page(
			'map_plum',
			'Категории',
			'Категории',
			'manage_options',
			'map_plum_category',
			array( $this, 'admin_page_category' )
		);

		add_submenu_page(
			'map_plum',
			'Округа',
			'Округа',
			'manage_options',
			'map_plum_product',
			array( $this, 'admin_page_product' )
		);

		add_submenu_page(
			'map_plum',
			'Маркеры',
			'Маркеры',
			'manage_options',
			'map_plum_marker',
			array( $this, 'admin_page_marker' )
		);
	}

	public function admin_page() {
		Map_Plum_Router::dispatch( 'dashboard' );
	}

	public function admin_page_language() {
		Map_Plum_Router::dispatch( 'language' );
	}

	public function admin_page_manufacturer() {
		Map_Plum_Router::dispatch( 'manufacturer' );
	}

	public function admin_page_category() {
		Map_Plum_Router::dispatch( 'category' );
	}

	public function admin_page_product() {
		Map_Plum_Router::dispatch( 'product' );
	}

	public function admin_page_marker() {
		Map_Plum_Router::dispatch( 'marker' );
	}

	/**
	 * Plugin activation: create DB tables.
	 */
	public static function activate() {
		require_once MAP_PLUM_PATH . 'inc/active_bd.php';
		map_plum_active_bd();
		update_option( 'map_plum_db_version', MAP_PLUM_VERSION );
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}
}

Map_Plum_Plugin::instance();

register_activation_hook( MAP_PLUM_FILE, array( 'Map_Plum_Plugin', 'activate' ) );
register_deactivation_hook( MAP_PLUM_FILE, array( 'Map_Plum_Plugin', 'deactivate' ) );

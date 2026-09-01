<?php
/**
 * Plugin Name: Family Comfort Calc
 * Plugin URI:  https://switzerland-expert.com
 * Description: Калькулятор семейного комфорта: категории «Возраст детей», «Интересы», «Направления» (MVC админка).
 * Version:     1.1.0
 * Author:      WebTime
 * Text Domain: family-comfort-calc
 * Domain Path: /languages
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FCC_VERSION', '1.1.0' );
define( 'FCC_FILE', __FILE__ );
define( 'FCC_PATH', plugin_dir_path( __FILE__ ) );
define( 'FCC_URL', plugin_dir_url( __FILE__ ) );

require_once FCC_PATH . 'inc/active_bd.php';
require_once FCC_PATH . 'inc/helpers.php';
require_once FCC_PATH . 'admin/core/class-fcc-model.php';
require_once FCC_PATH . 'inc/page-meta.php';
require_once FCC_PATH . 'inc/front-data.php';
require_once FCC_PATH . 'front/class-fcc-shortcode.php';

/**
 * Plugin bootstrap.
 */
final class Family_Comfort_Calc {

	/** @var Family_Comfort_Calc|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( FCC_FILE, array( $this, 'activate' ) );

		require_once FCC_PATH . 'admin/class-fcc-page-meta-box.php';
		FCC_Page_Meta_Box::register();
		FCC_Shortcode::register();

		if ( is_admin() ) {
			require_once FCC_PATH . 'admin/bootstrap.php';
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
			add_action( 'admin_init', array( 'FCC_Router', 'handle_early_save' ) );
		}
	}

	public function activate() {
		fcc_active_bd();
		fcc_seed_languages();
		update_option( 'fcc_db_version', FCC_VERSION );
	}

	/**
	 * @param string $hook
	 */
	public function enqueue_admin( $hook ) {
		if ( strpos( $hook, 'family_comfort_calc' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'fcc-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
			array(),
			'4.7.0'
		);
		wp_enqueue_style(
			'fcc-admin-style',
			FCC_URL . 'assets/css/admin/style.css',
			array( 'fcc-font-awesome' ),
			FCC_VERSION
		);

		wp_enqueue_script(
			'fcc-admin-script',
			FCC_URL . 'assets/js/admin/scripts.js',
			array( 'jquery' ),
			FCC_VERSION,
			true
		);

		$localize = array(
			'pluginUrl' => FCC_URL,
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		);

		if ( isset( $_GET['action'] ) && 'save' === sanitize_key( wp_unslash( $_GET['action'] ) ) && isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page  = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$route = FCC_Router::route_for_page( $page );
			if ( $route ) {
				$localize['saveEscape'] = FCC_Router::url( $route, 'index' );
			}
		}

		wp_localize_script( 'fcc-admin-script', 'fccAdmin', $localize );
	}

	public function register_menu() {
		add_menu_page(
			__( 'Family Comfort', 'family-comfort-calc' ),
			__( 'Family Comfort', 'family-comfort-calc' ),
			'manage_options',
			'family_comfort_calc',
			array( $this, 'render_admin_page' ),
			'dashicons-groups',
			58
		);

		add_submenu_page(
			'family_comfort_calc',
			__( 'Главная', 'family-comfort-calc' ),
			__( 'Главная', 'family-comfort-calc' ),
			'manage_options',
			'family_comfort_calc',
			array( $this, 'render_admin_page' )
		);

		foreach ( fcc_get_group_types() as $group => $label ) {
			$slug = FCC_Router::page_slug_for_group( $group );
			add_submenu_page(
				'family_comfort_calc',
				$label,
				$label,
				'manage_options',
				$slug,
				array( $this, 'render_admin_page' )
			);
		}
	}

	public function render_admin_page() {
		FCC_Router::dispatch();
	}
}

Family_Comfort_Calc::instance();

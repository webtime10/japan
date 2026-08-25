<?php
/**
 * Plugin Name: AI Calculator
 * Plugin URI: https://example.com/
 * Description: Travel calculators — catalog in WP, data via REST to Laravel.
 * Version: 1.8.7
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Nordic
 * Author URI: https://example.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-calculator
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AI_CALCULATOR_FILE', __FILE__ );
define( 'AI_CALCULATOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'AI_CALCULATOR_URL', plugin_dir_url( __FILE__ ) );
define( 'AI_CALCULATOR_VERSION', '1.8.7' );

require_once AI_CALCULATOR_PATH . 'admin/core/class-ai-calculator-model.php';
require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';
require_once AI_CALCULATOR_PATH . 'inc/helpers.php';
require_once AI_CALCULATOR_PATH . 'front/product/class-product-view.php';
require_once AI_CALCULATOR_PATH . 'front/bootstrap.php';
require_once AI_CALCULATOR_PATH . 'shortcodes/class-ai-calculator-shortcodes.php';

if ( is_admin() ) {
	require_once AI_CALCULATOR_PATH . 'admin/bootstrap.php';
}

/**
 * Main plugin class.
 */
final class AI_Calculator_Plugin {

	/** @var AI_Calculator_Plugin|null */
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
		add_action( 'admin_init', array( $this, 'maybe_upgrade_db' ), 0 );
		add_action( 'admin_init', array( $this, 'handle_early_save' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front' ) );
	}

	public function handle_early_save() {
		AI_Calculator_Router::handle_early_save();
	}

	public function maybe_upgrade_db() {
		require_once AI_CALCULATOR_PATH . 'inc/active_bd.php';
		global $wpdb;
		$prefix = $wpdb->prefix . 'ai_calculator_';
		ai_calculator_maybe_add_category_manufacturer_column( $prefix );
		ai_calculator_maybe_add_product_manufacturer_column( $prefix );
		ai_calculator_maybe_add_product_gallery_image_columns( $prefix );
		ai_calculator_maybe_fix_category_auto_increment( $prefix );
		ai_calculator_maybe_fix_manufacturer_auto_increment( $prefix );
		ai_calculator_maybe_fix_product_auto_increment( $prefix );
		ai_calculator_maybe_create_attribute_tables( $prefix );
		ai_calculator_maybe_add_product_description_block_columns( $prefix );
		ai_calculator_maybe_add_product_description_dop1_column( $prefix );
		ai_calculator_maybe_add_attribute_extended_columns( $prefix );
		ai_calculator_maybe_add_product_attribute_columns( $prefix );
		ai_calculator_drop_prompt_tables( $prefix );

		if ( get_option( 'ai_calculator_db_version' ) === AI_CALCULATOR_VERSION ) {
			return;
		}
		ai_calculator_active_bd();
		update_option( 'ai_calculator_db_version', AI_CALCULATOR_VERSION );
	}

	/**
	 * @param string $hook
	 */
	public function enqueue_admin( $hook ) {
		if ( strpos( $hook, 'ai_calculator' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'ai-calculator-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
			array(),
			'4.7.0'
		);
		wp_enqueue_style(
			'ai_calculator_style_admin',
			plugins_url( 'assets/css/admin/style.css', AI_CALCULATOR_FILE ),
			array( 'ai-calculator-font-awesome' ),
			AI_CALCULATOR_VERSION
		);

		wp_enqueue_script(
			'ai_calculator_script_admin',
			plugins_url( 'assets/js/admin/scripts.js', AI_CALCULATOR_FILE ),
			array( 'jquery' ),
			AI_CALCULATOR_VERSION . '.fc-product-form-5',
			true
		);

		$localize = array(
			'pluginUrl'  => AI_CALCULATOR_URL,
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'ai_calculator_admin' ),
			'saveEscape' => '',
			'mediaTitle' => __( 'Выберите изображение', 'ai-calculator' ),
			'mediaButton' => __( 'Использовать', 'ai-calculator' ),
		);

		if ( isset( $_GET['action'] ) && 'save' === sanitize_key( wp_unslash( $_GET['action'] ) ) && isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page  = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$route = AI_Calculator_Router::route_for_page( $page );
			if ( $route ) {
				$localize['saveEscape'] = AI_Calculator_Router::url( $route, 'index' );
			}
		}

		wp_localize_script(
			'ai_calculator_script_admin',
			'aiCalculatorAdmin',
			$localize
		);

		if ( 'toplevel_page_ai_calculator' === $hook ) {
			wp_enqueue_media();

			wp_enqueue_script(
				'ai_calculator_dashboard_settings',
				plugins_url( 'assets/js/admin/dashboard-settings.js', AI_CALCULATOR_FILE ),
				array( 'jquery', 'ai_calculator_script_admin' ),
				AI_CALCULATOR_VERSION,
				true
			);
		}

		if ( false !== strpos( $hook, 'ai_calculator_products' ) ) {
			wp_enqueue_media();

			wp_enqueue_script(
				'ai_calculator_product_related',
				plugins_url( 'assets/js/admin/product-related.js', AI_CALCULATOR_FILE ),
				array( 'jquery', 'ai_calculator_script_admin' ),
				AI_CALCULATOR_VERSION,
				true
			);
		}
	}

	public function enqueue_front() {
		AI_Calculator_Front_Assets::register();
	}

	public function add_admin_menu() {
		add_menu_page(
			__( 'AI Calculator', 'ai-calculator' ),
			__( 'AI Calculator', 'ai-calculator' ),
			'manage_options',
			'ai_calculator',
			array( $this, 'admin_page' ),
			'dashicons-calculator',
			57
		);

		add_submenu_page(
			'ai_calculator',
			__( 'Home', 'ai-calculator' ),
			__( 'Home', 'ai-calculator' ),
			'manage_options',
			'ai_calculator',
			array( $this, 'admin_page' )
		);

		add_submenu_page(
			'ai_calculator',
			__( 'Languages', 'ai-calculator' ),
			__( 'Languages', 'ai-calculator' ),
			'manage_options',
			'ai_calculator_languages',
			array( $this, 'admin_page_languages' )
		);

		add_submenu_page(
			'ai_calculator',
			__( 'Categories', 'ai-calculator' ),
			__( 'Categories', 'ai-calculator' ),
			'manage_options',
			'ai_calculator_categories',
			array( $this, 'admin_page_categories' )
		);

		add_submenu_page(
			'ai_calculator',
			__( 'Калькуляторы', 'ai-calculator' ),
			__( 'Калькуляторы', 'ai-calculator' ),
			'manage_options',
			'ai_calculator_manufacturers',
			array( $this, 'admin_page_manufacturers' )
		);

		add_submenu_page(
			'ai_calculator',
			__( 'Products', 'ai-calculator' ),
			__( 'Products', 'ai-calculator' ),
			'manage_options',
			'ai_calculator_products',
			array( $this, 'admin_page_products' )
		);

		add_submenu_page(
			'ai_calculator',
			__( 'Группы атрибутов', 'ai-calculator' ),
			__( 'Группы атрибутов', 'ai-calculator' ),
			'manage_options',
			'ai_calculator_attribute_groups',
			array( $this, 'admin_page_attribute_groups' )
		);

		add_submenu_page(
			'ai_calculator',
			__( 'Атрибуты', 'ai-calculator' ),
			__( 'Атрибуты', 'ai-calculator' ),
			'manage_options',
			'ai_calculator_attributes',
			array( $this, 'admin_page_attributes' )
		);

		add_submenu_page(
			'ai_calculator',
			__( 'DB Dump', 'ai-calculator' ),
			__( 'DB Dump', 'ai-calculator' ),
			'manage_options',
			'ai_calculator_db_dump',
			array( $this, 'admin_page_db_dump' )
		);
	}

	public function admin_page() {
		AI_Calculator_Router::dispatch( 'dashboard' );
	}

	public function admin_page_languages() {
		AI_Calculator_Router::dispatch( 'language' );
	}

	public function admin_page_categories() {
		AI_Calculator_Router::dispatch( 'category' );
	}

	public function admin_page_manufacturers() {
		AI_Calculator_Router::dispatch( 'manufacturer' );
	}

	public function admin_page_products() {
		AI_Calculator_Router::dispatch( 'product' );
	}

	public function admin_page_attribute_groups() {
		AI_Calculator_Router::dispatch( 'attribute_group' );
	}

	public function admin_page_attributes() {
		AI_Calculator_Router::dispatch( 'attribute' );
	}

	public function admin_page_db_dump() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'ai-calculator' ) );
		}
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'AI Calculator — DB Dump', 'ai-calculator' ) . '</h1>';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo ( new AI_Calculator_Db_Dump_Tool() )->render();
		echo '</div>';
	}

	public static function activate() {
		require_once AI_CALCULATOR_PATH . 'inc/active_bd.php';
		global $wpdb;
		$prefix = $wpdb->prefix . 'ai_calculator_';
		ai_calculator_active_bd();
		ai_calculator_drop_prompt_tables( $prefix );
		update_option( 'ai_calculator_db_version', AI_CALCULATOR_VERSION );
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}
}

AI_Calculator_Plugin::instance();

register_activation_hook( AI_CALCULATOR_FILE, array( 'AI_Calculator_Plugin', 'activate' ) );
register_deactivation_hook( AI_CALCULATOR_FILE, array( 'AI_Calculator_Plugin', 'deactivate' ) );

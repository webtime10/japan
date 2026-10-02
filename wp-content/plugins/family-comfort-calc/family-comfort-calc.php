<?php
/**
 * Plugin Name: Family Comfort Calc
 * Plugin URI:  https://switzerland-expert.com
 * Description: Калькулятор семейного комфорта: категории и посты (MVC админка в стиле OpenCart).
 * Version:     1.5.8
 * Author:      WebTime
 * Text Domain: family-comfort-calc
 * Domain Path: /languages
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FCC_VERSION', '1.5.8' );
define( 'FCC_FILE', __FILE__ );
define( 'FCC_PATH', plugin_dir_path( __FILE__ ) );
define( 'FCC_URL', plugin_dir_url( __FILE__ ) );

require_once FCC_PATH . 'inc/active_bd.php';
require_once FCC_PATH . 'inc/helpers.php';
require_once FCC_PATH . 'inc/ajax.php';
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

		FCC_Shortcode::register();
		fcc_register_ajax();

		add_action( 'init', array( $this, 'maybe_upgrade' ), 5 );

		if ( is_admin() ) {
			require_once FCC_PATH . 'admin/bootstrap.php';
			require_once FCC_PATH . 'admin/class-fcc-page-meta-box.php';
			FCC_Page_Meta_Box::register();
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
			add_action( 'admin_init', array( 'FCC_Router', 'handle_early_save' ) );
		}
	}

	public function activate() {
		fcc_active_bd();
		fcc_seed_languages();
		$this->unregister_legacy_cpt();
		flush_rewrite_rules();
		update_option( 'fcc_db_version', FCC_VERSION );
	}

	/**
	 * Create new tables / cleanup after version bump.
	 */
	public function maybe_upgrade() {
		$stored = (string) get_option( 'fcc_db_version', '' );
		if ( $stored === FCC_VERSION ) {
			return;
		}

		fcc_active_bd();
		fcc_seed_languages();
		$this->unregister_legacy_cpt();
		flush_rewrite_rules( false );
		update_option( 'fcc_db_version', FCC_VERSION );
	}

	/**
	 * Remove mistaken WP CPT from earlier version.
	 */
	private function unregister_legacy_cpt() {
		if ( post_type_exists( 'fcc_post' ) ) {
			unregister_post_type( 'fcc_post' );
		}
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

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'family_comfort_calc_post' === $page ) {
			wp_enqueue_media();
			wp_enqueue_style(
				'fcc-page-meta',
				FCC_URL . 'assets/css/admin/page-meta.css',
				array(),
				FCC_VERSION
			);
			wp_enqueue_script(
				'fcc-page-meta',
				FCC_URL . 'assets/js/admin/page-meta.js',
				array( 'jquery' ),
				FCC_VERSION,
				true
			);
			wp_localize_script(
				'fcc-page-meta',
				'fccPageMeta',
				array(
					'maxTags' => fcc_get_page_tags_max(),
					'i18n'    => array(
						'selectImage' => __( 'Выбрать изображение', 'family-comfort-calc' ),
						'useImage'    => __( 'Использовать', 'family-comfort-calc' ),
					),
				)
			);

			wp_enqueue_script(
				'fcc-post-city-page',
				FCC_URL . 'assets/js/admin/post-city-page.js',
				array( 'jquery', 'fcc-page-meta' ),
				FCC_VERSION,
				true
			);
			wp_localize_script(
				'fcc-post-city-page',
				'fccCityPage',
				array(
					'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'fcc_search_city_pages' ),
					'minChars' => fcc_page_search_min_chars(),
					'i18n'     => array(
						'searching' => __( 'Поиск…', 'family-comfort-calc' ),
						'empty'     => __( 'Ничего не найдено среди страниц Default', 'family-comfort-calc' ),
						'error'     => __( 'Ошибка поиска', 'family-comfort-calc' ),
						'selected'  => __( 'Выбрано:', 'family-comfort-calc' ),
					),
				)
			);
			wp_localize_script(
				'fcc-post-city-page',
				'fccAttractionPage',
				array(
					'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'fcc_search_attraction_pages' ),
					'minChars' => fcc_page_search_min_chars(),
					'i18n'     => array(
						'searching'  => __( 'Поиск…', 'family-comfort-calc' ),
						'empty'      => __( 'Ничего не найдено среди страниц Default', 'family-comfort-calc' ),
						'error'      => __( 'Ошибка поиска', 'family-comfort-calc' ),
						'selected'   => __( 'Страница выбрана', 'family-comfort-calc' ),
						'added'      => __( 'Добавлено:', 'family-comfort-calc' ),
						'limitOrDup' => __( 'Уже добавлено или достигнут лимит', 'family-comfort-calc' ),
						'needPage'   => __( 'Сначала выберите страницу', 'family-comfort-calc' ),
						'needName'   => __( 'Введите название достопримечательности', 'family-comfort-calc' ),
					),
				)
			);

			wp_enqueue_script(
				'fcc-post-auto-tags',
				FCC_URL . 'assets/js/admin/post-auto-tags.js',
				array( 'jquery' ),
				FCC_VERSION,
				true
			);
			wp_localize_script(
				'fcc-post-auto-tags',
				'fccAutoTags',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'fcc_direction_auto_tags' ),
				)
			);
		}

		$localize = array(
			'pluginUrl' => FCC_URL,
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		);

		if ( isset( $_GET['action'] ) && 'save' === sanitize_key( wp_unslash( $_GET['action'] ) ) && isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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

		add_submenu_page(
			'family_comfort_calc',
			__( 'Посты', 'family-comfort-calc' ),
			__( 'Посты', 'family-comfort-calc' ),
			'manage_options',
			'family_comfort_calc_post',
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

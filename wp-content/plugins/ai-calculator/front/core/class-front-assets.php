<?php
/**
 * Фронтовые ассеты — только register глобально, enqueue на страницах со шорткодом.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Front_Assets {

	/** @var array<string, true> */
	private static $active = array();

	/** @var bool */
	private static $booted = false;

	/**
	 * @return void
	 */
	public static function init() {
		if ( self::$booted ) {
			return;
		}

		self::$booted = true;

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'detect_in_content' ), 9 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_active' ), 20 );
	}

	/**
	 * @param string $slug
	 * @return void
	 */
	public static function mark( $slug ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			return;
		}

		self::$active[ $slug ] = true;
	}

	/**
	 * @return void
	 */
	public static function register() {
		wp_register_style(
			'ai_calculator_style',
			plugins_url( 'assets/css/front/style.css', AI_CALCULATOR_FILE ),
			array(),
			AI_CALCULATOR_VERSION
		);

		wp_register_style(
			'ai_calculator_product',
			plugins_url( 'assets/css/front/product.css', AI_CALCULATOR_FILE ),
			array( 'ai_calculator_style' ),
			AI_CALCULATOR_VERSION
		);

		AI_Calculator_Vue3_Assets::register();
	}

	/**
	 * @return void
	 */
	public static function detect_in_content() {
		if ( is_admin() ) {
			return;
		}

		$content = self::get_page_content();
		if ( '' === $content ) {
			return;
		}

		if ( class_exists( 'AI_Calculator_Manager' ) ) {
			foreach ( AI_Calculator_Manager::all_meta() as $slug => $meta ) {
				if ( empty( $meta['shortcodes'] ) || ! is_array( $meta['shortcodes'] ) ) {
					continue;
				}

				foreach ( $meta['shortcodes'] as $shortcode ) {
					if ( has_shortcode( $content, $shortcode ) ) {
						self::mark( $slug );
						break;
					}
				}
			}
		}

		if ( has_shortcode( $content, 'ai_calculator_product' ) ) {
			self::mark( 'product' );
		}
	}

	/**
	 * @return void
	 */
	public static function enqueue_active() {
		if ( empty( self::$active ) ) {
			return;
		}

		if ( ! empty( self::$active['product'] ) ) {
			wp_enqueue_style( 'ai_calculator_product' );
		}

		if ( ! class_exists( 'AI_Calculator_Manager' ) ) {
			return;
		}

		foreach ( array_keys( self::$active ) as $slug ) {
			if ( 'product' === $slug ) {
				continue;
			}

			$controller = AI_Calculator_Manager::get( $slug );
			if ( ! $controller || ! method_exists( $controller, 'enqueue_assets' ) ) {
				continue;
			}

			$controller->enqueue_assets();
		}
	}

	/**
	 * Пометить калькулятор и подключить ассеты (для шорткода / виджета).
	 *
	 * @param string $slug
	 * @return void
	 */
	public static function require_calculator( $slug ) {
		self::mark( $slug );

		if ( did_action( 'wp_enqueue_scripts' ) ) {
			self::enqueue_calculator( $slug );
		}
	}

	/**
	 * @param string $slug
	 * @return void
	 */
	public static function enqueue_calculator( $slug ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			return;
		}

		if ( 'product' === $slug ) {
			wp_enqueue_style( 'ai_calculator_product' );
			return;
		}

		$controller = AI_Calculator_Manager::get( $slug );
		if ( $controller && method_exists( $controller, 'enqueue_assets' ) ) {
			$controller->enqueue_assets();
		}
	}

	/**
	 * @return string
	 */
	private static function get_page_content() {
		global $post;

		if ( $post instanceof WP_Post && ! empty( $post->post_content ) ) {
			return (string) $post->post_content;
		}

		return '';
	}
}

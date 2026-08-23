<?php
/**
 * Список всех front-калькуляторов (аналог routes / service provider).
 * Не путать с admin Router — это только реестр slug → Controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Manager {

	/** @var array<string, AI_Calculator_Controller_Interface> */
	private static $controllers = array();

	/** @var array<string, array{title: string, shortcodes: array<int, string>}> */
	private static $meta = array();

	/**
	 * Подключить калькулятор (вызывается из calculators/{slug}/register.php).
	 *
	 * @param AI_Calculator_Controller_Interface $controller
	 * @param array{title?: string, shortcodes?: array<int, string>} $meta
	 */
	public static function register( AI_Calculator_Controller_Interface $controller, array $meta = array() ) {
		$slug = $controller->slug();
		self::$controllers[ $slug ] = $controller;
		self::$meta[ $slug ]        = array(
			'title'      => isset( $meta['title'] ) ? (string) $meta['title'] : $slug,
			'shortcodes' => isset( $meta['shortcodes'] ) && is_array( $meta['shortcodes'] ) ? array_values( $meta['shortcodes'] ) : array(),
		);
	}

	/**
	 * @param string $slug
	 * @return AI_Calculator_Controller_Interface|null
	 */
	public static function get( $slug ) {
		$slug = sanitize_key( $slug );
		return isset( self::$controllers[ $slug ] ) ? self::$controllers[ $slug ] : null;
	}

	/**
	 * @return array<int, string>
	 */
	public static function slugs() {
		return array_keys( self::$controllers );
	}

	/**
	 * @return array<string, array{title: string, shortcodes: array<int, string>}>
	 */
	public static function all_meta() {
		return self::$meta;
	}
}

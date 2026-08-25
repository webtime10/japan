<?php
/**
 * Vue 3 — локальный файл, если на странице ещё не подключён.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Vue3_Assets {

	const HANDLE = 'ai-calculator-vue3';

	const VERSION = '3.5.13';

	/** @var array<int, string> */
	private static $known_handles = array(
		'vue',
		'vue3',
		'vue-js',
		'vue.global',
		'vue-global',
		'vue-runtime',
	);

	/**
	 * URL локального Vue 3 (assets/js/front/vue.js).
	 *
	 * @return string
	 */
	public static function script_url() {
		return plugins_url( 'assets/js/front/vue.js', AI_CALCULATOR_FILE );
	}

	/**
	 * Зарегистрировать Vue 3 локально (без enqueue).
	 *
	 * @return void
	 */
	public static function register() {
		if ( wp_script_is( self::HANDLE, 'registered' ) ) {
			return;
		}

		wp_register_script(
			self::HANDLE,
			self::script_url(),
			array(),
			self::VERSION,
			true
		);
	}

	/**
	 * Подключить Vue 3 или вернуть handle уже зарегистрированного скрипта.
	 *
	 * @return string Script handle для dependency.
	 */
	public static function enqueue() {
		$existing = self::find_existing_handle();
		if ( $existing ) {
			if ( ! wp_script_is( $existing, 'enqueued' ) ) {
				wp_enqueue_script( $existing );
			}
			return $existing;
		}

		self::register();

		wp_enqueue_script( self::HANDLE );

		return self::HANDLE;
	}

	/**
	 * @return string|null
	 */
	public static function find_existing_handle() {
		global $wp_scripts;

		if ( ! $wp_scripts instanceof WP_Scripts ) {
			return null;
		}

		$handles = array_unique(
			array_merge(
				(array) $wp_scripts->queue,
				array_keys( (array) $wp_scripts->registered )
			)
		);

		foreach ( $handles as $handle ) {
			if ( ! is_string( $handle ) || '' === $handle ) {
				continue;
			}

			if ( self::is_vue3_handle( $handle, $wp_scripts ) ) {
				return $handle;
			}
		}

		return null;
	}

	/**
	 * @param string     $handle
	 * @param WP_Scripts $wp_scripts
	 */
	private static function is_vue3_handle( $handle, $wp_scripts ) {
		if ( in_array( $handle, self::$known_handles, true ) ) {
			return true;
		}

		if ( ! isset( $wp_scripts->registered[ $handle ] ) ) {
			return false;
		}

		$src = (string) $wp_scripts->registered[ $handle ]->src;
		if ( '' === $src ) {
			return false;
		}

		if ( preg_match( '#vue@3|/vue/dist/vue\.global(\.prod)?\.js#i', $src ) ) {
			return true;
		}

		if ( preg_match( '#vue\.global(\.prod)?(\.min)?\.js#i', $src ) && ! preg_match( '#vue@2|vue\.js@2|/vue/dist/vue\.js#i', $src ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Базовый URL assets/vue3/ai-calculator/{calculator}/.
	 *
	 * @param string $calculator
	 * @return string
	 */
	public static function calculator_assets_url( $calculator ) {
		$calculator = sanitize_key( $calculator );

		return plugins_url( 'assets/vue3/ai-calculator/' . $calculator . '/', AI_CALCULATOR_FILE );
	}
}

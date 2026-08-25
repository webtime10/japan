<?php
/**
 * Настройки плагина: URL для отправки на Laravel.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Settings {

	const OPTION_ACTIVE = 'ai_calculator_active_remote_url';
	const OPTION_API_KEY = 'ai_calculator_remote_api_key';

	/** Префикс маршрутов Laravel: POST /api/plugins/{slug} */
	const LARAVEL_PLUGINS_API_PREFIX = 'api/plugins';

	/**
	 * Значение из БД — как ввели (без http:// и без обрезки пути).
	 *
	 * @return string
	 */
	public static function get_active_url() {
		return self::sanitize_stored_url( (string) get_option( self::OPTION_ACTIVE, '' ) );
	}

	/**
	 * Только trim и sanitize; схему и путь не меняем.
	 *
	 * @param string $url
	 * @return string
	 */
	public static function sanitize_stored_url( $url ) {
		$url = trim( sanitize_text_field( wp_unslash( (string) $url ) ) );
		if ( '' === $url ) {
			return '';
		}

		return str_replace( array( "\r", "\n" ), '', $url );
	}

	/**
	 * Хост Laravel из настроек (без /api/plugins/…).
	 *
	 * @return string
	 */
	public static function get_laravel_base_url() {
		return self::extract_laravel_base( self::get_active_url() );
	}

	/**
	 * Origin Laravel со схемой. Хост без http(s) — всегда https://
	 *
	 * @return string
	 */
	public static function get_laravel_origin_url() {
		$base = self::get_laravel_base_url();
		if ( '' === $base ) {
			return '';
		}

		if ( ! preg_match( '#^https?://#i', $base ) ) {
			// Локальные .loc/.test — http; иначе https.
			$scheme = preg_match( '/\.(loc|test|localhost)(:\d+)?$/i', $base ) ? 'http://' : 'https://';
			$base   = $scheme . ltrim( $base, '/' );
		}

		return untrailingslashit( $base );
	}

	/**
	 * Убирает суффикс /api/plugins/{slug}, если сохранили полный endpoint.
	 *
	 * @param string $stored
	 * @return string
	 */
	public static function extract_laravel_base( $stored ) {
		$base = untrailingslashit( trim( (string) $stored ) );
		if ( '' === $base ) {
			return '';
		}

		$prefix = preg_quote( self::LARAVEL_PLUGINS_API_PREFIX, '#' );
		if ( preg_match( '#^(.*?)/' . $prefix . '(?:/.*)?$#', $base, $matches ) ) {
			$base = untrailingslashit( $matches[1] );
		}

		return $base;
	}

	/**
	 * Путь endpoint на Laravel для плагина (slug калькулятора).
	 *
	 * @param string $plugin_slug
	 * @return string
	 */
	public static function get_laravel_plugin_path( $plugin_slug ) {
		$plugin_slug = sanitize_key( $plugin_slug );
		if ( '' === $plugin_slug ) {
			return '';
		}

		return '/' . self::LARAVEL_PLUGINS_API_PREFIX . '/' . $plugin_slug;
	}

	/**
	 * Полный URL для wp_remote_post: база + /api/plugins/{slug}, http:// при отсутствии схемы.
	 *
	 * @param string $plugin_slug Slug калькулятора (= slug маршрута в Laravel).
	 * @return string
	 */
	public static function get_remote_request_url( $plugin_slug ) {
		$base = self::get_laravel_base_url();
		if ( '' === $base ) {
			return '';
		}

		$path = self::get_laravel_plugin_path( $plugin_slug );
		if ( '' === $path ) {
			return '';
		}

		$url = $base;
		if ( ! preg_match( '#^https?://#i', $url ) ) {
			$url = 'http://' . ltrim( $url, '/' );
		}

		/**
		 * @param string $url
		 * @param string $plugin_slug
		 * @param string $base
		 */
		$url = apply_filters( 'ai_calculator_laravel_request_url', untrailingslashit( $url ) . $path, $plugin_slug, $base );

		return $url;
	}

	/**
	 * Ключ для Laravel (X-Plugin-Api-Key). Можно задать в wp-config: define( 'AI_CALCULATOR_LARA_API_KEY', '...' );
	 *
	 * @return string
	 */
	public static function get_api_key() {
		if ( defined( 'AI_CALCULATOR_LARA_API_KEY' ) && is_string( AI_CALCULATOR_LARA_API_KEY ) ) {
			return trim( AI_CALCULATOR_LARA_API_KEY );
		}

		return (string) get_option( self::OPTION_API_KEY, '' );
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	public static function set_active_url( $url ) {
		$url = self::sanitize_stored_url( $url );
		if ( '' === $url ) {
			return false;
		}

		update_option( self::OPTION_ACTIVE, $url, false );
		return true;
	}

	/**
	 * @param string $api_key
	 * @return bool
	 */
	public static function set_api_key( $api_key ) {
		if ( defined( 'AI_CALCULATOR_LARA_API_KEY' ) ) {
			return false;
		}

		update_option( self::OPTION_API_KEY, trim( sanitize_text_field( wp_unslash( (string) $api_key ) ) ), false );
		return true;
	}
}

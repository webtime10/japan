<?php
/**
 * AJAX: выбранные месяц + регион (после того как оба select заполнены).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Weather_Ajax {

	const PLUGIN_SLUG = 'weather';

	public static function register() {
		add_action( 'wp_ajax_ai_calculator_weather_data', array( __CLASS__, 'handle' ) );
		add_action( 'wp_ajax_nopriv_ai_calculator_weather_data', array( __CLASS__, 'handle' ) );
	}

	/**
	 * @param array{month_name: string, region_name: string} $sent
	 * @param string               $laravel_base
	 * @param string               $laravel_path
	 * @param array<string, mixed> $remote_result
	 */
	private static function build_debug_message( array $sent, $laravel_base, $laravel_path, array $remote_result ) {
		$lines   = array();
		$lines[] = isset( $sent['month_name'] ) ? (string) $sent['month_name'] : '';
		$lines[] = isset( $sent['region_name'] ) ? (string) $sent['region_name'] : '';
		if ( ! empty( $sent['language'] ) ) {
			$lines[] = (string) $sent['language'];
		}

		if ( '' === $laravel_base ) {
			$lines[] = '';
			$lines[] = __( 'Laravel: URL не задан (AI Calculator → Home).', 'ai-calculator' );
			return implode( "\n", $lines );
		}

		$lines[] = '';
		$lines[] = '→ ' . $laravel_base . $laravel_path;

		if ( ! empty( $remote_result['message'] ) ) {
			$lines[] = (string) $remote_result['message'];
			if ( isset( $remote_result['code'] ) && 401 === (int) $remote_result['code'] ) {
				$lines[] = '';
				$lines[] = __( 'Проверьте API ключ: AI Calculator → Home = PLUGIN_WEATHER_API_KEY в .env Laravel.', 'ai-calculator' );
			}
			return implode( "\n", $lines );
		}

		if ( ! empty( $remote_result['ok'] ) ) {
			$code    = isset( $remote_result['code'] ) ? (int) $remote_result['code'] : 0;
			$lines[] = __( 'Laravel: OK', 'ai-calculator' ) . ' (HTTP ' . $code . ')';
		} else {
			$err = isset( $remote_result['error'] ) ? (string) $remote_result['error'] : __( 'ошибка', 'ai-calculator' );
			$lines[] = __( 'Laravel: ошибка', 'ai-calculator' ) . ' — ' . $err;
			if ( isset( $remote_result['code'] ) && 401 === (int) $remote_result['code'] ) {
				$lines[] = __( 'Проверьте API ключ: AI Calculator → Home = PLUGIN_WEATHER_API_KEY в lara2.loc/.env', 'ai-calculator' );
			}
		}

		return implode( "\n", $lines );
	}

	/**
	 * Локальные домены (.loc, localhost) часто с самоподписанным HTTPS — cURL error 60.
	 *
	 * @param string $url
	 * @return bool
	 */
	private static function should_disable_ssl_verify( $url ) {
		$host = (string) wp_parse_url( $url, PHP_URL_HOST );
		if ( '' === $host ) {
			return false;
		}

		$host = strtolower( $host );
		if ( in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) ) {
			return true;
		}
		if ( str_ends_with( $host, '.loc' ) || str_ends_with( $host, '.local' ) || str_ends_with( $host, '.test' ) ) {
			return true;
		}
		if ( function_exists( 'wp_get_environment_type' ) && 'local' === wp_get_environment_type() ) {
			return true;
		}

		return (bool) apply_filters( 'ai_calculator_disable_ssl_verify', false, $url );
	}

	/**
	 * @param string               $remote_url
	 * @param array<string, string> $payload
	 * @return array{ok: bool, code?: int, error?: string, body?: string, message?: string}
	 */
	private static function forward_to_remote( array $payload ) {
		require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';

		$remote_url = AI_Calculator_Settings::get_remote_request_url( self::PLUGIN_SLUG );
		if ( '' === $remote_url ) {
			return array(
				'ok'    => false,
				'error' => __( 'URL not set.', 'ai-calculator' ),
			);
		}

		$headers = array(
			'Content-Type' => 'application/json; charset=utf-8',
			'Accept'       => 'application/json',
		);

		$api_key = AI_Calculator_Settings::get_api_key();
		if ( '' !== $api_key ) {
			$headers['X-Plugin-Api-Key'] = $api_key;
		}

		$args = array(
			'timeout'   => 300,
			'headers'   => $headers,
			'body'      => wp_json_encode( $payload ),
			'sslverify' => ! self::should_disable_ssl_verify( $remote_url ),
		);

		$response = wp_remote_post( $remote_url, $args );

		if ( is_wp_error( $response ) ) {
			return array(
				'ok'    => false,
				'error' => $response->get_error_message(),
			);
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$raw  = (string) wp_remote_retrieve_body( $response );
		$ok   = $code >= 200 && $code < 300;

		$result = array(
			'ok'   => $ok,
			'code' => $code,
		);

		if ( ! $ok ) {
			$result['error'] = 'HTTP ' . $code;
		}

		if ( '' !== $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$result['laravel_ok'] = ! empty( $decoded['ok'] );
				if ( ! empty( $decoded['message'] ) && is_string( $decoded['message'] ) ) {
					$result['message'] = $decoded['message'];
				}
				if ( ! empty( $decoded['weather'] ) && is_array( $decoded['weather'] ) ) {
					$result['weather'] = self::sanitize_weather_stats( $decoded['weather'] );
				}
			}
			$result['body'] = function_exists( 'mb_substr' ) ? mb_substr( $raw, 0, 200 ) : substr( $raw, 0, 200 );
		}

		return $result;
	}

	/**
	 * @param array<string, mixed> $weather
	 * @return array{temperature?: string, precipitation?: string, sunny_days?: string, season?: string, summary?: string}
	 */
	private static function sanitize_weather_stats( array $weather ) {
		$out = array();
		$keys = array( 'temperature', 'precipitation', 'sunny_days', 'season', 'summary' );
		foreach ( $keys as $key ) {
			if ( isset( $weather[ $key ] ) && is_scalar( $weather[ $key ] ) ) {
				$value = trim( (string) $weather[ $key ] );
				if ( '' !== $value ) {
					$out[ $key ] = $value;
				}
			}
		}

		return $out;
	}

	public static function handle() {
		check_ajax_referer( 'ai_calculator_weather', 'nonce' );

		$honeypot = isset( $_POST['ai_calculator_hp'] ) ? trim( (string) wp_unslash( $_POST['ai_calculator_hp'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' !== $honeypot ) {
			wp_send_json_error( array( 'message' => __( 'Spam detected.', 'ai-calculator' ) ), 400 );
		}

		$region_id = isset( $_POST['region'] ) ? absint( wp_unslash( $_POST['region'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$month_id  = isset( $_POST['month'] ) ? absint( wp_unslash( $_POST['month'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( $region_id <= 0 || $month_id <= 0 ) {
			wp_send_json_error(
				array( 'message' => __( 'Choose month and region.', 'ai-calculator' ) ),
				400
			);
		}

		$model = new AI_Calculator_Weather_Model();
		$sent  = $model->build_laravel_payload( $month_id, $region_id );

		if ( '' === $sent['month_name'] || '' === $sent['region_name'] ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid month or region in catalog.', 'ai-calculator' ),
					'sent'    => $sent,
				),
				400
			);
		}

		require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';
		$laravel_base = AI_Calculator_Settings::get_laravel_base_url();
		$laravel_path = AI_Calculator_Settings::get_laravel_plugin_path( self::PLUGIN_SLUG );

		$remote_result = array( 'ok' => false, 'error' => __( 'URL not set.', 'ai-calculator' ) );
		if ( '' !== $laravel_base ) {
			$remote_result = self::forward_to_remote( $sent );
		}

		if ( ! empty( $remote_result['weather'] ) ) {
			wp_send_json_success(
				array(
					'weather' => $remote_result['weather'],
					'message' => isset( $remote_result['message'] ) ? (string) $remote_result['message'] : '',
					'sent'    => $sent,
				)
			);
		}

		if ( '' !== $laravel_base && ! empty( $remote_result['ok'] ) && empty( $remote_result['laravel_ok'] ) ) {
			wp_send_json_error(
				array(
					'message' => isset( $remote_result['message'] ) && '' !== $remote_result['message']
						? (string) $remote_result['message']
						: __( 'AI could not return weather data.', 'ai-calculator' ),
					'sent'    => $sent,
				),
				502
			);
		}

		wp_send_json_success(
			array(
				'message' => self::build_debug_message( $sent, $laravel_base, $laravel_path, $remote_result ),
				'sent'    => $sent,
			)
		);
	}
}

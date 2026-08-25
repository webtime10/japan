<?php
/**
 * AJAX: опрос Ideal Region → Laravel API.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Ideal_Region_Ajax {

	const PLUGIN_SLUG = 'ideal_region';

	public static function register() {
		add_action( 'wp_ajax_ai_calculator_ideal_region_submit', array( __CLASS__, 'handle' ) );
		add_action( 'wp_ajax_nopriv_ai_calculator_ideal_region_submit', array( __CLASS__, 'handle' ) );
	}

	/**
	 * @param array<string, mixed> $payload
	 * @return array{ok: bool, code?: int, error?: string, message?: string, laravel_ok?: bool, status?: string, received?: array<string, mixed>}
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

		$response = wp_remote_post(
			$remote_url,
			array(
				'timeout'   => 60,
				'headers'   => $headers,
				'body'      => wp_json_encode( $payload ),
				'sslverify' => ! self::should_disable_ssl_verify( $remote_url ),
			)
		);

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
				if ( isset( $decoded['status'] ) ) {
					$result['status'] = (string) $decoded['status'];
				}
				if ( isset( $decoded['received'] ) && is_array( $decoded['received'] ) ) {
					$result['received'] = $decoded['received'];
				}
				if ( isset( $decoded['result'] ) && is_array( $decoded['result'] ) ) {
					$result['result'] = $decoded['result'];
				}
				if ( isset( $decoded['step_slots'] ) && is_array( $decoded['step_slots'] ) ) {
					$result['step_slots'] = $decoded['step_slots'];
				}
			}
		}

		return $result;
	}

	/**
	 * @param string $url
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

	public static function handle() {
		check_ajax_referer( 'ai_calculator_ideal_region', 'nonce' );

		$honeypot = isset( $_POST['ai_calculator_hp'] ) ? trim( (string) wp_unslash( $_POST['ai_calculator_hp'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' !== $honeypot ) {
			wp_send_json_error( array( 'message' => __( 'Spam detected.', 'ai-calculator' ) ), 400 );
		}

		$answers_raw = isset( $_POST['answers'] ) ? wp_unslash( $_POST['answers'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$answers     = json_decode( is_string( $answers_raw ) ? $answers_raw : '', true );

		if ( ! is_array( $answers ) || empty( $answers['catalog'] ) || ! is_array( $answers['catalog'] ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid survey answers.', 'ai-calculator' ) ),
				400
			);
		}

		$language = isset( $_POST['language'] ) ? sanitize_key( wp_unslash( $_POST['language'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' === $language && function_exists( 'ai_calculator_polylang_slug' ) ) {
			$language = (string) ai_calculator_polylang_slug();
		}
		if ( '' === $language && function_exists( 'pll_current_language' ) ) {
			$language = (string) pll_current_language( 'slug' );
		}
		if ( 'iw' === $language ) {
			$language = 'he';
		}
		if ( '' === $language ) {
			$language = 'he';
		}

		$session_token = isset( $_POST['session_token'] ) ? sanitize_text_field( wp_unslash( $_POST['session_token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		// japan.loc → всегда Япония (lara2 manufacturer_id=2).
		$manufacturer_id = 2;
		if ( function_exists( 'ai_calculator_get_ideal_region_laravel_manufacturer_id' ) ) {
			$resolved = (int) ai_calculator_get_ideal_region_laravel_manufacturer_id();
			if ( $resolved > 0 ) {
				$manufacturer_id = $resolved;
			}
		}
		if ( 1 === $manufacturer_id || 5 === $manufacturer_id ) {
			$manufacturer_id = 2;
		}

		$payload = array(
			'language'        => $language,
			'session_token'   => $session_token,
			'manufacturer_id' => $manufacturer_id,
			'answers'         => $answers,
		);

		$remote = self::forward_to_remote( $payload );

		if ( empty( $remote['ok'] ) || empty( $remote['laravel_ok'] ) ) {
			wp_send_json_error(
				array(
					'message' => isset( $remote['message'] ) && '' !== $remote['message']
						? (string) $remote['message']
						: ( isset( $remote['error'] ) ? (string) $remote['error'] : __( 'Laravel request failed.', 'ai-calculator' ) ),
				),
				502
			);
		}

		wp_send_json_success(
			array(
				'status'     => isset( $remote['status'] ) ? (string) $remote['status'] : 'received',
				'message'    => isset( $remote['message'] ) ? (string) $remote['message'] : '',
				'received'   => isset( $remote['received'] ) && is_array( $remote['received'] ) ? $remote['received'] : array(),
				'result'     => isset( $remote['result'] ) && is_array( $remote['result'] ) ? $remote['result'] : array(),
				'step_slots' => isset( $remote['step_slots'] ) && is_array( $remote['step_slots'] ) ? $remote['step_slots'] : array(),
			)
		);
	}
}

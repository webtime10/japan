<?php
/**
 * AJAX: финальный опрос budget → Laravel API.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Budget_Ajax {

	const PLUGIN_SLUG = 'budget';

	public static function register() {
		add_action( 'wp_ajax_ai_calculator_budget_submit', array( __CLASS__, 'handle' ) );
		add_action( 'wp_ajax_nopriv_ai_calculator_budget_submit', array( __CLASS__, 'handle' ) );
		add_action( 'wp_ajax_ai_calculator_budget_status', array( __CLASS__, 'handle_status' ) );
		add_action( 'wp_ajax_nopriv_ai_calculator_budget_status', array( __CLASS__, 'handle_status' ) );
	}

	/**
	 * @param string               $remote_url
	 * @param array<string, mixed> $payload
	 * @return array{ok: bool, code?: int, error?: string, message?: string, budget?: array<string, mixed>, quiz_answer_id?: int}
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
				if ( isset( $decoded['status'] ) ) {
					$result['status'] = (string) $decoded['status'];
				}
				if ( ! empty( $decoded['budget'] ) && is_array( $decoded['budget'] ) ) {
					$result['budget'] = $decoded['budget'];
				}
				if ( isset( $decoded['quiz_answer_id'] ) ) {
					$result['quiz_answer_id'] = (int) $decoded['quiz_answer_id'];
				}
				if ( isset( $decoded['budget_total'] ) ) {
					$result['budget_total'] = (string) $decoded['budget_total'];
				}
				if ( isset( $decoded['item_total'] ) ) {
					$result['item_total'] = (string) $decoded['item_total'];
				}
				if ( isset( $decoded['budget_base_total'] ) ) {
					$result['budget_base_total'] = (string) $decoded['budget_base_total'];
				}
				if ( isset( $decoded['base_total'] ) ) {
					$result['base_total'] = (string) $decoded['base_total'];
				}
				if ( isset( $decoded['priority_adjustment'] ) ) {
					$result['priority_adjustment'] = (string) $decoded['priority_adjustment'];
				}
				if ( isset( $decoded['budget_priority_adjustment_total'] ) ) {
					$result['budget_priority_adjustment_total'] = (string) $decoded['budget_priority_adjustment_total'];
				}
			}
		}

		return $result;
	}

	private static function forward_status_to_remote( $quiz_answer_id ) {
		require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';

		$base = AI_Calculator_Settings::get_laravel_base_url();
		if ( '' === $base ) {
			return array(
				'ok'    => false,
				'error' => __( 'URL not set.', 'ai-calculator' ),
			);
		}

		if ( ! preg_match( '#^https?://#i', $base ) ) {
			$base = 'http://' . ltrim( $base, '/' );
		}

		$remote_url = untrailingslashit( $base ) . '/api/plugins/budget/status/' . absint( $quiz_answer_id );
		$headers    = array( 'Accept' => 'application/json' );
		$api_key    = AI_Calculator_Settings::get_api_key();
		if ( '' !== $api_key ) {
			$headers['X-Plugin-Api-Key'] = $api_key;
		}

		$response = wp_remote_get(
			$remote_url,
			array(
				'timeout'   => 30,
				'headers'   => $headers,
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
		$out  = array(
			'ok'   => $code >= 200 && $code < 300,
			'code' => $code,
		);

		if ( '' !== $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				foreach ( array( 'status', 'message', 'quiz_answer_id', 'budget', 'budget_total', 'item_total', 'budget_base_total', 'base_total', 'priority_adjustment', 'budget_priority_adjustment_total', 'calculation_error' ) as $key ) {
					if ( isset( $decoded[ $key ] ) ) {
						$out[ $key ] = $decoded[ $key ];
					}
				}
			}
		}

		return $out;
	}

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
		check_ajax_referer( 'ai_calculator_budget', 'nonce' );

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
		if ( '' === $language && function_exists( 'pll_current_language' ) ) {
			$language = (string) pll_current_language( 'slug' );
		}
		if ( '' === $language ) {
			$language = 'ar';
		}

		$session_token = isset( $_POST['session_token'] ) ? sanitize_text_field( wp_unslash( $_POST['session_token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$payload = array(
			'language'      => $language,
			'session_token' => $session_token,
			'answers'       => $answers,
		);

		$remote = self::forward_to_remote( $payload );
		$has_saved_result = ! empty( $remote['quiz_answer_id'] )
			|| ! empty( $remote['item_total'] )
			|| ! empty( $remote['budget_total'] );

		if ( empty( $remote['ok'] ) || ( empty( $remote['laravel_ok'] ) && ! $has_saved_result ) ) {
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
				'budget'          => isset( $remote['budget'] ) && is_array( $remote['budget'] ) ? $remote['budget'] : array(),
				'status'          => isset( $remote['status'] ) ? (string) $remote['status'] : '',
				'message'         => isset( $remote['message'] ) ? (string) $remote['message'] : '',
				'quiz_answer_id'  => isset( $remote['quiz_answer_id'] ) ? (int) $remote['quiz_answer_id'] : 0,
				'budget_total'    => isset( $remote['budget_total'] ) ? (string) $remote['budget_total'] : '',
				'item_total'      => isset( $remote['item_total'] ) ? (string) $remote['item_total'] : '',
				'budget_base_total' => isset( $remote['budget_base_total'] ) ? (string) $remote['budget_base_total'] : '',
				'base_total'      => isset( $remote['base_total'] ) ? (string) $remote['base_total'] : '',
				'priority_adjustment' => isset( $remote['priority_adjustment'] ) ? (string) $remote['priority_adjustment'] : '',
				'budget_priority_adjustment_total' => isset( $remote['budget_priority_adjustment_total'] ) ? (string) $remote['budget_priority_adjustment_total'] : '',
			)
		);
	}

	public static function handle_status() {
		check_ajax_referer( 'ai_calculator_budget', 'nonce' );

		$quiz_answer_id = isset( $_POST['quiz_answer_id'] ) ? absint( wp_unslash( $_POST['quiz_answer_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $quiz_answer_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid calculation id.', 'ai-calculator' ) ), 400 );
		}

		$remote = self::forward_status_to_remote( $quiz_answer_id );
		if ( empty( $remote['ok'] ) ) {
			wp_send_json_error(
				array(
					'message' => isset( $remote['message'] ) ? (string) $remote['message'] : ( isset( $remote['error'] ) ? (string) $remote['error'] : __( 'Laravel request failed.', 'ai-calculator' ) ),
				),
				502
			);
		}

		wp_send_json_success(
			array(
				'status' => isset( $remote['status'] ) ? (string) $remote['status'] : '',
				'message' => isset( $remote['message'] ) ? (string) $remote['message'] : '',
				'quiz_answer_id' => isset( $remote['quiz_answer_id'] ) ? (int) $remote['quiz_answer_id'] : $quiz_answer_id,
				'budget' => isset( $remote['budget'] ) && is_array( $remote['budget'] ) ? $remote['budget'] : array(),
				'budget_total' => isset( $remote['budget_total'] ) ? (string) $remote['budget_total'] : '',
				'item_total' => isset( $remote['item_total'] ) ? (string) $remote['item_total'] : '',
				'budget_base_total' => isset( $remote['budget_base_total'] ) ? (string) $remote['budget_base_total'] : '',
				'base_total' => isset( $remote['base_total'] ) ? (string) $remote['base_total'] : '',
				'priority_adjustment' => isset( $remote['priority_adjustment'] ) ? (string) $remote['priority_adjustment'] : '',
				'budget_priority_adjustment_total' => isset( $remote['budget_priority_adjustment_total'] ) ? (string) $remote['budget_priority_adjustment_total'] : '',
				'calculation_error' => isset( $remote['calculation_error'] ) ? (string) $remote['calculation_error'] : '',
			)
		);
	}
}

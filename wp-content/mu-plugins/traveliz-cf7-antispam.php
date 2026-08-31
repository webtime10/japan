<?php
/**
 * Plugin Name: Traveliz CF7 Antispam
 * Description: Защита форм Contact Form 7: honeypot, проверка времени и HMAC-токен (без внешних капч).
 * Version: 1.0.0
 *
 * @package Traveliz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Антиспам для всех форм Contact Form 7.
 */
final class Traveliz_CF7_Antispam {

	/** Honeypot-поле (боты часто заполняют «website»). */
	const HONEYPOT_FIELD = 'traveliz_cf7_hp';

	/** Unix-timestamp генерации формы. */
	const TIMESTAMP_FIELD = 'traveliz_cf7_ts';

	/** HMAC-токен (timestamp + ID формы CF7). */
	const TOKEN_FIELD = 'traveliz_cf7_token';

	/** Минимальная задержка между открытием формы и отправкой (секунды). */
	const MIN_SUBMIT_SECONDS = 3;

	/** Максимальный «возраст» формы (секунды). */
	const MAX_FORM_AGE_SECONDS = 86400;

	/** Ключ соли для HMAC. */
	const SALT_CONTEXT = 'traveliz_cf7_antispam';

	/**
	 * Подключение хуков после загрузки CF7.
	 */
	public static function bootstrap() {
		if ( ! class_exists( 'WPCF7' ) ) {
			return;
		}

		add_filter( 'wpcf7_form_elements', array( __CLASS__, 'inject_fields' ), 20, 1 );
		add_filter( 'wpcf7_validate', array( __CLASS__, 'validate_submission' ), 1, 2 );
	}

	/**
	 * Добавляет скрытые поля защиты в разметку формы CF7.
	 *
	 * @param string $elements HTML формы.
	 * @return string
	 */
	public static function inject_fields( $elements ) {
		$form_id = self::get_current_form_id();

		return $elements . self::get_hidden_fields_html( $form_id );
	}

	/**
	 * Валидация отправки CF7.
	 *
	 * @param WPCF7_Validation $result Объект результата валидации.
	 * @param array            $tags   Теги полей формы.
	 * @return WPCF7_Validation
	 */
	public static function validate_submission( $result, $tags ) {
		unset( $tags );

		if ( self::is_exempt_user() ) {
			return $result;
		}

		$form_id = self::get_submission_form_id();

		if ( self::is_honeypot_filled() ) {
			self::log_block( 'honeypot', $form_id );
			$result->invalidate( self::TIMESTAMP_FIELD, self::get_error_message() );

			return $result;
		}

		$timestamp_error = self::get_timestamp_validation_error( $form_id );

		if ( null !== $timestamp_error ) {
			self::log_block( $timestamp_error, $form_id );
			$result->invalidate( self::TIMESTAMP_FIELD, self::get_error_message() );
		}

		return $result;
	}

	/**
	 * HTML скрытых полей для конкретной формы.
	 *
	 * @param int $form_id ID формы CF7.
	 * @return string
	 */
	private static function get_hidden_fields_html( $form_id ) {
		$issued_at = time();
		$token     = self::build_token( $issued_at, $form_id );

		$html  = '<span class="traveliz-cf7-antispam" style="display:none !important;" aria-hidden="true">';
		$html .= '<label for="' . esc_attr( self::HONEYPOT_FIELD ) . '">' . esc_html__( 'Website', 'traveliz' ) . '</label>';
		$html .= '<input type="text" name="' . esc_attr( self::HONEYPOT_FIELD ) . '" id="' . esc_attr( self::HONEYPOT_FIELD ) . '" value="" tabindex="-1" autocomplete="off" />';
		$html .= '<input type="hidden" name="' . esc_attr( self::TIMESTAMP_FIELD ) . '" value="' . esc_attr( (string) $issued_at ) . '" />';
		$html .= '<input type="hidden" name="' . esc_attr( self::TOKEN_FIELD ) . '" value="' . esc_attr( $token ) . '" />';
		$html .= '</span>';

		return $html;
	}

	/**
	 * Администраторы и модераторы комментариев — без ограничений.
	 */
	private static function is_exempt_user() {
		return is_user_logged_in() && current_user_can( 'moderate_comments' );
	}

	/**
	 * Honeypot не должен быть заполнен.
	 */
	private static function is_honeypot_filled() {
		$honeypot = isset( $_POST[ self::HONEYPOT_FIELD ] )
			? trim( (string) wp_unslash( $_POST[ self::HONEYPOT_FIELD ] ) )
			: '';

		return '' !== $honeypot;
	}

	/**
	 * Проверка timestamp и HMAC-токена.
	 *
	 * @param int $form_id ID формы CF7.
	 * @return string|null Код ошибки или null, если всё в порядке.
	 */
	private static function get_timestamp_validation_error( $form_id ) {
		$issued_at = isset( $_POST[ self::TIMESTAMP_FIELD ] )
			? absint( wp_unslash( $_POST[ self::TIMESTAMP_FIELD ] ) )
			: 0;

		$token = isset( $_POST[ self::TOKEN_FIELD ] )
			? sanitize_text_field( wp_unslash( $_POST[ self::TOKEN_FIELD ] ) )
			: '';

		if ( $issued_at < 1 || '' === $token || $form_id < 1 ) {
			return 'timestamp_missing';
		}

		$expected = self::build_token( $issued_at, $form_id );

		if ( ! hash_equals( $expected, $token ) ) {
			return 'timestamp_invalid';
		}

		$elapsed = time() - $issued_at;

		if ( $elapsed < self::MIN_SUBMIT_SECONDS ) {
			return 'too_fast';
		}

		if ( $elapsed > self::MAX_FORM_AGE_SECONDS ) {
			return 'too_old';
		}

		return null;
	}

	/**
	 * HMAC-токен: timestamp + ID формы + соль WordPress.
	 *
	 * @param int $issued_at Unix time.
	 * @param int $form_id   ID формы CF7.
	 * @return string
	 */
	private static function build_token( $issued_at, $form_id ) {
		$payload = (string) absint( $issued_at ) . '|' . (string) absint( $form_id );

		return hash_hmac( 'sha256', $payload, wp_salt( self::SALT_CONTEXT ) );
	}

	/**
	 * ID формы на этапе рендера.
	 *
	 * @return int
	 */
	private static function get_current_form_id() {
		if ( function_exists( 'wpcf7_get_current_contact_form' ) ) {
			$form = wpcf7_get_current_contact_form();

			if ( $form && method_exists( $form, 'id' ) ) {
				return (int) $form->id();
			}
		}

		return 0;
	}

	/**
	 * ID формы на этапе валидации отправки.
	 *
	 * @return int
	 */
	private static function get_submission_form_id() {
		if ( class_exists( 'WPCF7_Submission' ) ) {
			$submission = WPCF7_Submission::get_instance();

			if ( $submission && method_exists( $submission, 'get_contact_form' ) ) {
				$form = $submission->get_contact_form();

				if ( $form && method_exists( $form, 'id' ) ) {
					return (int) $form->id();
				}
			}
		}

		return self::get_current_form_id();
	}

	/**
	 * Сообщение об ошибке для пользователя.
	 *
	 * @return string
	 */
	private static function get_error_message() {
		return __( 'Отправка формы отклонена. Обновите страницу и попробуйте снова.', 'traveliz' );
	}

	/**
	 * Логирование блокировки (только при WP_DEBUG).
	 *
	 * @param string $reason  Код причины.
	 * @param int    $form_id ID формы CF7.
	 */
	private static function log_block( $reason, $form_id ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log(
			sprintf(
				'[Traveliz CF7 Antispam] Blocked form #%d: %s',
				(int) $form_id,
				$reason
			)
		);
	}
}

add_action( 'plugins_loaded', array( 'Traveliz_CF7_Antispam', 'bootstrap' ), 20 );

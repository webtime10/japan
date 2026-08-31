<?php
/**
 * Plugin Name: Traveliz Comment Antispam
 * Description: Защита формы комментариев: honeypot, проверка времени и nonce (без внешних капч).
 * Version: 1.0.0
 *
 * @package Traveliz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Антиспам для стандартной формы комментариев WordPress.
 */
final class Traveliz_Comment_Antispam {

	/** Имя honeypot-поля (должно выглядеть правдоподобно для ботов). */
	const HONEYPOT_FIELD = 'traveliz_hp_website';

	/** Скрытое поле: Unix-timestamp генерации формы. */
	const TIMESTAMP_FIELD = 'traveliz_comment_ts';

	/** Скрытое поле: HMAC-токен (timestamp + post_id). */
	const TOKEN_FIELD = 'traveliz_comment_token';

	/** Имя поля WordPress nonce. */
	const NONCE_FIELD = 'traveliz_comment_nonce';

	/** Действие для wp_nonce_field / wp_verify_nonce. */
	const NONCE_ACTION = 'traveliz_comment_antispam_submit';

	/** Минимальная задержка между открытием формы и отправкой (секунды). */
	const MIN_SUBMIT_SECONDS = 3;

	/** Максимальный «возраст» формы (секунды); защита от переиспользования старых токенов. */
	const MAX_FORM_AGE_SECONDS = 86400;

	/**
	 * Регистрация хуков.
	 */
	public static function init() {
		add_action( 'comment_form_logged_in_after', array( __CLASS__, 'render_fields' ) );
		add_action( 'comment_form_after_fields', array( __CLASS__, 'render_fields' ) );
		add_filter( 'preprocess_comment', array( __CLASS__, 'preprocess_comment' ), 1 );
	}

	/**
	 * Вывод скрытых полей защиты в форме комментария.
	 */
	public static function render_fields() {
		static $rendered = false;

		if ( $rendered ) {
			return;
		}

		$rendered = true;

		$post_id   = self::get_form_post_id();
		$issued_at = time();
		$token     = self::build_token( $issued_at, $post_id );

		// Honeypot: боты часто заполняют «website», люди поле не видят.
		echo '<p class="traveliz-comment-hp" style="display:none !important;" aria-hidden="true">';
		echo '<label for="' . esc_attr( self::HONEYPOT_FIELD ) . '">' . esc_html__( 'Website', 'traveliz' ) . '</label>';
		echo '<input type="text" name="' . esc_attr( self::HONEYPOT_FIELD ) . '" id="' . esc_attr( self::HONEYPOT_FIELD ) . '" value="" tabindex="-1" autocomplete="off" />';
		echo '</p>';

		// Время генерации формы + подпись (защита от подделки timestamp).
		echo '<input type="hidden" name="' . esc_attr( self::TIMESTAMP_FIELD ) . '" value="' . esc_attr( (string) $issued_at ) . '" />';
		echo '<input type="hidden" name="' . esc_attr( self::TOKEN_FIELD ) . '" value="' . esc_attr( $token ) . '" />';

		// Nonce: отсекает прямые POST в wp-comments-post.php без загрузки страницы.
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
	}

	/**
	 * Проверка комментария перед сохранением.
	 *
	 * @param array<string, mixed> $commentdata Данные комментария.
	 * @return array<string, mixed>
	 */
	public static function preprocess_comment( $commentdata ) {
		// Модераторы и администраторы — без ограничений.
		if ( is_user_logged_in() && current_user_can( 'moderate_comments' ) ) {
			return $commentdata;
		}

		// Только POST-запросы с формы.
		if ( ! self::is_comment_post_request() ) {
			return $commentdata;
		}

		self::validate_request();

		return $commentdata;
	}

	/**
	 * Проверяет, что это отправка формы комментария.
	 */
	private static function is_comment_post_request() {
		if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '' ) ) {
			return false;
		}

		return isset( $_POST['comment'] ) || isset( $_POST[ self::HONEYPOT_FIELD ] ) || isset( $_POST[ self::NONCE_FIELD ] );
	}

	/**
	 * Полный набор проверок; при ошибке — wp_die( 403 ).
	 */
	private static function validate_request() {
		self::validate_honeypot();
		self::validate_nonce();
		self::validate_timestamp();
	}

	/**
	 * Honeypot: поле должно оставаться пустым.
	 */
	private static function validate_honeypot() {
		$honeypot = isset( $_POST[ self::HONEYPOT_FIELD ] )
			? trim( (string) wp_unslash( $_POST[ self::HONEYPOT_FIELD ] ) )
			: '';

		if ( '' !== $honeypot ) {
			self::block_request( 'honeypot' );
		}
	}

	/**
	 * Nonce: запрос должен идти со страницы, где была отрисована форма.
	 */
	private static function validate_nonce() {
		$nonce = isset( $_POST[ self::NONCE_FIELD ] )
			? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) )
			: '';

		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			self::block_request( 'nonce' );
		}
	}

	/**
	 * Timestamp: форма не старше MAX_FORM_AGE и отправлена не раньше MIN_SUBMIT_SECONDS.
	 */
	private static function validate_timestamp() {
		$issued_at = isset( $_POST[ self::TIMESTAMP_FIELD ] )
			? absint( wp_unslash( $_POST[ self::TIMESTAMP_FIELD ] ) )
			: 0;

		$token = isset( $_POST[ self::TOKEN_FIELD ] )
			? sanitize_text_field( wp_unslash( $_POST[ self::TOKEN_FIELD ] ) )
			: '';

		$post_id = isset( $_POST['comment_post_ID'] )
			? absint( wp_unslash( $_POST['comment_post_ID'] ) )
			: 0;

		if ( $issued_at < 1 || '' === $token || $post_id < 1 ) {
			self::block_request( 'timestamp_missing' );
		}

		$expected = self::build_token( $issued_at, $post_id );

		if ( ! hash_equals( $expected, $token ) ) {
			self::block_request( 'timestamp_invalid' );
		}

		$now     = time();
		$elapsed = $now - $issued_at;

		if ( $elapsed < self::MIN_SUBMIT_SECONDS ) {
			self::block_request( 'too_fast' );
		}

		if ( $elapsed > self::MAX_FORM_AGE_SECONDS ) {
			self::block_request( 'too_old' );
		}
	}

	/**
	 * HMAC-токен: timestamp + ID записи + соль WordPress.
	 *
	 * @param int $issued_at Unix time.
	 * @param int $post_id   ID записи.
	 */
	private static function build_token( $issued_at, $post_id ) {
		$payload = (string) absint( $issued_at ) . '|' . (string) absint( $post_id );

		return hash_hmac( 'sha256', $payload, wp_salt( 'traveliz_comment_antispam' ) );
	}

	/**
	 * ID записи на этапе рендера формы.
	 */
	private static function get_form_post_id() {
		$post_id = get_the_ID();

		if ( $post_id ) {
			return (int) $post_id;
		}

		return 0;
	}

	/**
	 * Блокировка спам-запроса.
	 *
	 * @param string $reason Внутренний код причины (для логов).
	 */
	private static function block_request( $reason ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( '[Traveliz Comment Antispam] Blocked comment: ' . $reason );
		}

		wp_die(
			esc_html__( 'Отправка комментария отклонена. Обновите страницу и попробуйте снова.', 'traveliz' ),
			esc_html__( 'Ошибка', 'traveliz' ),
			array(
				'response'  => 403,
				'back_link' => true,
			)
		);
	}
}

Traveliz_Comment_Antispam::init();

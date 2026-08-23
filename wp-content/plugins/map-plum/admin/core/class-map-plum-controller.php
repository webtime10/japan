<?php
/**
 * Base controller.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Map_Plum_Controller {

	/** @var string */
	protected $route = '';

	public function __construct( $route = '' ) {
		$this->route = $route;
	}

	/**
	 * @param string $view Path under admin/views/ without .php.
	 * @param array  $data Variables for view.
	 */
	protected function render( $view, $data = array() ) {
		$data = array_merge(
			array(
				'route'          => $this->route,
				'page_slug'      => Map_Plum_Router::page_slug_for_route( $this->route ),
				'action'         => isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'index', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'flash'          => $this->get_flash(),
				'header_buttons' => '',
			),
			$data
		);

		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		include MAP_PLUM_PATH . 'admin/views/layout/header.php';
		include MAP_PLUM_PATH . 'admin/views/' . $view . '.php';
		include MAP_PLUM_PATH . 'admin/views/layout/footer.php';
	}

	protected function redirect( $action = 'index', $id = 0, $message = '' ) {
		$url = Map_Plum_Router::url( $this->route, $action, $id );
		if ( $message ) {
			$url = add_query_arg( 'message', $message, $url );
		}

		if ( ! headers_sent() ) {
			wp_safe_redirect( $url );
			exit;
		}

		$escaped_url = esc_url( $url );
		echo '<!DOCTYPE html><html><head><meta charset="utf-8">';
		echo '<meta http-equiv="refresh" content="0;url=' . esc_attr( $escaped_url ) . '">';
		echo '<script>window.location.replace(' . wp_json_encode( $url ) . ');</script>';
		echo '</head><body><p><a href="' . esc_attr( $escaped_url ) . '">Продолжить</a></p></body></html>';
		exit;
	}

	protected function verify_nonce( $action ) {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $action ) ) {
			wp_die( 'Ошибка проверки безопасности.' );
		}
	}

	/**
	 * Необязательная ссылка: пустое значение допустимо.
	 *
	 * @param mixed $value
	 * @return string
	 */
	protected function sanitize_optional_link( $value ) {
		$url = trim( (string) $value );
		if ( '' === $url ) {
			return '';
		}

		$sanitized = esc_url_raw( $url );
		if ( '' === $sanitized && ! preg_match( '#^https?://#i', $url ) ) {
			$sanitized = esc_url_raw( 'https://' . ltrim( $url, '/' ) );
		}

		return is_string( $sanitized ) ? $sanitized : '';
	}

	protected function can_manage() {
		return current_user_can( 'manage_options' );
	}

	protected function set_flash( $type, $text ) {
		set_transient( 'map_plum_flash_' . get_current_user_id(), array( 'type' => $type, 'text' => $text ), 30 );
	}

	/**
	 * Сохраняет введённые данные формы при ошибке валидации (до редиректа).
	 *
	 * @param int                  $entity_id     0 при добавлении.
	 * @param array<string, mixed> $main          Основные поля формы.
	 * @param array<int, array>    $descriptions  Описания по language_id.
	 * @param array<string, mixed> $extra         Доп. поля (категории, маркеры и т.д.).
	 */
	protected function stash_form_input( $entity_id, array $main, array $descriptions, array $extra = array() ) {
		set_transient(
			$this->form_input_transient_key(),
			array(
				'entity_id'    => (int) $entity_id,
				'main'         => $main,
				'descriptions' => $descriptions,
				'extra'        => $extra,
			),
			300
		);
	}

	protected function clear_stashed_form_input() {
		delete_transient( $this->form_input_transient_key() );
	}

	protected function form_input_transient_key() {
		return 'map_plum_form_input_' . get_current_user_id() . '_' . $this->route;
	}

	/**
	 * Подставляет сохранённые после ошибки валидации данные в форму.
	 *
	 * @param int                $entity_id
	 * @param object|null        $item
	 * @param array<int, object> $descriptions
	 * @return array{0: object|null, 1: array<int, object>, 2: array<string, mixed>}
	 */
	protected function merge_stashed_form_input( $entity_id, $item, $descriptions ) {
		$extra   = array();
		$stashed = get_transient( $this->form_input_transient_key() );
		delete_transient( $this->form_input_transient_key() );

		if ( ! is_array( $stashed ) || (int) $stashed['entity_id'] !== (int) $entity_id ) {
			return array( $item, $descriptions, $extra );
		}

		if ( ! empty( $stashed['main'] ) && is_array( $stashed['main'] ) ) {
			$item = (object) array_merge( (array) ( $item ? $item : new stdClass() ), $stashed['main'] );
		}

		if ( ! empty( $stashed['descriptions'] ) && is_array( $stashed['descriptions'] ) ) {
			$descriptions = $this->stashed_descriptions_to_view( $stashed['descriptions'] );
		}

		if ( ! empty( $stashed['extra'] ) && is_array( $stashed['extra'] ) ) {
			$extra = $stashed['extra'];
		}

		return array( $item, $descriptions, $extra );
	}

	/**
	 * @param array<int, array<string, string>> $descriptions
	 * @return array<int, object>
	 */
	protected function stashed_descriptions_to_view( array $descriptions ) {
		$out = array();
		foreach ( $descriptions as $language_id => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$out[ (int) $language_id ] = (object) $row;
		}
		return $out;
	}

	/**
	 * @return array{type: string, text: string}|null
	 */
	protected function get_flash() {
		$key  = 'map_plum_flash_' . get_current_user_id();
		$data = get_transient( $key );
		if ( $data ) {
			delete_transient( $key );
		}
		if ( isset( $_GET['message'] ) && sanitize_key( wp_unslash( $_GET['message'] ) ) === 'saved' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return array(
				'type' => 'success',
				'text' => 'Изменения успешно сохранены.',
			);
		}
		return is_array( $data ) ? $data : null;
	}

	/**
	 * @param array<int, object> $languages
	 * @param array              $post_description $_POST['description'].
	 * @return array<int, array<string, string>>
	 */
	protected function parse_descriptions( $languages, $post_description ) {
		$out = array();
		if ( ! is_array( $post_description ) ) {
			return $out;
		}
		foreach ( $languages as $lang ) {
			$lid = (int) $lang->language_id;
			if ( ! isset( $post_description[ $lid ] ) || ! is_array( $post_description[ $lid ] ) ) {
				continue;
			}
			$row = $post_description[ $lid ];
			$out[ $lid ] = array(
				'name'             => isset( $row['name'] ) ? sanitize_text_field( wp_unslash( $row['name'] ) ) : '',
				'description'      => isset( $row['description'] ) ? wp_kses_post( wp_unslash( $row['description'] ) ) : '',
				'meta_title'       => isset( $row['meta_title'] ) ? sanitize_text_field( wp_unslash( $row['meta_title'] ) ) : '',
				'meta_description' => isset( $row['meta_description'] ) ? sanitize_text_field( wp_unslash( $row['meta_description'] ) ) : '',
				'meta_keyword'     => isset( $row['meta_keyword'] ) ? sanitize_text_field( wp_unslash( $row['meta_keyword'] ) ) : '',
			);
		}
		return $out;
	}
}

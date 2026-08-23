<?php
/**
 * Base controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class AI_Calculator_Controller {

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
				'page_slug'      => AI_Calculator_Router::page_slug_for_route( $this->route ),
				'action'         => isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'index', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'flash'          => $this->get_flash(),
				'header_buttons' => '',
				'heading_title'  => '',
			),
			$data
		);

		if ( empty( $data['heading_title'] ) && ! empty( $data['title'] ) ) {
			$data['heading_title'] = $data['title'];
		}

		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		include AI_CALCULATOR_PATH . 'admin/views/layout/header.php';
		include AI_CALCULATOR_PATH . 'admin/views/' . $view . '.php';
		include AI_CALCULATOR_PATH . 'admin/views/layout/footer.php';
	}

	protected function redirect( $action = 'index', $id = 0, $message = '' ) {
		$url = AI_Calculator_Router::url( $this->route, $action, $id );
		if ( $message ) {
			$url = add_query_arg( 'message', $message, $url );
		}

		if ( ! headers_sent() ) {
			wp_safe_redirect( $url );
			exit;
		}

		wp_die(
			'<p><a href="' . esc_url( $url ) . '">' . esc_html__( 'Continue', 'ai-calculator' ) . '</a></p>',
			esc_html__( 'Redirect', 'ai-calculator' ),
			array( 'response' => 302 )
		);
	}

	protected function verify_nonce( $action ) {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $action ) ) {
			wp_die( esc_html__( 'Security check failed.', 'ai-calculator' ) );
		}
	}

	/**
	 * @return bool
	 */
	protected function db_failed() {
		global $wpdb;
		return ! empty( $wpdb->last_error );
	}

	protected function flash_db_error() {
		global $wpdb;
		$this->set_flash(
			'error',
			sprintf(
				/* translators: %s: database error message */
				__( 'Database error: %s', 'ai-calculator' ),
				$wpdb->last_error
			)
		);
	}

	protected function set_flash( $type, $text ) {
		set_transient( 'ai_calculator_flash_' . get_current_user_id(), array( 'type' => $type, 'text' => $text ), 30 );
	}

	/**
	 * @param string $form_id HTML form id for header submit button.
	 */
	protected function header_btn_save( $form_id ) {
		return '<button type="submit" form="' . esc_attr( $form_id ) . '" class="btn btn-primary"><i class="fa fa-save"></i> ' . esc_html__( 'Save', 'ai-calculator' ) . '</button>';
	}

	/**
	 * @param string $route
	 * @param string $text
	 */
	protected function header_btn_add( $route, $text ) {
		return '<a href="' . esc_url( AI_Calculator_Router::url( $route, 'form' ) ) . '" class="btn btn-primary"><i class="fa fa-plus"></i> ' . esc_html( $text ) . '</a>';
	}

	protected function get_flash() {
		$key  = 'ai_calculator_flash_' . get_current_user_id();
		$data = get_transient( $key );
		if ( $data ) {
			delete_transient( $key );
		}
		if ( isset( $_GET['message'] ) && sanitize_key( wp_unslash( $_GET['message'] ) ) === 'saved' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return array(
				'type' => 'success',
				'text' => __( 'Changes saved.', 'ai-calculator' ),
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
			);
		}
		return $out;
	}
}

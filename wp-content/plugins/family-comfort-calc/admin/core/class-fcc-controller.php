<?php
/**
 * Base controller.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class FCC_Controller {

	/** @var string */
	protected $route = '';

	/** @var string */
	protected $group_type = '';

	public function __construct( $route = '', $group_type = '' ) {
		$this->route      = $route;
		$this->group_type = $group_type;
	}

	/**
	 * @param string $view
	 * @param array  $data
	 */
	protected function render( $view, $data = array() ) {
		$data = array_merge(
			array(
				'route'          => $this->route,
				'group_type'     => $this->group_type,
				'page_slug'      => FCC_Router::page_slug_for_route( $this->route ),
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

		include FCC_PATH . 'admin/views/layout/header.php';
		include FCC_PATH . 'admin/views/' . $view . '.php';
		include FCC_PATH . 'admin/views/layout/footer.php';
	}

	protected function redirect( $action = 'index', $id = 0, $message = '' ) {
		$url = FCC_Router::url( $this->route, $action, $id );
		if ( $message ) {
			$url = add_query_arg( 'message', $message, $url );
		}

		if ( ! headers_sent() ) {
			wp_safe_redirect( $url );
			exit;
		}

		wp_die(
			'<p><a href="' . esc_url( $url ) . '">' . esc_html__( 'Continue', 'family-comfort-calc' ) . '</a></p>',
			esc_html__( 'Redirect', 'family-comfort-calc' ),
			array( 'response' => 302 )
		);
	}

	protected function verify_nonce( $action ) {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $action ) ) {
			wp_die( esc_html__( 'Security check failed.', 'family-comfort-calc' ) );
		}
	}

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
				__( 'Database error: %s', 'family-comfort-calc' ),
				$wpdb->last_error
			)
		);
	}

	protected function set_flash( $type, $text ) {
		set_transient( 'fcc_flash_' . get_current_user_id(), array( 'type' => $type, 'text' => $text ), 30 );
	}

	protected function header_btn_save( $form_id ) {
		return '<button type="submit" form="' . esc_attr( $form_id ) . '" class="btn btn-primary"><i class="fa fa-save"></i> ' . esc_html__( 'Сохранить', 'family-comfort-calc' ) . '</button>';
	}

	protected function header_btn_add( $route, $text ) {
		return '<a href="' . esc_url( FCC_Router::url( $route, 'form' ) ) . '" class="btn btn-primary"><i class="fa fa-plus"></i> ' . esc_html( $text ) . '</a>';
	}

	protected function get_flash() {
		$key  = 'fcc_flash_' . get_current_user_id();
		$data = get_transient( $key );
		if ( $data ) {
			delete_transient( $key );
		}
		if ( isset( $_GET['message'] ) && sanitize_key( wp_unslash( $_GET['message'] ) ) === 'saved' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return array(
				'type' => 'success',
				'text' => __( 'Изменения сохранены.', 'family-comfort-calc' ),
			);
		}
		return is_array( $data ) ? $data : null;
	}

	/**
	 * @param array<int, object> $languages
	 * @param array              $post_description
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
				'name'        => isset( $row['name'] ) ? sanitize_text_field( wp_unslash( $row['name'] ) ) : '',
				'description' => isset( $row['description'] ) ? wp_kses_post( wp_unslash( $row['description'] ) ) : '',
			);
		}
		return $out;
	}
}

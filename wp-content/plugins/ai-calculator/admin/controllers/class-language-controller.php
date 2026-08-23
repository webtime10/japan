<?php
/**
 * Language admin controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Language_Controller extends AI_Calculator_Controller {

	/** @var AI_Calculator_Language_Model */
	private $model;

	public function __construct( $route = 'language' ) {
		parent::__construct( $route );
		$this->model = new AI_Calculator_Language_Model();
	}

	public function index() {
		$this->render(
			'language/list',
			array(
				'title'          => __( 'Languages', 'ai-calculator' ),
				'languages'      => $this->model->get_list(),
				'heading_title'  => __( 'Language List', 'ai-calculator' ),
				'header_buttons' => $this->header_btn_add( 'language', __( 'Add Language', 'ai-calculator' ) ),
			)
		);
	}

	public function form() {
		$id       = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$language = $id ? $this->model->get( $id ) : null;

		$this->render(
			'language/form',
			array(
				'title'          => $id ? __( 'Edit Language', 'ai-calculator' ) : __( 'Add Language', 'ai-calculator' ),
				'language'       => $language,
				'header_buttons' => $this->header_btn_save( 'ai-calculator-form-language' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_language_save' );

		$id = isset( $_POST['language_id'] ) ? (int) $_POST['language_id'] : 0;

		$data = array(
			'language_id' => $id,
			'name'        => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'code'        => isset( $_POST['code'] ) ? sanitize_key( wp_unslash( $_POST['code'] ) ) : '',
			'locale'      => isset( $_POST['locale'] ) ? sanitize_text_field( wp_unslash( $_POST['locale'] ) ) : '',
			'sort_order'  => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'      => isset( $_POST['status'] ),
		);

		if ( '' === $data['name'] || '' === $data['code'] ) {
			$this->set_flash( 'error', __( 'Name and code are required.', 'ai-calculator' ) );
			$this->redirect( 'form', $id );
		}

		$new_id = $this->model->save( $data );
		if ( $this->db_failed() ) {
			$this->flash_db_error();
			$this->redirect( 'form', $id );
		}
		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'ai_calculator_language_delete_' . $id );

		if ( ! $this->model->delete( $id ) ) {
			$this->set_flash( 'error', __( 'Cannot delete the last language.', 'ai-calculator' ) );
		} else {
			$this->set_flash( 'success', __( 'Language deleted.', 'ai-calculator' ) );
		}

		$this->redirect( 'index' );
	}

	public function sync_polylang() {
		check_admin_referer( 'ai_calculator_sync_polylang' );
		$added = $this->model->sync_from_polylang();
		if ( $added > 0 ) {
			$this->set_flash( 'success', sprintf(
				/* translators: %d: number of languages */
				__( '%d language(s) imported from Polylang.', 'ai-calculator' ),
				$added
			) );
		} else {
			$this->set_flash( 'error', __( 'Polylang not found or no new languages.', 'ai-calculator' ) );
		}
		$this->redirect( 'index' );
	}
}

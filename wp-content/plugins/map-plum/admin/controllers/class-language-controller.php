<?php
/**
 * Language admin controller.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Language_Controller extends Map_Plum_Controller {

	/** @var Map_Plum_Language_Model */
	private $model;

	public function __construct( $route = 'language' ) {
		parent::__construct( $route );
		$this->model = new Map_Plum_Language_Model();
	}

	public function index() {
		$this->render(
			'language/list',
			array(
				'title'          => __( 'Languages', 'map-plum' ),
				'heading_title'  => __( 'Language List', 'map-plum' ),
				'languages'      => $this->model->get_all(),
				'header_buttons' => '<a href="' . esc_url( Map_Plum_Router::url( 'language', 'form' ) ) . '" class="btn btn-primary"><i class="fa fa-plus"></i> ' . esc_html__( 'Add Language', 'map-plum' ) . '</a>',
			)
		);
	}

	public function form() {
		$id       = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$language = $id ? $this->model->get_by_id( $id ) : null;

		$this->render(
			'language/form',
			array(
				'title'          => $id ? __( 'Edit Language', 'map-plum' ) : __( 'Add Language', 'map-plum' ),
				'heading_title'  => $id ? __( 'Edit Language', 'map-plum' ) : __( 'Add Language', 'map-plum' ),
				'language'       => $language,
				'header_buttons' => '<button type="submit" form="map-plum-form-language" class="btn btn-primary"><i class="fa fa-save"></i> ' . esc_html__( 'Save', 'map-plum' ) . '</button>',
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'map_plum_language_save' );

		$id = isset( $_POST['language_id'] ) ? (int) $_POST['language_id'] : 0;
		$data = array(
			'language_id' => $id,
			'name'        => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'code'        => isset( $_POST['code'] ) ? sanitize_key( wp_unslash( $_POST['code'] ) ) : '',
			'locale'      => isset( $_POST['locale'] ) ? sanitize_text_field( wp_unslash( $_POST['locale'] ) ) : '',
			'sort_order'  => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'      => isset( $_POST['status'] ) ? 1 : 0,
		);

		if ( '' === $data['name'] || '' === $data['code'] ) {
			$this->set_flash( 'error', __( 'Name and code are required.', 'map-plum' ) );
			$this->redirect( 'form', $id );
		}

		$existing = $this->model->get_by_code( $data['code'] );
		if ( $existing && (int) $existing->language_id !== $id ) {
			$this->set_flash( 'error', __( 'Language code already exists.', 'map-plum' ) );
			$this->redirect( 'form', $id );
		}

		$this->model->save( $data );
		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'map_plum_language_delete_' . $id );

		if ( ! $this->model->delete( $id ) ) {
			$this->set_flash( 'error', __( 'Cannot delete the last language.', 'map-plum' ) );
		} else {
			$this->set_flash( 'success', __( 'Language deleted.', 'map-plum' ) );
		}

		$this->redirect( 'index' );
	}
}

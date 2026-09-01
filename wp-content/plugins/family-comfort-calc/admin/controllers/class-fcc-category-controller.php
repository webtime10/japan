<?php
/**
 * Category controller.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Category_Controller extends FCC_Controller {

	/** @var FCC_Category_Model */
	private $model;

	public function __construct( $route = '', $group_type = '' ) {
		parent::__construct( $route, $group_type );
		$this->model = new FCC_Category_Model( $this->group_type );
	}

	public function index() {
		$lang_id = fcc_get_default_language_id();
		$page    = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$total   = $this->model->count_list( $lang_id );
		$pages   = max( 1, (int) ceil( $total / FCC_Category_Model::PER_PAGE ) );

		if ( $page > $pages ) {
			$page = $pages;
		}

		$group_label = fcc_get_group_types()[ $this->group_type ];

		$this->render(
			'category/list',
			array(
				'title'          => $group_label,
				'categories'     => $this->model->get_list( $lang_id, $page ),
				'total'          => $total,
				'page'           => $page,
				'pages'          => $pages,
				'header_buttons' => $this->header_btn_add( $this->route, __( 'Добавить', 'family-comfort-calc' ) ),
			)
		);
	}

	public function form() {
		$id       = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$category = $id > 0 ? $this->model->get( $id ) : null;
		$lang_id  = fcc_get_default_language_id();
		$desc_raw = $id > 0 ? $this->model->get_descriptions( $id ) : array();
		$name     = '';
		$text     = '';

		if ( isset( $desc_raw[ $lang_id ] ) ) {
			$name = (string) $desc_raw[ $lang_id ]->name;
			$text = (string) $desc_raw[ $lang_id ]->description;
		}

		$group_label = fcc_get_group_types()[ $this->group_type ];

		$this->render(
			'category/form',
			array(
				'title'          => $id ? sprintf( __( 'Редактировать: %s', 'family-comfort-calc' ), $group_label ) : sprintf( __( 'Добавить: %s', 'family-comfort-calc' ), $group_label ),
				'category'       => $category,
				'name'           => $name,
				'description'    => $text,
				'header_buttons' => $this->header_btn_save( 'fcc-form-category' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'fcc_category_save' );

		$id = isset( $_POST['category_id'] ) ? (int) $_POST['category_id'] : 0;

		$data = array(
			'sort_order' => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'     => isset( $_POST['status'] ),
		);

		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$text = isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '';

		if ( '' === $name ) {
			$this->set_flash( 'error', __( 'Введите название.', 'family-comfort-calc' ) );
			$this->redirect( 'form', $id );
		}

		$lang_id = fcc_get_default_language_id();
		$descriptions = array(
			$lang_id => array(
				'name'        => $name,
				'description' => $text,
			),
		);

		$this->model->save( $id, $data, $descriptions );
		if ( $this->db_failed() ) {
			$this->flash_db_error();
			$this->redirect( 'form', $id );
		}

		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'fcc_category_delete_' . $id );

		$error = $this->model->delete( $id );
		if ( $error ) {
			$this->set_flash( 'error', $error );
		}

		$this->redirect( 'index' );
	}

	public function bulk_delete() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'fcc_category_bulk_delete' );

		$ids = isset( $_POST['category_ids'] ) ? array_map( 'intval', (array) $_POST['category_ids'] ) : array();
		$ids = array_filter( $ids );

		if ( ! empty( $ids ) ) {
			$this->model->bulk_delete( $ids );
		}

		$this->redirect( 'index' );
	}
}

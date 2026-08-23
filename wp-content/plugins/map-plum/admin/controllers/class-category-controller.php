<?php
/**
 * Category controller.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Category_Controller extends Map_Plum_Controller {

	/** @var Map_Plum_Category_Model */
	private $model;

	/** @var Map_Plum_Language_Model */
	private $lang_model;

	public function __construct( $route = 'category' ) {
		parent::__construct( $route );
		$this->model      = new Map_Plum_Category_Model();
		$this->lang_model = new Map_Plum_Language_Model();
	}

	public function index() {
		$lang_id = $this->lang_model->get_default_language_id();
		$add_url = Map_Plum_Router::url( 'category', 'add' );
		$header_buttons  = '<button type="submit" form="map-plum-list-category" class="btn btn-danger" onclick="return confirm(\'Delete selected?\');"><i class="fa fa-trash-o"></i> Delete</button>';
		$header_buttons .= '<a href="' . esc_url( $add_url ) . '" class="btn btn-primary"><i class="fa fa-plus"></i> ' . esc_html__( 'Add New', 'map-plum' ) . '</a>';
		$this->render(
			'category/list',
			array(
				'title'          => __( 'Categories', 'map-plum' ),
				'heading_title'  => __( 'Category List', 'map-plum' ),
				'items'          => $this->model->get_list( $lang_id ),
				'marker_counts'  => $this->model->get_active_marker_counts(),
				'header_buttons' => $header_buttons,
			)
		);
	}

	public function add() {
		$this->form( 0 );
	}

	public function edit() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->form( $id );
	}

	public function save() {
		if ( strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) !== 'POST' ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'map_plum_category_save' );
		$id = isset( $_POST['category_id'] ) ? (int) $_POST['category_id'] : 0;

		$main = array(
			'parent_id'  => isset( $_POST['parent_id'] ) ? (int) $_POST['parent_id'] : 0,
			'image'      => '',
			'sort_order' => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'     => isset( $_POST['status'] ) ? 1 : 0,
		);

		$languages    = $this->lang_model->get_all_active();
		$descriptions = $this->parse_descriptions( $languages, isset( $_POST['description'] ) ? $_POST['description'] : array() );

		if ( $id > 0 ) {
			$this->model->edit( $id, $main, $descriptions );
		} else {
			$id = $this->model->add( $main, $descriptions );
		}

		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$this->verify_nonce( 'map_plum_bulk_action' );
		$ids = isset( $_POST['selected'] ) ? array_map( 'intval', (array) $_POST['selected'] ) : array();
		if ( ! empty( $ids ) ) {
			$this->model->delete( $ids );
		}
		$this->redirect( 'index', 0, 'saved' );
	}

	/**
	 * @param int $id
	 */
	private function form( $id ) {
		$languages = $this->lang_model->get_all_active_sorted();
		$lang_id   = $this->lang_model->get_default_language_id();
		$item      = null;
		$desc      = array();

		if ( $id > 0 ) {
			$item = $this->model->get_category( $id );
			if ( ! $item ) {
				wp_die( esc_html__( 'Category not found.', 'map-plum' ) );
			}
			$desc = $this->model->get_descriptions( $id );
		}

		$header_buttons = '<button type="submit" form="map-plum-form-category" class="btn btn-primary"><i class="fa fa-save"></i> Сохранить</button>';
		$this->render(
			'category/form',
			array(
				'title'           => $id ? __( 'Edit Category', 'map-plum' ) : __( 'Add Category', 'map-plum' ),
				'heading_title'   => $id ? __( 'Edit Category', 'map-plum' ) : __( 'Add Category', 'map-plum' ),
				'item'            => $item,
				'descriptions'    => $desc,
				'languages'       => $languages,
				'category_id'     => $id,
				'parent_options'  => $this->model->get_options( $lang_id, $id ),
				'header_buttons'  => $header_buttons,
			)
		);
	}
}

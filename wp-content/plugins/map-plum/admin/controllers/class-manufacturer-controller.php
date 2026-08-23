<?php
/**
 * Manufacturer controller.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Manufacturer_Controller extends Map_Plum_Controller {

	/** @var Map_Plum_Manufacturer_Model */
	private $model;

	/** @var Map_Plum_Language_Model */
	private $lang_model;

	public function __construct( $route = 'manufacturer' ) {
		parent::__construct( $route );
		$this->model      = new Map_Plum_Manufacturer_Model();
		$this->lang_model = new Map_Plum_Language_Model();
	}

	public function index() {
		$lang_id = $this->lang_model->get_default_language_id();
		$add_url = Map_Plum_Router::url( 'manufacturer', 'add' );
		$header_buttons  = '<button type="submit" form="map-plum-list-manufacturer" class="btn btn-danger" onclick="return confirm(\'Delete selected?\');"><i class="fa fa-trash-o"></i> Delete</button>';
		$header_buttons .= '<a href="' . esc_url( $add_url ) . '" class="btn btn-primary"><i class="fa fa-plus"></i> ' . esc_html__( 'Add New', 'map-plum' ) . '</a>';
		$this->render(
			'manufacturer/list',
			array(
				'title'          => 'Регионы',
				'heading_title'  => 'Список регионов',
				'items'          => $this->model->get_list( $lang_id ),
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

		$this->verify_nonce( 'map_plum_manufacturer_save' );
		$id = isset( $_POST['manufacturer_id'] ) ? (int) $_POST['manufacturer_id'] : 0;

		$main = array(
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
		$item      = null;
		$desc      = array();

		if ( $id > 0 ) {
			$item = $this->model->get_manufacturer( $id );
			if ( ! $item ) {
				wp_die( 'Регион не найден.' );
			}
			$desc = $this->model->get_descriptions( $id );
		}

		$header_buttons = '<button type="submit" form="map-plum-form-manufacturer" class="btn btn-primary"><i class="fa fa-save"></i> Сохранить</button>';
		$this->render(
			'manufacturer/form',
			array(
				'title'            => $id ? 'Редактирование региона' : 'Добавление региона',
				'heading_title'    => $id ? 'Редактирование региона' : 'Добавление региона',
				'item'             => $item,
				'descriptions'     => $desc,
				'languages'        => $languages,
				'manufacturer_id'  => $id,
				'header_buttons'   => $header_buttons,
			)
		);
	}
}

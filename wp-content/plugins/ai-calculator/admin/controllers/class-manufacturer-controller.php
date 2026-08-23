<?php
/**
 * Manufacturer admin controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Manufacturer_Controller extends AI_Calculator_Controller {

	/** @var AI_Calculator_Manufacturer_Model */
	private $model;

	/** @var AI_Calculator_Language_Model */
	private $languages;

	public function __construct( $route = 'manufacturer' ) {
		parent::__construct( $route );
		$this->model     = new AI_Calculator_Manufacturer_Model();
		$this->languages = new AI_Calculator_Language_Model();
	}

	private function admin_language_id() {
		if ( isset( $_GET['language_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return (int) $_GET['language_id'];
		}
		$list = $this->languages->get_list( true );
		return ! empty( $list ) ? (int) $list[0]->language_id : 0;
	}

	public function index() {
		$lang_id = $this->admin_language_id();
		$this->render(
			'manufacturer/list',
			array(
				'title'          => __( 'Калькуляторы', 'ai-calculator' ),
				'manufacturers'  => $this->model->get_list( $lang_id ),
				'heading_title'  => __( 'Список калькуляторов', 'ai-calculator' ),
				'header_buttons' => $this->header_btn_add( 'manufacturer', __( 'Добавить калькулятор', 'ai-calculator' ) ),
			)
		);
	}

	public function form() {
		$id   = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = $id ? $this->model->get( $id ) : array(
			'manufacturer' => null,
			'descriptions' => array(),
		);

		$this->render(
			'manufacturer/form',
			array(
				'title'          => $id ? __( 'Редактирование калькулятора', 'ai-calculator' ) : __( 'Добавить калькулятор', 'ai-calculator' ),
				'manufacturer'   => $data['manufacturer'],
				'descriptions'   => $data['descriptions'],
				'languages'      => $this->languages->get_list( true ),
				'header_buttons' => $this->header_btn_save( 'ai-calculator-form-manufacturer' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_manufacturer_save' );

		$id = isset( $_POST['manufacturer_id'] ) ? (int) $_POST['manufacturer_id'] : 0;

		$data = array(
			'image'      => '',
			'sort_order' => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'     => isset( $_POST['status'] ),
		);

		$descriptions = $this->parse_descriptions(
			$this->languages->get_list(),
			isset( $_POST['description'] ) ? wp_unslash( $_POST['description'] ) : array() // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		);

		$has_name = false;
		foreach ( $descriptions as $desc ) {
			if ( ! empty( $desc['name'] ) ) {
				$has_name = true;
				break;
			}
		}

		if ( ! $has_name ) {
			$this->set_flash( 'error', __( 'Введите название калькулятора хотя бы на одном языке.', 'ai-calculator' ) );
			$this->redirect( 'form', $id );
		}

		$new_id = $this->model->save( $id, $data, $descriptions );
		if ( $this->db_failed() ) {
			$this->flash_db_error();
			$this->redirect( 'form', $id );
		}
		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'ai_calculator_manufacturer_delete_' . $id );

		$error = $this->model->delete( $id );
		if ( $error ) {
			$this->set_flash( 'error', $error );
		} else {
			$this->set_flash( 'success', __( 'Калькулятор удалён.', 'ai-calculator' ) );
		}

		$this->redirect( 'index' );
	}
}

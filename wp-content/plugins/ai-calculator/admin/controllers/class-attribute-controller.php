<?php
/**
 * Attribute admin controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Attribute_Controller extends AI_Calculator_Controller {

	/** @var AI_Calculator_Attribute_Model */
	private $model;

	/** @var AI_Calculator_Attribute_Group_Model */
	private $groups;

	/** @var AI_Calculator_Language_Model */
	private $languages;

	public function __construct( $route = 'attribute' ) {
		parent::__construct( $route );
		$this->model     = new AI_Calculator_Attribute_Model();
		$this->groups    = new AI_Calculator_Attribute_Group_Model();
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
		$lang_id  = $this->admin_language_id();
		$group_id = isset( $_GET['filter_group'] ) ? (int) $_GET['filter_group'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->render(
			'attribute/list',
			array(
				'title'          => __( 'Атрибуты', 'ai-calculator' ),
				'attributes'     => $this->model->get_list( $lang_id, $group_id ),
				'group_id'       => $group_id,
				'group_list'     => $this->groups->get_options( $lang_id ),
				'heading_title'  => __( 'Список атрибутов', 'ai-calculator' ),
				'header_buttons' => $this->header_btn_add( 'attribute', __( 'Добавить атрибут', 'ai-calculator' ) ),
			)
		);
	}

	public function form() {
		$id   = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = $id > 0 ? $this->model->get( $id ) : array(
			'attribute'    => null,
			'descriptions' => array(),
		);

		$lang_id = $this->admin_language_id();
		$group_id = $data['attribute'] ? (int) $data['attribute']->attribute_group_id : 0;
		if ( ! $group_id && isset( $_GET['filter_group'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$group_id = (int) $_GET['filter_group'];
		}

		$this->render(
			'attribute/form',
			array(
				'title'                => $id ? __( 'Редактирование атрибута', 'ai-calculator' ) : __( 'Добавить атрибут', 'ai-calculator' ),
				'attribute'            => $data['attribute'],
				'descriptions'         => $data['descriptions'],
				'languages'            => $this->languages->get_list( true ),
				'group_options'        => $this->groups->get_options( $lang_id ),
				'selected_group_id'    => $group_id,
				'header_buttons'       => $this->header_btn_save( 'ai-calculator-form-attribute' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_attribute_save' );

		$id = isset( $_POST['attribute_id'] ) ? (int) $_POST['attribute_id'] : 0;

		$data = array(
			'attribute_group_id' => isset( $_POST['attribute_group_id'] ) ? (int) $_POST['attribute_group_id'] : 0,
			'sort_order'         => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
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

		if ( empty( $data['attribute_group_id'] ) ) {
			$this->set_flash( 'error', __( 'Выберите группу атрибутов.', 'ai-calculator' ) );
			$this->redirect( 'form', $id );
		}

		if ( ! $has_name ) {
			$this->set_flash( 'error', __( 'Введите название атрибута хотя бы на одном языке.', 'ai-calculator' ) );
			$this->redirect( 'form', $id );
		}

		$new_id = $this->model->save( $id, $data, $descriptions );
		if ( $this->db_failed() || $new_id <= 0 ) {
			$this->flash_db_error();
			$this->redirect( 'form', $id );
		}
		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'ai_calculator_attribute_delete_' . $id );

		$error = $this->model->delete( $id );
		if ( $error ) {
			$this->set_flash( 'error', $error );
		} else {
			$this->set_flash( 'success', __( 'Атрибут удалён.', 'ai-calculator' ) );
		}

		$this->redirect( 'index' );
	}
}

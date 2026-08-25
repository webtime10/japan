<?php
/**
 * Attribute group admin controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Attribute_Group_Controller extends AI_Calculator_Controller {

	/** @var AI_Calculator_Attribute_Group_Model */
	private $model;

	/** @var AI_Calculator_Language_Model */
	private $languages;

	public function __construct( $route = 'attribute_group' ) {
		parent::__construct( $route );
		$this->model     = new AI_Calculator_Attribute_Group_Model();
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
			'attribute-group/list',
			array(
				'title'          => __( 'Группы атрибутов', 'ai-calculator' ),
				'groups'         => $this->model->get_list( $lang_id ),
				'heading_title'  => __( 'Список групп атрибутов', 'ai-calculator' ),
				'header_buttons' => $this->header_btn_add( 'attribute_group', __( 'Добавить группу', 'ai-calculator' ) ),
			)
		);
	}

	public function form() {
		$id   = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = $id > 0 ? $this->model->get( $id ) : array(
			'group'        => null,
			'descriptions' => array(),
		);

		$this->render(
			'attribute-group/form',
			array(
				'title'          => $id ? __( 'Редактирование группы атрибутов', 'ai-calculator' ) : __( 'Добавить группу атрибутов', 'ai-calculator' ),
				'group'          => $data['group'],
				'descriptions'   => $data['descriptions'],
				'languages'      => $this->languages->get_list( true ),
				'header_buttons' => $this->header_btn_save( 'ai-calculator-form-attribute-group' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_attribute_group_save' );

		// PK/AUTO_INCREMENT иногда слетают — без этого insert_id=0 и «Database error».
		require_once AI_CALCULATOR_PATH . 'inc/active_bd.php';
		global $wpdb;
		ai_calculator_maybe_fix_attribute_auto_increment( $wpdb->prefix . 'ai_calculator_' );

		$id = isset( $_POST['attribute_group_id'] ) ? (int) $_POST['attribute_group_id'] : 0;

		$data = array(
			'sort_order' => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
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
			$this->set_flash( 'error', __( 'Введите название группы хотя бы на одном языке.', 'ai-calculator' ) );
			$this->redirect( 'form', $id );
		}

		$is_new = $id <= 0;
		$new_id = $this->model->save( $id, $data, $descriptions );
		if ( $this->db_failed() || $new_id <= 0 ) {
			if ( $this->db_failed() ) {
				$this->flash_db_error();
			} else {
				$this->set_flash( 'error', __( 'Не удалось сохранить группу (нет AUTO_INCREMENT у attribute_group_id). Обновите страницу и попробуйте снова.', 'ai-calculator' ) );
			}
			$this->redirect( 'form', $id );
		}

		// Только для новой группы: один атрибут с тем же названием. Не воссоздаём удалённые.
		if ( $is_new ) {
			$attr_model = new AI_Calculator_Attribute_Model();
			$attr_model->ensure_attribute_for_group( $new_id );
			if ( $this->db_failed() ) {
				$this->flash_db_error();
				$this->redirect( 'form', $new_id );
			}
		}

		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'ai_calculator_attribute_group_delete_' . $id );

		$error = $this->model->delete( $id );
		if ( $error ) {
			$this->set_flash( 'error', $error );
		} else {
			$this->set_flash( 'success', __( 'Группа атрибутов удалена.', 'ai-calculator' ) );
		}

		$this->redirect( 'index' );
	}
}

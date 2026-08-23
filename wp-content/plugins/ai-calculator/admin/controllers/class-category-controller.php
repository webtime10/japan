<?php
/**
 * Category admin controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Category_Controller extends AI_Calculator_Controller {

	/** @var AI_Calculator_Category_Model */
	private $model;

	/** @var AI_Calculator_Language_Model */
	private $languages;

	/** @var AI_Calculator_Manufacturer_Model */
	private $manufacturers;

	public function __construct( $route = 'category' ) {
		parent::__construct( $route );
		$this->model         = new AI_Calculator_Category_Model();
		$this->languages     = new AI_Calculator_Language_Model();
		$this->manufacturers = new AI_Calculator_Manufacturer_Model();
	}

	/**
	 * @return int
	 */
	private function admin_language_id() {
		if ( isset( $_GET['language_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return (int) $_GET['language_id'];
		}
		$list = $this->languages->get_list( true );
		return ! empty( $list ) ? (int) $list[0]->language_id : 0;
	}

	public function index() {
		$lang_id         = $this->admin_language_id();
		$manufacturer_id = isset( $_GET['filter_manufacturer'] ) ? (int) $_GET['filter_manufacturer'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->render(
			'category/list',
			array(
				'title'              => __( 'Categories', 'ai-calculator' ),
				'categories'         => $this->model->get_list( $lang_id, $manufacturer_id ),
				'manufacturer_id'    => $manufacturer_id,
				'manufacturer_list'  => $this->manufacturers->get_options( $lang_id ),
				'heading_title'      => __( 'Category List', 'ai-calculator' ),
				'header_buttons'     => $this->header_btn_add( 'category', __( 'Add Category', 'ai-calculator' ) ),
			)
		);
	}

	public function form() {
		$id   = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = $id ? $this->model->get( $id ) : array(
			'category'     => null,
			'descriptions' => array(),
		);

		$lang_list = $this->languages->get_list( true );
		$lang_id   = $this->admin_language_id();
		if ( ! $lang_id && ! empty( $lang_list ) ) {
			$lang_id = (int) $lang_list[0]->language_id;
		}

		$mfr_id = $data['category'] ? (int) $data['category']->manufacturer_id : 0;
		if ( ! $mfr_id && isset( $_GET['filter_manufacturer'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$mfr_id = (int) $_GET['filter_manufacturer'];
		}

		$this->render(
			'category/form',
			array(
				'title'                => $id ? __( 'Edit Category', 'ai-calculator' ) : __( 'Add Category', 'ai-calculator' ),
				'category'             => $data['category'],
				'descriptions'         => $data['descriptions'],
				'languages'            => $lang_list,
				'manufacturer_options' => $this->manufacturers->get_options( $lang_id ),
				'parent_categories'    => $this->model->get_list( $lang_id ),
				'header_buttons'       => $this->header_btn_save( 'ai-calculator-form-category' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_category_save' );

		$id = isset( $_POST['category_id'] ) ? (int) $_POST['category_id'] : 0;

		$data = array(
			'manufacturer_id' => isset( $_POST['manufacturer_id'] ) ? (int) $_POST['manufacturer_id'] : 0,
			'parent_id'       => isset( $_POST['parent_id'] ) ? (int) $_POST['parent_id'] : 0,
			'image'           => isset( $_POST['image'] ) ? esc_url_raw( wp_unslash( $_POST['image'] ) ) : '',
			'sort_order'      => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'          => isset( $_POST['status'] ),
		);

		if ( $id > 0 && (int) $data['parent_id'] === $id ) {
			$data['parent_id'] = 0;
		}

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

		if ( empty( $data['manufacturer_id'] ) ) {
			$this->set_flash( 'error', __( 'Выберите калькулятор.', 'ai-calculator' ) );
			$this->redirect( 'form', $id );
		}

		if ( ! $has_name ) {
			$this->set_flash( 'error', __( 'Enter a category name in at least one language.', 'ai-calculator' ) );
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
		check_admin_referer( 'ai_calculator_category_delete_' . $id );

		$error = $this->model->delete( $id );
		if ( $error ) {
			$this->set_flash( 'error', $error );
		} else {
			$this->set_flash( 'success', __( 'Category deleted.', 'ai-calculator' ) );
		}

		$this->redirect( 'index' );
	}

	public function bulk_delete() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_category_bulk_delete' );

		$ids = isset( $_POST['category_ids'] ) && is_array( $_POST['category_ids'] )
			? array_filter( array_map( 'intval', wp_unslash( $_POST['category_ids'] ) ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();
		$ids = array_values( array_unique( $ids ) );

		if ( empty( $ids ) ) {
			$this->set_flash( 'error', __( 'Выберите категории для удаления.', 'ai-calculator' ) );
			$this->redirect_to_filtered_index();
		}

		$deleted = 0;
		$errors  = array();
		foreach ( $ids as $id ) {
			$error = $this->model->delete( $id );
			if ( $error ) {
				$errors[] = '#' . $id . ': ' . $error;
				continue;
			}
			$deleted++;
		}

		if ( ! empty( $errors ) ) {
			$message = sprintf(
				/* translators: %d: deleted categories count */
				_n( '%d category deleted.', '%d categories deleted.', $deleted, 'ai-calculator' ),
				$deleted
			);
			$this->set_flash( 'error', $message . ' ' . implode( ' ', $errors ) );
		} else {
			$this->set_flash(
				'success',
				sprintf(
					/* translators: %d: deleted categories count */
					_n( '%d category deleted.', '%d categories deleted.', $deleted, 'ai-calculator' ),
					$deleted
				)
			);
		}

		$this->redirect_to_filtered_index();
	}

	private function redirect_to_filtered_index() {
		$args = array(
			'filter_manufacturer' => isset( $_POST['filter_manufacturer'] ) ? (int) $_POST['filter_manufacturer'] : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing
		);
		$url  = AI_Calculator_Router::url( 'category', 'index', 0, $args );

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
}

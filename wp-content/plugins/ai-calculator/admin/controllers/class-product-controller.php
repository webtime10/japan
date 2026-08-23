<?php
/**
 * Product admin controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Product_Controller extends AI_Calculator_Controller {

	/** @var AI_Calculator_Product_Model */
	private $model;

	/** @var AI_Calculator_Category_Model */
	private $categories;

	/** @var AI_Calculator_Manufacturer_Model */
	private $manufacturers;

	/** @var AI_Calculator_Language_Model */
	private $languages;

	public function __construct( $route = 'product' ) {
		parent::__construct( $route );
		$this->model         = new AI_Calculator_Product_Model();
		$this->categories    = new AI_Calculator_Category_Model();
		$this->manufacturers = new AI_Calculator_Manufacturer_Model();
		$this->languages     = new AI_Calculator_Language_Model();
	}

	private function admin_language_id() {
		if ( isset( $_GET['language_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return (int) $_GET['language_id'];
		}
		$list = $this->languages->get_list( true );
		return ! empty( $list ) ? (int) $list[0]->language_id : 0;
	}

	public function index() {
		$lang_id     = $this->admin_language_id();
		$category_id     = isset( $_GET['filter_category'] ) ? (int) $_GET['filter_category'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$manufacturer_id = isset( $_GET['filter_manufacturer'] ) ? (int) $_GET['filter_manufacturer'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page            = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$filters = array(
			'language_id'     => $lang_id,
			'category_id'     => $category_id,
			'manufacturer_id' => $manufacturer_id,
			'page'            => $page,
		);

		$total = $this->model->count_list( $filters );
		$pages = max( 1, (int) ceil( $total / AI_Calculator_Product_Model::PER_PAGE ) );

		$this->render(
			'product/list',
			array(
				'title'          => __( 'Products', 'ai-calculator' ),
				'products'          => $this->model->get_list( $filters ),
				'category_id'       => $category_id,
				'manufacturer_id'   => $manufacturer_id,
				'admin_language_id' => $lang_id,
				'category_list'     => $this->categories->get_list( $lang_id, $manufacturer_id ),
				'manufacturer_list' => $this->manufacturers->get_options( $lang_id ),
				'total'             => $total,
				'page'           => $page,
				'pages'          => $pages,
				'heading_title'  => __( 'Product List', 'ai-calculator' ),
				'header_buttons' => $this->header_btn_add( 'product', __( 'Add Product', 'ai-calculator' ) ),
			)
		);
	}

	public function form() {
		$id   = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = $id ? $this->model->get( $id ) : array(
			'product'             => null,
			'descriptions'        => array(),
			'category_ids'        => array(),
			'related_product_ids' => array(),
		);

		$lang_list = $this->languages->get_list( true );
		$lang_id   = $this->admin_language_id();
		if ( ! $lang_id && ! empty( $lang_list ) ) {
			$lang_id = (int) $lang_list[0]->language_id;
		}

		$category_id = ! empty( $data['category_ids'] ) ? (int) $data['category_ids'][0] : 0;
		$related_ids = isset( $data['related_product_ids'] ) ? $data['related_product_ids'] : array();
		$stashed     = $this->pull_stashed_product_form( $id );
		if ( $stashed ) {
			$data['product']      = $stashed['product'];
			$data['descriptions'] = $stashed['descriptions'];
			$category_id          = $stashed['category_id'];
			$related_ids          = $stashed['related_product_ids'];
		}

		$this->render(
			'product/form',
			array(
				'title'                => $id ? __( 'Edit Product', 'ai-calculator' ) : __( 'Add Product', 'ai-calculator' ),
				'product'              => $data['product'],
				'descriptions'         => $data['descriptions'],
				'category_id'          => $category_id,
				'related_items'        => $this->model->get_related_chip_items( $lang_id, $category_id, $id, $related_ids ),
				'admin_language_id'    => $lang_id,
				'languages'            => $lang_list,
				'category_list'        => $this->categories->get_list( $lang_id ),
				'manufacturer_options' => $this->manufacturers->get_options( $lang_id ),
				'header_buttons'       => $this->header_btn_save( 'ai-calculator-form-product' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_product_save' );

		$id = isset( $_POST['product_id'] ) ? (int) $_POST['product_id'] : 0;

		$data = array(
			'manufacturer_id' => isset( $_POST['manufacturer_id'] ) ? (int) $_POST['manufacturer_id'] : 0,
			'image'           => '',
			'sort_order'      => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'          => isset( $_POST['status'] ),
		);

		$category_id  = isset( $_POST['category_id'] ) ? (int) $_POST['category_id'] : 0;
		$category_ids = $category_id > 0 ? array( $category_id ) : array();

		$related_product_ids = array();
		if ( isset( $_POST['related_product_ids'] ) && is_array( $_POST['related_product_ids'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$related_product_ids = array_map( 'intval', wp_unslash( $_POST['related_product_ids'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		$langs        = $this->languages->get_list( true );
		$post_desc    = isset( $_POST['description'] ) ? wp_unslash( $_POST['description'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$descriptions = $this->parse_product_descriptions( $langs, $post_desc );

		$errors = $this->validate_product_form( $data, $category_id, $descriptions, $langs );
		if ( ! empty( $errors ) ) {
			$this->set_flash( 'error', $errors );
			$this->stash_product_form( $id, $data, $descriptions, $category_id, $related_product_ids );
			$this->redirect( 'form', $id );
		}

		$new_id = $this->model->save( $id, $data, $descriptions, $category_ids, $related_product_ids );
		if ( $this->db_failed() || $new_id <= 0 ) {
			$this->flash_db_error();
			if ( $new_id <= 0 ) {
				$this->set_flash( 'error', __( 'Товар не был создан. Проверьте структуру таблицы товаров.', 'ai-calculator' ) );
			}
			$this->stash_product_form( $id, $data, $descriptions, $category_id, $related_product_ids );
			$this->redirect( 'form', $id );
		}

		delete_transient( $this->product_form_transient_key() );
		$this->redirect_to_index_with_filters( $category_id, (int) $data['manufacturer_id'], 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'ai_calculator_product_delete_' . $id );

		$this->model->delete( $id );
		$this->set_flash( 'success', __( 'Product deleted.', 'ai-calculator' ) );
		$this->redirect( 'index' );
	}

	public function bulk_delete() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'ai_calculator_product_bulk_delete' );

		$ids = isset( $_POST['product_ids'] ) && is_array( $_POST['product_ids'] )
			? array_filter( array_map( 'intval', wp_unslash( $_POST['product_ids'] ) ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();
		$ids = array_values( array_unique( $ids ) );

		if ( empty( $ids ) ) {
			$this->set_flash( 'error', __( 'Выберите продукты для удаления.', 'ai-calculator' ) );
			$this->redirect_to_filtered_index();
		}

		foreach ( $ids as $id ) {
			$this->model->delete( $id );
		}

		$this->set_flash(
			'success',
			sprintf(
				/* translators: %d: deleted products count */
				_n( '%d product deleted.', '%d products deleted.', count( $ids ), 'ai-calculator' ),
				count( $ids )
			)
		);
		$this->redirect_to_filtered_index();
	}

	public function save_sort_order() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		if (
			! isset( $_POST['ai_calculator_sort_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ai_calculator_sort_nonce'] ) ), 'ai_calculator_product_sort_order' )
		) {
			wp_die( esc_html__( 'Security check failed.', 'ai-calculator' ) );
		}

		$product_id = isset( $_POST['product_sort_id'] ) ? (int) $_POST['product_sort_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$sort_order = 0;
		if ( isset( $_POST['sort_order'] ) && is_array( $_POST['sort_order'] ) && isset( $_POST['sort_order'][ $product_id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$sort_order = (int) $_POST['sort_order'][ $product_id ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing
		}

		if ( $product_id <= 0 ) {
			$this->set_flash( 'error', __( 'Invalid product.', 'ai-calculator' ) );
			$this->redirect_to_filtered_index();
		}

		if ( ! $this->model->update_sort_order( $product_id, $sort_order ) ) {
			$this->flash_db_error();
			$this->redirect_to_filtered_index();
		}

		$this->set_flash( 'success', __( 'Sort order saved.', 'ai-calculator' ) );
		$this->redirect_to_filtered_index();
	}

	/**
	 * @param array<int, object> $languages
	 * @param array              $post_description
	 * @return array<int, array<string, string>>
	 */
	private function parse_product_descriptions( $languages, $post_description ) {
		$out = array();
		if ( ! is_array( $post_description ) ) {
			return $out;
		}
		foreach ( $languages as $lang ) {
			$lid = (int) $lang->language_id;
			$row = isset( $post_description[ $lid ] ) && is_array( $post_description[ $lid ] )
				? $post_description[ $lid ]
				: array();
			$out[ $lid ] = array(
				'name'        => isset( $row['name'] ) ? sanitize_text_field( wp_unslash( $row['name'] ) ) : '',
				'description' => isset( $row['description'] ) ? wp_kses_post( wp_unslash( $row['description'] ) ) : '',
				'block1' => isset( $row['block1'] ) ? sanitize_text_field( wp_unslash( $row['block1'] ) ) : '',
				'block2' => isset( $row['block2'] ) ? sanitize_text_field( wp_unslash( $row['block2'] ) ) : '',
				'block3' => isset( $row['block3'] ) ? sanitize_text_field( wp_unslash( $row['block3'] ) ) : '',
				'block4' => isset( $row['block4'] ) ? sanitize_text_field( wp_unslash( $row['block4'] ) ) : '',
				'block5' => isset( $row['block5'] ) ? sanitize_text_field( wp_unslash( $row['block5'] ) ) : '',
				'block6' => isset( $row['block6'] ) ? sanitize_text_field( wp_unslash( $row['block6'] ) ) : '',
			);
		}
		return $out;
	}

	/**
	 * @param array<string, mixed>     $data
	 * @param int                      $category_id
	 * @param array<int, array>        $descriptions
	 * @param array<int, object>       $languages
	 * @return array<int, string>
	 */
	private function validate_product_form( $data, $category_id, $descriptions, $languages ) {
		$errors = array();

		if ( empty( $data['manufacturer_id'] ) ) {
			$errors[] = __( 'Выберите калькулятор.', 'ai-calculator' );
		}

		if ( $category_id <= 0 ) {
			$errors[] = __( 'Выберите категорию.', 'ai-calculator' );
		}

		if ( ! empty( $data['manufacturer_id'] ) && $category_id > 0 ) {
			$category_data = $this->categories->get( $category_id );
			$category      = isset( $category_data['category'] ) ? $category_data['category'] : null;
			if ( ! $category || (int) $category->manufacturer_id !== (int) $data['manufacturer_id'] ) {
				$errors[] = __( 'Выбранная категория не относится к выбранному калькулятору.', 'ai-calculator' );
			}
		}

		foreach ( $languages as $lang ) {
			$lid  = (int) $lang->language_id;
			$name = isset( $descriptions[ $lid ]['name'] ) ? trim( $descriptions[ $lid ]['name'] ) : '';
			if ( '' === $name ) {
				$errors[] = sprintf(
					/* translators: %s: language name */
					__( 'Введите название продукта на языке «%s».', 'ai-calculator' ),
					$lang->name
				);
			}
		}

		if ( empty( $errors ) ) {
			return array();
		}

		return array_merge(
			array( __( 'Не все обязательные поля заполнены:', 'ai-calculator' ) ),
			$errors
		);
	}

	/**
	 * @param int                   $product_id
	 * @param array<string, mixed>  $data
	 * @param array<int, array>     $descriptions
	 * @param int                   $category_id
	 * @param array<int>            $related_product_ids
	 */
	private function stash_product_form( $product_id, $data, $descriptions, $category_id, $related_product_ids = array() ) {
		set_transient(
			$this->product_form_transient_key(),
			array(
				'product_id'          => (int) $product_id,
				'data'                => $data,
				'descriptions'        => $descriptions,
				'category_id'         => (int) $category_id,
				'related_product_ids' => array_map( 'intval', (array) $related_product_ids ),
			),
			300
		);
	}

	/**
	 * @param int $product_id
	 * @return array{product: object, descriptions: array<int, object>, category_id: int, related_product_ids: array<int>}|null
	 */
	private function pull_stashed_product_form( $product_id ) {
		$stashed = get_transient( $this->product_form_transient_key() );
		delete_transient( $this->product_form_transient_key() );

		if ( ! is_array( $stashed ) || (int) $stashed['product_id'] !== (int) $product_id ) {
			return null;
		}

		$product = (object) array_merge(
			array(
				'product_id'      => (int) $product_id,
				'manufacturer_id' => 0,
				'image'           => '',
				'sort_order'      => 0,
				'status'          => 1,
			),
			isset( $stashed['data'] ) && is_array( $stashed['data'] ) ? $stashed['data'] : array()
		);

		$descriptions = array();
		if ( ! empty( $stashed['descriptions'] ) && is_array( $stashed['descriptions'] ) ) {
			foreach ( $stashed['descriptions'] as $language_id => $row ) {
				if ( is_array( $row ) ) {
					$descriptions[ (int) $language_id ] = (object) $row;
				}
			}
		}

		return array(
			'product'             => $product,
			'descriptions'        => $descriptions,
			'category_id'         => isset( $stashed['category_id'] ) ? (int) $stashed['category_id'] : 0,
			'related_product_ids' => isset( $stashed['related_product_ids'] ) ? array_map( 'intval', (array) $stashed['related_product_ids'] ) : array(),
		);
	}

	/**
	 * @return string
	 */
	private function product_form_transient_key() {
		return 'ai_calculator_product_form_' . get_current_user_id();
	}

	private function redirect_to_filtered_index() {
		$args = array(
			'filter_category'     => isset( $_POST['filter_category'] ) ? (int) $_POST['filter_category'] : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing
			'filter_manufacturer' => isset( $_POST['filter_manufacturer'] ) ? (int) $_POST['filter_manufacturer'] : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing
			'paged'               => isset( $_POST['paged'] ) ? max( 1, (int) $_POST['paged'] ) : 1, // phpcs:ignore WordPress.Security.NonceVerification.Missing
		);
		$url  = AI_Calculator_Router::url( 'product', 'index', 0, $args );

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

	private function redirect_to_index_with_filters( $category_id, $manufacturer_id, $message = '' ) {
		$args = array(
			'filter_category'     => (int) $category_id,
			'filter_manufacturer' => (int) $manufacturer_id,
		);
		$url  = AI_Calculator_Router::url( 'product', 'index', 0, $args );

		if ( $message ) {
			$url = add_query_arg( 'message', $message, $url );
		}

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

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

	/** @var AI_Calculator_Attribute_Model */
	private $attributes;

	public function __construct( $route = 'product' ) {
		parent::__construct( $route );
		$this->model         = new AI_Calculator_Product_Model();
		$this->categories    = new AI_Calculator_Category_Model();
		$this->manufacturers = new AI_Calculator_Manufacturer_Model();
		$this->languages     = new AI_Calculator_Language_Model();
		$this->attributes    = new AI_Calculator_Attribute_Model();
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

		$category_id            = ! empty( $data['category_ids'] ) ? (int) $data['category_ids'][0] : 0;
		$related_ids            = isset( $data['related_product_ids'] ) ? $data['related_product_ids'] : array();
		$selected_attribute_ids = $id > 0 ? $this->attributes->get_product_attribute_ids( $id ) : array();
		$stashed                = $this->pull_stashed_product_form( $id );
		if ( $stashed ) {
			$data['product']            = $stashed['product'];
			$data['descriptions']       = $stashed['descriptions'];
			$category_id                = $stashed['category_id'];
			$related_ids                = $stashed['related_product_ids'];
			$selected_attribute_ids     = $stashed['selected_attribute_ids'];
		}

		$this->render(
			'product/form',
			array(
				'title'                          => $id ? __( 'Edit Product', 'ai-calculator' ) : __( 'Add Product', 'ai-calculator' ),
				'product'                        => $data['product'],
				'descriptions'                   => $data['descriptions'],
				'category_id'                    => $category_id,
				'related_items'                  => $this->model->get_related_chip_items( $lang_id, $category_id, $id, $related_ids ),
				'selected_attribute_ids'         => $selected_attribute_ids,
				'attribute_options'              => $this->attributes->get_flat_checkbox_options( $lang_id ),
				'family_comfort_manufacturer_id' => $this->get_family_comfort_manufacturer_id(),
				'is_family_comfort'              => $this->is_family_comfort_product(
					$data['product'] ? (int) $data['product']->manufacturer_id : 0,
					$category_id
				),
				'admin_language_id'              => $lang_id,
				'languages'                      => $lang_list,
				'category_list'                  => $this->categories->get_list( $lang_id ),
				'manufacturer_options'           => $this->manufacturers->get_options( $lang_id ),
				'header_buttons'                 => $this->header_btn_save( 'ai-calculator-form-product' ),
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
			'image'           => isset( $_POST['image'] ) ? esc_url_raw( wp_unslash( (string) $_POST['image'] ) ) : '',
			'image2'          => isset( $_POST['image2'] ) ? esc_url_raw( wp_unslash( (string) $_POST['image2'] ) ) : '',
			'image3'          => isset( $_POST['image3'] ) ? esc_url_raw( wp_unslash( (string) $_POST['image3'] ) ) : '',
			'image4'          => isset( $_POST['image4'] ) ? esc_url_raw( wp_unslash( (string) $_POST['image4'] ) ) : '',
			'image5'          => isset( $_POST['image5'] ) ? esc_url_raw( wp_unslash( (string) $_POST['image5'] ) ) : '',
			'image6'          => isset( $_POST['image6'] ) ? esc_url_raw( wp_unslash( (string) $_POST['image6'] ) ) : '',
			'sort_order'      => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'          => isset( $_POST['status'] ),
		);

		$category_id  = isset( $_POST['category_id'] ) ? (int) $_POST['category_id'] : 0;
		$category_ids = $category_id > 0 ? array( $category_id ) : array();

		if ( ! $this->is_family_comfort_manufacturer( (int) $data['manufacturer_id'] ) && $category_id > 0 ) {
			$category_data = $this->categories->get( $category_id );
			$category      = isset( $category_data['category'] ) ? $category_data['category'] : null;
			if ( $category && $this->is_family_comfort_manufacturer( (int) $category->manufacturer_id ) ) {
				$data['manufacturer_id'] = (int) $category->manufacturer_id;
			}
		}

		$related_product_ids = array();
		if ( isset( $_POST['related_product_ids'] ) && is_array( $_POST['related_product_ids'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$related_product_ids = array_map( 'intval', wp_unslash( $_POST['related_product_ids'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		$langs        = $this->languages->get_list( true );
		$lang_id      = $this->admin_language_id();
		if ( ! $lang_id && ! empty( $langs ) ) {
			$lang_id = (int) $langs[0]->language_id;
		}
		$post_general = isset( $_POST['description'] ) && is_array( $_POST['description'] )
			? wp_unslash( $_POST['description'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();
		$post_fc      = isset( $_POST['fc_description'] ) && is_array( $_POST['fc_description'] )
			? wp_unslash( $_POST['fc_description'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();
		$is_fc_product = $this->is_family_comfort_product( (int) $data['manufacturer_id'], $category_id );
		$post_desc     = $this->merge_product_post_descriptions( $langs, $post_general, $post_fc, $is_fc_product );
		$descriptions  = $this->parse_product_descriptions( $langs, $post_desc, $is_fc_product );
		$product_attributes = $this->parse_product_attributes(
			isset( $_POST['product_attribute_ids'] ) ? wp_unslash( $_POST['product_attribute_ids'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			isset( $_POST['product_attribute'] ) ? wp_unslash( $_POST['product_attribute'] ) : array(), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$langs
		);

		$errors = $this->validate_product_form( $data, $category_id, $descriptions, $langs );
		if ( ! empty( $errors ) ) {
			$this->set_flash( 'error', $errors );
			$this->stash_product_form( $id, $data, $descriptions, $category_id, $related_product_ids, $product_attributes );
			$this->redirect( 'form', $id );
		}

		$new_id = $this->model->save( $id, $data, $descriptions, $category_ids, $related_product_ids, $product_attributes );
		if ( $this->db_failed() || $new_id <= 0 ) {
			$this->flash_db_error();
			if ( $new_id <= 0 ) {
				$this->set_flash( 'error', __( 'Товар не был создан. Проверьте структуру таблицы товаров.', 'ai-calculator' ) );
			}
			$this->stash_product_form( $id, $data, $descriptions, $category_id, $related_product_ids, $product_attributes );
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
	 * @param bool               $is_fc_product
	 * @return array<int, array<string, string>>
	 */
	private function parse_product_descriptions( $languages, $post_description, $is_fc_product = false ) {
		$out = array();
		if ( ! is_array( $post_description ) ) {
			return $out;
		}
		foreach ( $languages as $lang ) {
			$lid = (int) $lang->language_id;
			$row = $this->get_post_description_row( $post_description, $lid );
			$out[ $lid ] = array(
				'name'        => isset( $row['name'] ) ? sanitize_text_field( wp_unslash( $row['name'] ) ) : '',
				'description' => isset( $row['description'] ) ? wp_kses_post( wp_unslash( $row['description'] ) ) : '',
				'block1'      => isset( $row['block1'] ) ? $this->parse_product_block1( $row['block1'], $is_fc_product ) : '',
				'block2' => isset( $row['block2'] ) ? sanitize_text_field( wp_unslash( $row['block2'] ) ) : '',
				'block3' => isset( $row['block3'] ) ? sanitize_text_field( wp_unslash( $row['block3'] ) ) : '',
				'block4' => isset( $row['block4'] ) ? sanitize_text_field( wp_unslash( $row['block4'] ) ) : '',
				'block5' => isset( $row['block5'] ) ? sanitize_text_field( wp_unslash( $row['block5'] ) ) : '',
				'block6' => $this->parse_product_russian_name( $row ),
				'block7' => $this->parse_product_block6_1( $row ),
				'block8' => isset( $row['block8'] ) ? sanitize_text_field( wp_unslash( $row['block8'] ) ) : '',
				'dop1'   => isset( $row['dop1'] ) ? sanitize_text_field( wp_unslash( $row['dop1'] ) ) : '',
			);
		}
		return $out;
	}

	/**
	 * Русское название: отдельное поле name_ru в форме → block6 в БД.
	 *
	 * @param array<string, mixed> $row
	 * @return string
	 */
	private function parse_product_russian_name( $row ) {
		if ( isset( $row['name_ru'] ) ) {
			return sanitize_text_field( wp_unslash( (string) $row['name_ru'] ) );
		}

		if ( isset( $row['block6'] ) ) {
			return sanitize_text_field( wp_unslash( (string) $row['block6'] ) );
		}

		return '';
	}

	/**
	 * Блок 6_1 в форме → block7 в БД (block6 занят русским названием).
	 *
	 * @param array<string, mixed> $row
	 * @return string
	 */
	private function parse_product_block6_1( $row ) {
		if ( isset( $row['block6_1'] ) ) {
			return sanitize_text_field( wp_unslash( (string) $row['block6_1'] ) );
		}

		if ( isset( $row['block7'] ) ) {
			return sanitize_text_field( wp_unslash( (string) $row['block7'] ) );
		}

		return '';
	}

	/**
	 * Блок1: текст для обычных товаров, URL только для Family Comfort.
	 *
	 * @param mixed $value
	 * @param bool  $is_fc_product
	 * @return string
	 */
	private function parse_product_block1( $value, $is_fc_product ) {
		$value = wp_unslash( (string) $value );

		if ( $is_fc_product ) {
			return esc_url_raw( $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * General + Family Comfort: разные POST-массивы, чтобы скрытые поля не затирали данные.
	 *
	 * @param array<int, object> $languages
	 * @param array              $post_general
	 * @param array              $post_fc
	 * @param bool               $is_fc_product
	 * @return array
	 */
	private function merge_product_post_descriptions( $languages, $post_general, $post_fc, $is_fc_product ) {
		$merged = array();

		foreach ( $languages as $lang ) {
			$lid         = (int) $lang->language_id;
			$general_row = $this->get_post_description_row( $post_general, $lid );

			if ( $is_fc_product ) {
				$fc_row         = $this->get_post_description_row( $post_fc, $lid );
				$merged[ $lid ] = $fc_row;
				if ( isset( $general_row['name'] ) ) {
					$merged[ $lid ]['name'] = $general_row['name'];
				}
				if ( isset( $general_row['name_ru'] ) ) {
					$merged[ $lid ]['name_ru'] = $general_row['name_ru'];
				}
				continue;
			}

			$merged[ $lid ] = $general_row;
		}

		return $merged;
	}

	/**
	 * @param array $post_description
	 * @param int   $language_id
	 * @return array<string, mixed>
	 */
	private function get_post_description_row( $post_description, $language_id ) {
		if ( ! is_array( $post_description ) ) {
			return array();
		}

		$language_id = (int) $language_id;
		if ( isset( $post_description[ $language_id ] ) && is_array( $post_description[ $language_id ] ) ) {
			return $post_description[ $language_id ];
		}

		$key = (string) $language_id;
		if ( isset( $post_description[ $key ] ) && is_array( $post_description[ $key ] ) ) {
			return $post_description[ $key ];
		}

		return array();
	}

	/**
	 * @param array|mixed       $post_ids
	 * @param array             $post_attributes
	 * @param array<int, object> $languages
	 * @return array<int, array<int, array<string, string>>>
	 */
	private function parse_product_attributes( $post_ids, $post_attributes, $languages ) {
		unset( $post_attributes );

		$ids = array();
		if ( is_array( $post_ids ) ) {
			$ids = array_values( array_unique( array_filter( array_map( 'intval', $post_ids ) ) ) );
		} elseif ( (int) $post_ids > 0 ) {
			$ids = array( (int) $post_ids );
		}

		if ( empty( $ids ) ) {
			return array();
		}

		$out = array();
		foreach ( $ids as $attribute_id ) {
			$attribute_id = (int) $attribute_id;
			if ( $attribute_id <= 0 ) {
				continue;
			}

			$out[ $attribute_id ] = array();
			foreach ( $languages as $lang ) {
				$out[ $attribute_id ][ (int) $lang->language_id ] = array();
			}
		}

		return $out;
	}

	/**
	 * @return int
	 */
	private function get_family_comfort_manufacturer_id() {
		return function_exists( 'ai_calculator_get_family_comfort_manufacturer_id' )
			? ai_calculator_get_family_comfort_manufacturer_id()
			: 0;
	}

	/**
	 * @param int $manufacturer_id
	 * @param int $category_id
	 * @return bool
	 */
	private function is_family_comfort_product( $manufacturer_id, $category_id = 0 ) {
		if ( $this->is_family_comfort_manufacturer( (int) $manufacturer_id ) ) {
			return true;
		}

		$category_id = (int) $category_id;
		if ( $category_id <= 0 ) {
			return false;
		}

		return function_exists( 'ai_calculator_is_family_comfort_category' )
			&& ai_calculator_is_family_comfort_category( $category_id );
	}

	/**
	 * @param int $manufacturer_id
	 * @return bool
	 */
	private function is_family_comfort_manufacturer( $manufacturer_id ) {
		$manufacturer_id = (int) $manufacturer_id;
		if ( $manufacturer_id <= 0 ) {
			return false;
		}

		$family_comfort_id = $this->get_family_comfort_manufacturer_id();
		if ( $family_comfort_id > 0 && $manufacturer_id === $family_comfort_id ) {
			return true;
		}

		$lang_id = $this->admin_language_id();
		if ( ! $lang_id ) {
			$list = $this->languages->get_list( true );
			$lang_id = ! empty( $list ) ? (int) $list[0]->language_id : 0;
		}

		$labels = $this->manufacturers->get_options( $lang_id );
		$label  = isset( $labels[ $manufacturer_id ] ) ? (string) $labels[ $manufacturer_id ] : '';

		return function_exists( 'ai_calculator_family_comfort_name_matches' )
			&& ai_calculator_family_comfort_name_matches( $label );
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
	 * @param array                 $product_attributes
	 */
	private function stash_product_form( $product_id, $data, $descriptions, $category_id, $related_product_ids = array(), $product_attributes = array() ) {
		$attribute_texts          = array();
		$selected_attribute_ids   = array();
		if ( is_array( $product_attributes ) ) {
			foreach ( $product_attributes as $attribute_id => $langs ) {
				$attribute_id = (int) $attribute_id;
				if ( $attribute_id <= 0 ) {
					continue;
				}
				$selected_attribute_ids[] = $attribute_id;
				$attribute_texts[ $attribute_id ] = is_array( $langs ) ? $langs : array();
			}
		}

		set_transient(
			$this->product_form_transient_key(),
			array(
				'product_id'             => (int) $product_id,
				'data'                   => $data,
				'descriptions'           => $descriptions,
				'category_id'            => (int) $category_id,
				'related_product_ids'    => array_map( 'intval', (array) $related_product_ids ),
				'selected_attribute_ids' => array_values( array_unique( $selected_attribute_ids ) ),
				'attribute_texts'        => $attribute_texts,
			),
			300
		);
	}

	/**
	 * @param int $product_id
	 * @return array{product: object, descriptions: array<int, object>, category_id: int, related_product_ids: array<int>, selected_attribute_ids: array<int>, attribute_texts: array<int, array<int, string>>}|null
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
				'image2'          => '',
				'image3'          => '',
				'image4'          => '',
				'image5'          => '',
				'image6'          => '',
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

		$selected_attribute_ids = array();
		$attribute_texts        = array();
		if ( isset( $stashed['selected_attribute_ids'] ) && is_array( $stashed['selected_attribute_ids'] ) ) {
			$selected_attribute_ids = array_map( 'intval', $stashed['selected_attribute_ids'] );
		} elseif ( isset( $stashed['product_attributes'] ) && is_array( $stashed['product_attributes'] ) ) {
			$selected_attribute_ids = array_map( 'intval', array_keys( $stashed['product_attributes'] ) );
		}
		if ( isset( $stashed['attribute_texts'] ) && is_array( $stashed['attribute_texts'] ) ) {
			foreach ( $stashed['attribute_texts'] as $attribute_id => $langs ) {
				$attribute_id = (int) $attribute_id;
				if ( $attribute_id <= 0 || ! is_array( $langs ) ) {
					continue;
				}
				$attribute_texts[ $attribute_id ] = $langs;
			}
		} elseif ( isset( $stashed['product_attributes'] ) && is_array( $stashed['product_attributes'] ) ) {
			foreach ( $stashed['product_attributes'] as $attribute_id => $langs ) {
				$attribute_id = (int) $attribute_id;
				if ( $attribute_id <= 0 || ! is_array( $langs ) ) {
					continue;
				}
				$attribute_texts[ $attribute_id ] = $langs;
			}
		}

		return array(
			'product'                => $product,
			'descriptions'           => $descriptions,
			'category_id'            => isset( $stashed['category_id'] ) ? (int) $stashed['category_id'] : 0,
			'related_product_ids'    => isset( $stashed['related_product_ids'] ) ? array_map( 'intval', (array) $stashed['related_product_ids'] ) : array(),
			'selected_attribute_ids' => $selected_attribute_ids,
			'attribute_texts'        => $attribute_texts,
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

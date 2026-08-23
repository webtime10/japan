<?php
/**
 * Product controller.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Product_Controller extends Map_Plum_Controller {

	/** @var Map_Plum_Product_Model */
	private $model;

	/** @var Map_Plum_Language_Model */
	private $lang_model;

	/** @var Map_Plum_Manufacturer_Model */
	private $manufacturer_model;

	/** @var Map_Plum_Category_Model */
	private $category_model;

	/** @var Map_Plum_Marker_Model */
	private $marker_model;

	public function __construct( $route = 'product' ) {
		parent::__construct( $route );
		$this->model              = new Map_Plum_Product_Model();
		$this->lang_model         = new Map_Plum_Language_Model();
		$this->manufacturer_model = new Map_Plum_Manufacturer_Model();
		$this->category_model     = new Map_Plum_Category_Model();
		$this->marker_model       = new Map_Plum_Marker_Model();
	}

	public function index() {
		$lang_id                = $this->lang_model->get_default_language_id();
		$filter_manufacturer_id = 0;
		$filter_category_id     = 0;

		if ( isset( $_GET['filter_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$filter_manufacturer_id = isset( $_GET['manufacturer_id'] ) ? (int) $_GET['manufacturer_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$filter_category_id     = isset( $_GET['category_id'] ) ? (int) $_GET['category_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$manufacturer_options = array( 0 => 'Все регионы' );
		foreach ( $this->manufacturer_model->get_list( $lang_id ) as $manufacturer ) {
			$manufacturer_options[ (int) $manufacturer->manufacturer_id ] = $manufacturer->name
				? $manufacturer->name
				: '#' . $manufacturer->manufacturer_id;
		}

		$category_options = array( 0 => 'Все категории' );
		foreach ( $this->category_model->get_list( $lang_id ) as $category ) {
			$category_options[ (int) $category->category_id ] = $category->name
				? $category->name
				: '#' . $category->category_id;
		}

		$add_url = Map_Plum_Router::url( 'product', 'add' );
		$header_buttons  = '<button type="submit" form="map-plum-list-product" class="btn btn-danger" onclick="return confirm(\'Удалить выбранные округа?\');"><i class="fa fa-trash-o"></i> Удалить</button>';
		$header_buttons .= '<a href="' . esc_url( $add_url ) . '" class="button button-primary">Добавить округ</a>';
		$this->render(
			'product/list',
			array(
				'title'                  => 'Округа',
				'heading_title'          => 'Список округов',
				'items'                  => $this->model->get_list( $lang_id, $filter_manufacturer_id, $filter_category_id ),
				'manufacturer_options'   => $manufacturer_options,
				'category_options'       => $category_options,
				'filter_manufacturer_id' => $filter_manufacturer_id,
				'filter_category_id'     => $filter_category_id,
				'filters_active'         => isset( $_GET['filter_action'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'header_buttons'         => $header_buttons,
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

		$this->verify_nonce( 'map_plum_product_save' );
		$id = isset( $_POST['product_id'] ) ? (int) $_POST['product_id'] : 0;

		$main = array(
			'manufacturer_id' => isset( $_POST['manufacturer_id'] ) ? (int) $_POST['manufacturer_id'] : 0,
			'model'           => '',
			'sku'             => '',
			'image'           => '',
			'image_id'        => 0,
			'polylink'        => $this->sanitize_optional_link( isset( $_POST['polylink'] ) ? wp_unslash( $_POST['polylink'] ) : '' ),
			'price'           => 0,
			'quantity'        => 0,
			'sort_order'      => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'          => isset( $_POST['status'] ) ? 1 : 0,
		);
		$image_data     = $this->resolve_product_image_data();
		$main['image']  = $image_data['url'];
		$main['image_id'] = $image_data['id'];

		$languages     = $this->lang_model->get_all_active();
		$descriptions  = $this->parse_descriptions( $languages, isset( $_POST['description'] ) ? $_POST['description'] : array() );
		$category_ids = array_values(
			array_filter(
				array_map( 'intval', (array) ( isset( $_POST['product_category'] ) ? $_POST['product_category'] : array() ) ),
				static function ( $id ) {
					return $id > 0;
				}
			)
		);
		$marker_ids = array_values(
			array_filter(
				array_map( 'intval', (array) ( isset( $_POST['product_marker'] ) ? $_POST['product_marker'] : array() ) ),
				static function ( $id ) {
					return $id > 0;
				}
			)
		);
		$errors       = array();

		if ( (int) $main['manufacturer_id'] <= 0 ) {
			$errors[] = 'Выберите регион.';
		}

		foreach ( $languages as $language ) {
			$lid      = (int) $language->language_id;
			$langName = isset( $language->name ) ? (string) $language->name : (string) $lid;
			$title    = isset( $descriptions[ $lid ]['name'] ) ? trim( (string) $descriptions[ $lid ]['name'] ) : '';
			if ( '' === $title ) {
				$errors[] = 'Заполните название округа на языке: ' . $langName . '.';
			}
		}

		if ( empty( $category_ids ) ) {
			$errors[] = 'Выберите хотя бы одну категорию.';
		}

		if ( empty( $marker_ids ) ) {
			$errors[] = 'Выберите хотя бы один маркер.';
		}

		if ( ! empty( $errors ) ) {
			$this->stash_form_input(
				$id,
				$main,
				$descriptions,
				array(
					'product_categories' => $category_ids,
					'marker_ids'         => $marker_ids,
				)
			);
			$this->set_flash( 'error', $errors );
			if ( $id > 0 ) {
				$this->redirect( 'edit', $id );
			}
			$this->redirect( 'add' );
		}

		$this->clear_stashed_form_input();

		if ( $id > 0 ) {
			$this->model->edit( $id, $main, $descriptions, $category_ids );
		} else {
			$id = $this->model->add( $main, $descriptions, $category_ids );
		}

		$this->model->save_markers( $id, $marker_ids );

		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$this->verify_nonce( 'map_plum_bulk_action' );
		$ids = isset( $_POST['selected'] ) ? array_map( 'intval', (array) $_POST['selected'] ) : array();
		if ( ! empty( $ids ) ) {
			$this->model->delete( $ids );
		}
		$this->redirect_to_list( 'saved' );
	}

	/**
	 * @param string $message
	 */
	private function redirect_to_list( $message = '' ) {
		$args = array( 'filter_action' => '1' );
		if ( isset( $_POST['return_manufacturer_id'] ) && (int) $_POST['return_manufacturer_id'] > 0 ) {
			$args['manufacturer_id'] = (int) $_POST['return_manufacturer_id'];
		}
		if ( isset( $_POST['return_category_id'] ) && (int) $_POST['return_category_id'] > 0 ) {
			$args['category_id'] = (int) $_POST['return_category_id'];
		}

		$url = add_query_arg( $args, Map_Plum_Router::url( 'product' ) );
		if ( $message ) {
			$url = add_query_arg( 'message', $message, $url );
		}

		if ( ! headers_sent() ) {
			wp_safe_redirect( $url );
			exit;
		}

		$escaped_url = esc_url( $url );
		echo '<!DOCTYPE html><html><head><meta charset="utf-8">';
		echo '<meta http-equiv="refresh" content="0;url=' . esc_attr( $escaped_url ) . '">';
		echo '<script>window.location.replace(' . wp_json_encode( $url ) . ');</script>';
		echo '</head><body><p><a href="' . esc_attr( $escaped_url ) . '">Продолжить</a></p></body></html>';
		exit;
	}

	/**
	 * @param int $id
	 */
	private function form( $id ) {
		$languages          = $this->lang_model->get_all_active_sorted();
		$lang_id            = $this->lang_model->get_default_language_id();
		$item               = null;
		$desc               = array();
		$cat_ids            = array();
		$selected_markers   = array();
		$stash_extra        = array();

		if ( $id > 0 ) {
			$item = $this->model->get_product( $id );
			if ( ! $item ) {
				wp_die( 'Округ не найден.' );
			}
			$desc              = $this->model->get_descriptions( $id );
			$cat_ids           = $this->model->get_category_ids( $id );
			$selected_markers  = $this->marker_model->get_by_product( $id, $lang_id );
		}

		list( $item, $desc, $stash_extra ) = $this->merge_stashed_form_input( $id, $item, $desc );

		if ( array_key_exists( 'product_categories', $stash_extra ) ) {
			$cat_ids = array_map( 'intval', (array) $stash_extra['product_categories'] );
		}

		if ( array_key_exists( 'marker_ids', $stash_extra ) ) {
			$selected_markers = $this->marker_model->get_by_ids( $stash_extra['marker_ids'], $lang_id );
		}

		$manufacturers = $this->manufacturer_model->get_list( $lang_id );
		$man_options = array( 0 => '— не выбран —' );
		foreach ( $manufacturers as $m ) {
			$man_options[ (int) $m->manufacturer_id ] = $m->name ? $m->name : '#' . $m->manufacturer_id;
		}

		$header_buttons = '<button type="submit" form="map-plum-form-product" class="btn btn-primary"><i class="fa fa-save"></i> Сохранить</button>';
		$this->render(
			'product/form',
			array(
				'title'                => $id ? 'Редактирование округа' : 'Добавление округа',
				'heading_title'        => $id ? 'Редактирование округа' : 'Добавление округа',
				'item'                 => $item,
				'descriptions'         => $desc,
				'languages'            => $languages,
				'product_id'           => $id,
				'manufacturer_options' => $man_options,
				'category_list'        => $this->category_model->get_list( $lang_id ),
				'product_categories'   => $cat_ids,
				'all_markers'          => $this->marker_model->get_all_for_select( $lang_id ),
				'selected_markers'     => $selected_markers,
				'header_buttons'       => $header_buttons,
			)
		);
	}

	/**
	 * URL изображения: загрузка файла, медиатека или сохранённое значение.
	 *
	 * @return string
	 */
	private function resolve_product_image_data() {
		$current_id  = isset( $_POST['image_attachment_id'] ) ? (int) $_POST['image_attachment_id'] : 0;
		$current_url = isset( $_POST['image'] ) ? esc_url_raw( wp_unslash( $_POST['image'] ) ) : '';

		if ( ! empty( $_FILES['product_image_file']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$attachment_id = media_handle_upload( 'product_image_file', 0 );
			if ( ! is_wp_error( $attachment_id ) ) {
				$url = wp_get_attachment_url( $attachment_id );
				if ( $url ) {
					return array(
						'url' => esc_url_raw( $url ),
						'id'  => (int) $attachment_id,
					);
				}
			}
		}

		if ( $current_id > 0 ) {
			$url = wp_get_attachment_url( $current_id );
			if ( $url ) {
				return array(
					'url' => esc_url_raw( $url ),
					'id'  => $current_id,
				);
			}
		}

		return array(
			'url' => $current_url,
			'id'  => 0,
		);
	}
}

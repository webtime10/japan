<?php
/**
 * Marker controller.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Marker_Controller extends Map_Plum_Controller {

	/** @var Map_Plum_Marker_Model */
	private $model;

	/** @var Map_Plum_Language_Model */
	private $lang_model;

	/** @var Map_Plum_Manufacturer_Model */
	private $manufacturer_model;

	/** @var Map_Plum_Category_Model */
	private $category_model;

	public function __construct( $route = 'marker' ) {
		parent::__construct( $route );
		$this->model              = new Map_Plum_Marker_Model();
		$this->lang_model         = new Map_Plum_Language_Model();
		$this->manufacturer_model = new Map_Plum_Manufacturer_Model();
		$this->category_model     = new Map_Plum_Category_Model();
	}

	public function index() {
		$lang_id               = $this->lang_model->get_default_language_id();
		$filter_manufacturer_id = isset( $_GET['manufacturer_id'] ) ? (int) $_GET['manufacturer_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$per_page              = 20;
		$paged                 = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$total                 = $this->model->count_list( $filter_manufacturer_id );
		$total_pages           = max( 1, (int) ceil( $total / $per_page ) );

		if ( $paged > $total_pages ) {
			$paged = $total_pages;
		}

		$items = $this->model->get_list_paginated( $lang_id, $filter_manufacturer_id, $paged, $per_page );

		$manufacturer_options = array( 0 => 'Все регионы' );
		foreach ( $this->manufacturer_model->get_list( $lang_id ) as $manufacturer ) {
			$manufacturer_options[ (int) $manufacturer->manufacturer_id ] = $manufacturer->name
				? $manufacturer->name
				: '#' . $manufacturer->manufacturer_id;
		}

		$add_url = Map_Plum_Router::url( 'marker', 'add' );
		$header_buttons  = '<button type="submit" form="map-plum-list-marker" class="btn btn-danger" onclick="return confirm(\'Удалить выбранные маркеры?\');"><i class="fa fa-trash-o"></i> Удалить</button>';
		$header_buttons .= '<a href="' . esc_url( $add_url ) . '" class="button button-primary">Добавить маркер</a>';
		$this->render(
			'marker/list',
			array(
				'title'                  => 'Маркеры',
				'heading_title'          => 'Список маркеров',
				'items'                  => $items,
				'manufacturer_options'   => $manufacturer_options,
				'filter_manufacturer_id' => $filter_manufacturer_id,
				'paged'                  => $paged,
				'per_page'               => $per_page,
				'total'                  => $total,
				'total_pages'            => $total_pages,
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

		$this->verify_nonce( 'map_plum_marker_save' );
		$id = isset( $_POST['marker_id'] ) ? (int) $_POST['marker_id'] : 0;

		$main = array(
			'manufacturer_id' => isset( $_POST['manufacturer_id'] ) ? (int) $_POST['manufacturer_id'] : 0,
			'category_id'     => isset( $_POST['category_id'] ) ? (int) $_POST['category_id'] : 0,
			'coordinates'     => isset( $_POST['coordinates'] ) ? sanitize_text_field( wp_unslash( $_POST['coordinates'] ) ) : '',
			'image'           => '',
			'image_id'        => 0,
			'polylink'        => $this->sanitize_optional_link( isset( $_POST['polylink'] ) ? wp_unslash( $_POST['polylink'] ) : '' ),
			'sort_order'      => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'          => isset( $_POST['status'] ) ? 1 : 0,
		);
		$image_data       = $this->resolve_marker_image_data();
		$main['image']    = $image_data['url'];
		$main['image_id'] = $image_data['id'];

		$languages    = $this->lang_model->get_all_active();
		$descriptions = $this->parse_marker_descriptions( $languages, isset( $_POST['description'] ) ? $_POST['description'] : array() );
		$errors       = array();

		if ( (int) $main['manufacturer_id'] <= 0 ) {
			$errors[] = 'Выберите регион.';
		}

		if ( '' === trim( (string) $main['coordinates'] ) ) {
			$errors[] = 'Заполните координаты маркера.';
		}

		foreach ( $languages as $language ) {
			$lid      = (int) $language->language_id;
			$langName = isset( $language->name ) ? (string) $language->name : (string) $lid;
			$title    = isset( $descriptions[ $lid ]['name'] ) ? trim( (string) $descriptions[ $lid ]['name'] ) : '';
			if ( '' === $title ) {
				$errors[] = 'Заполните название маркера на языке: ' . $langName . '.';
			}
		}

		if ( ! empty( $errors ) ) {
			$this->stash_form_input( $id, $main, $descriptions );
			$this->set_flash( 'error', $errors );
			if ( $id > 0 ) {
				$this->redirect( 'edit', $id );
			}
			$this->redirect( 'add' );
		}

		$this->clear_stashed_form_input();

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
		$this->redirect_to_list( 'saved' );
	}

	/**
	 * @param string $message
	 */
	private function redirect_to_list( $message = '' ) {
		$args = array();
		if ( isset( $_POST['return_manufacturer_id'] ) && (int) $_POST['return_manufacturer_id'] > 0 ) {
			$args['manufacturer_id'] = (int) $_POST['return_manufacturer_id'];
		}
		if ( isset( $_POST['return_paged'] ) && (int) $_POST['return_paged'] > 1 ) {
			$args['paged'] = (int) $_POST['return_paged'];
		}

		$url = Map_Plum_Router::url( 'marker' );
		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}
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
	 * @param array $languages
	 * @param mixed $post_description
	 * @return array<int, array<string, string>>
	 */
	private function parse_marker_descriptions( $languages, $post_description ) {
		$out = array();
		if ( ! is_array( $post_description ) ) {
			return $out;
		}
		foreach ( $languages as $lang ) {
			$lid = (int) $lang->language_id;
			if ( ! isset( $post_description[ $lid ] ) || ! is_array( $post_description[ $lid ] ) ) {
				continue;
			}
			$out[ $lid ] = array(
				'name'        => isset( $post_description[ $lid ]['name'] ) ? sanitize_text_field( wp_unslash( $post_description[ $lid ]['name'] ) ) : '',
				'description' => isset( $post_description[ $lid ]['description'] ) ? wp_kses_post( wp_unslash( $post_description[ $lid ]['description'] ) ) : '',
				'arabic_name' => isset( $post_description[ $lid ]['arabic_name'] ) ? sanitize_text_field( wp_unslash( $post_description[ $lid ]['arabic_name'] ) ) : '',
			);
		}
		return $out;
	}

	/**
	 * @return array{url: string, id: int}
	 */
	private function resolve_marker_image_data() {
		$current_id  = isset( $_POST['image_attachment_id'] ) ? (int) $_POST['image_attachment_id'] : 0;
		$current_url = isset( $_POST['image'] ) ? esc_url_raw( wp_unslash( $_POST['image'] ) ) : '';

		if ( ! empty( $_FILES['marker_image_file']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$attachment_id = media_handle_upload( 'marker_image_file', 0 );
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

	/**
	 * @param int $id
	 */
	private function form( $id ) {
		$languages = $this->lang_model->get_all_active_sorted();
		$lang_id   = $this->lang_model->get_default_language_id();
		$item      = null;
		$desc      = array();

		if ( $id > 0 ) {
			$item = $this->model->get_marker( $id );
			if ( ! $item ) {
				wp_die( 'Маркер не найден.' );
			}
			$desc = $this->model->get_descriptions( $id );
		}

		list( $item, $desc ) = $this->merge_stashed_form_input( $id, $item, $desc );

		$manufacturers = $this->manufacturer_model->get_list( $lang_id );
		$man_options   = array( 0 => '— не выбран —' );
		foreach ( $manufacturers as $manufacturer ) {
			$man_options[ (int) $manufacturer->manufacturer_id ] = $manufacturer->name ? $manufacturer->name : '#' . $manufacturer->manufacturer_id;
		}

		$category_options = array( 0 => '— не выбрана —' );
		foreach ( $this->category_model->get_list( $lang_id ) as $category ) {
			$prefix = $category->parent_id > 0 ? '— ' : '';
			$category_options[ (int) $category->category_id ] = $prefix . ( $category->name ? $category->name : '#' . $category->category_id );
		}

		$header_buttons = '<button type="submit" form="map-plum-form-marker" class="btn btn-primary"><i class="fa fa-save"></i> Сохранить</button>';

		$this->render(
			'marker/form',
			array(
				'title'         => $id ? 'Редактирование маркера' : 'Добавление маркера',
				'heading_title' => $id ? 'Редактирование маркера' : 'Добавление маркера',
				'item'          => $item,
				'descriptions'  => $desc,
				'languages'     => $languages,
				'marker_id'     => $id,
				'manufacturer_options' => $man_options,
				'category_options'     => $category_options,
				'header_buttons' => $header_buttons,
			)
		);
	}
}

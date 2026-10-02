<?php
/**
 * Internal posts controller.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Post_Controller extends FCC_Controller {

	/** @var FCC_Post_Model */
	private $model;

	public function __construct( $route = 'post' ) {
		parent::__construct( $route, '' );
		$this->model = new FCC_Post_Model();
	}

	public function index() {
		$lang_id = fcc_get_default_language_id();
		$page    = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$total   = $this->model->count_list();
		$pages   = max( 1, (int) ceil( $total / FCC_Post_Model::PER_PAGE ) );

		if ( $page > $pages ) {
			$page = $pages;
		}

		$this->render(
			'post/list',
			array(
				'title'          => __( 'Посты', 'family-comfort-calc' ),
				'posts'          => $this->model->get_list( $lang_id, $page ),
				'total'          => $total,
				'page'           => $page,
				'pages'          => $pages,
				'header_buttons' => $this->header_btn_add( $this->route, __( 'Добавить', 'family-comfort-calc' ) ),
			)
		);
	}

	public function form() {
		$id      = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post    = $id > 0 ? $this->model->get( $id ) : null;
		$lang_id = fcc_get_default_language_id();
		$desc    = $id > 0 ? $this->model->get_descriptions( $id ) : array();
		$name    = '';
		$text    = '';

		if ( isset( $desc[ $lang_id ] ) ) {
			$name = (string) $desc[ $lang_id ]->name;
			$text = (string) $desc[ $lang_id ]->description;
		}

		$this->render(
			'post/form',
			array(
				'title'          => $id ? __( 'Редактировать пост', 'family-comfort-calc' ) : __( 'Добавить пост', 'family-comfort-calc' ),
				'post'           => $post,
				'name'           => $name,
				'description'    => $text,
				'header_buttons' => $this->header_btn_save( 'fcc-form-post' ),
			)
		);
	}

	public function save() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'fcc_post_save' );

		$id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;

		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$text = isset( $_POST['description'] ) ? wp_kses_post( wp_unslash( $_POST['description'] ) ) : '';

		if ( '' === $name ) {
			$this->set_flash( 'error', __( 'Введите название.', 'family-comfort-calc' ) );
			$this->redirect( 'form', $id );
		}

		$existing = $id > 0 ? $this->model->get( $id ) : null;

		$direction_id = isset( $_POST['direction_id'] ) ? (int) $_POST['direction_id'] : 0;
		if ( $direction_id <= 0 && $existing ) {
			$direction_id = (int) $existing->direction_id;
		}
		if ( $direction_id <= 0 ) {
			$direction_id = fcc_find_direction_id_by_name( $name );
		}
		if ( $direction_id <= 0 || ! fcc_get_category( 'direction', $direction_id ) ) {
			$this->set_flash( 'error', __( 'Не найден город (направление) с таким названием. Создайте направление или укажите точное имя города.', 'family-comfort-calc' ) );
			$this->redirect( 'form', $id );
		}

		// Возраст / интересы / теги — только со WP-страниц.
		$auto_tags    = fcc_get_wp_page_tags_for_direction( $direction_id );
		$places       = array();
		$age_ids      = array();
		$interest_ids = array();

		foreach ( $auto_tags as $tag ) {
			$places[] = array(
				'label' => $tag['label'],
				'url'   => $tag['url'],
			);
			foreach ( $tag['age_ids'] as $age_id ) {
				$age_id = (int) $age_id;
				if ( $age_id > 0 && ! in_array( $age_id, $age_ids, true ) ) {
					$age_ids[] = $age_id;
				}
			}
			foreach ( $tag['interest_ids'] as $interest_id ) {
				$interest_id = (int) $interest_id;
				if ( $interest_id > 0 && ! in_array( $interest_id, $interest_ids, true ) ) {
					$interest_ids[] = $interest_id;
				}
			}
		}

		$data = array(
			'direction_id' => $direction_id,
			'image'        => isset( $_POST['image'] ) ? esc_url_raw( wp_unslash( (string) $_POST['image'] ) ) : '',
			'url'          => isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( (string) $_POST['url'] ) ) : '',
			'places'       => wp_json_encode( $places ),
			'age_ids'      => wp_json_encode( $age_ids ),
			'interest_ids' => wp_json_encode( $interest_ids ),
			'sort_order'   => isset( $_POST['sort_order'] ) ? (int) $_POST['sort_order'] : 0,
			'status'       => isset( $_POST['status'] ),
		);

		$lang_id = fcc_get_default_language_id();
		$descriptions = array(
			$lang_id => array(
				'name'        => $name,
				'description' => $text,
			),
		);

		$this->model->save( $id, $data, $descriptions );
		if ( $this->db_failed() ) {
			$this->flash_db_error();
			$this->redirect( 'form', $id );
		}

		$this->redirect( 'index', 0, 'saved' );
	}

	public function delete() {
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'fcc_post_delete_' . $id );

		$error = $this->model->delete( $id );
		if ( $error ) {
			$this->set_flash( 'error', $error );
		}

		$this->redirect( 'index' );
	}

	public function bulk_delete() {
		if ( 'POST' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) ) {
			$this->redirect( 'index' );
		}

		$this->verify_nonce( 'fcc_post_bulk_delete' );

		$ids = isset( $_POST['post_ids'] ) ? array_map( 'intval', (array) $_POST['post_ids'] ) : array();
		$ids = array_filter( $ids );

		if ( ! empty( $ids ) ) {
			$this->model->bulk_delete( $ids );
		}

		$this->redirect( 'index' );
	}
}

<?php
/**
 * List table class for reviews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WT_Reviews_List_Table extends WP_List_Table {
	
	public function __construct() {
		parent::__construct( array(
			'singular' => 'review',
			'plural'   => 'reviews',
			'ajax'     => false,
		) );
	}
	
	/**
	 * Get columns
	 */
	public function get_columns() {
		$columns = array(
			'id'            => 'ID',
			'photo'         => 'Photo',
			'name'          => 'Name',
			'actions'       => 'Actions',
		);
		
		return $columns;
	}
	
	/**
	 * Prepare items
	 */
	public function prepare_items() {
		global $wpdb;
		
		
		
		// Проверка подключения к базе
		if ( ! $wpdb ) {
			echo '<div class="notice notice-error"><p>Нет подключения к базе данных!</p></div>';
			$this->items = array();
			$this->set_pagination_args( array(
				'total_items' => 0,
				'per_page'    => 20,
				'total_pages' => 0,
			) );
			return;
		}
		
		// Прямой запрос к таблице wtreviews - БЕЗ пагинации, все данные
		$table_name = 'wtreviews';
		
		// Получаем ВСЕ данные без LIMIT - только отзывы (keywords = '1')
		$query = "SELECT * FROM `{$table_name}` WHERE keywords = '1' ORDER BY news_id DESC";
		$this->items = $wpdb->get_results( $query );
		
		// Если пусто, пробуем без обратных кавычек
		if ( empty( $this->items ) ) {
			$query = "SELECT * FROM {$table_name} WHERE keywords = '1' ORDER BY news_id DESC";
			$this->items = $wpdb->get_results( $query );
		}
		
		// Получаем общее количество
		$total_items = is_array( $this->items ) ? count( $this->items ) : 0;
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $total_items, // Показываем все на одной странице
			'total_pages' => 1,
		) );
	}
	
	/**
	 * Column default
	 */
	public function column_default( $item, $column_name ) {
		if ( ! is_object( $item ) ) {
			return '—';
		}
		
		switch ( $column_name ) {
			case 'id':
				return isset( $item->news_id ) ? $item->news_id : '—';
			case 'photo':
				$site_url = site_url();
				if ( ! empty( $item->reiting ) ) {
					$photo_url = $site_url . '/uploads/' . $item->reiting;
				} else {
					// Используем аватар по умолчанию, если фото нет
					$photo_url = get_template_directory_uri() . '/img/avatar.webp';
				}
				return '<img src="' . esc_url( $photo_url ) . '" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;" />';
			case 'name':
				$name     = isset( $item->name ) ? $item->name : '';
				$email    = isset( $item->email ) ? $item->email : '';
				
				$name_html = ! empty( $name ) ? esc_html( $name ) : '—';
				if ( ! empty( $email ) ) {
					$name_html .= '<br><small style="color: #666;">' . esc_html( $email ) . '</small>';
				}
				
				return $name_html;
			case 'actions':
				$review_id = isset( $item->news_id ) ? $item->news_id : 0;
				if ( $review_id > 0 ) {
					$edit_url = add_query_arg( array(
						'page' => 'wt-reviews-edit',
						'review_id' => $review_id,
					), admin_url( 'admin.php' ) );
					
					return '<a href="' . esc_url( $edit_url ) . '" class="button button-small">Edit</a>';
				}
				return '—';
			default:
				return '—';
		}
	}
	
	/**
	 * No items message
	 */
	public function no_items() {
		global $wpdb;
		
		// Проверка подключения к базе
		if ( ! $wpdb || ! $wpdb->dbh ) {
			echo '<div class="notice notice-error"><p><strong>Ошибка подключения к базе данных!</strong> Нет подключения к базе данных WordPress.</p></div>';
			return;
		}
		
		// Проверка существования таблицы
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE 'wtreviews'" );
		if ( ! $table_exists ) {
			echo '<div class="notice notice-error"><p><strong>Таблица не найдена!</strong> Таблица \"wtreviews\" не существует в базе данных.</p></div>';
			return;
		}
		
		_e( 'No reviews found.', 'wt-reviews' );
	}
}

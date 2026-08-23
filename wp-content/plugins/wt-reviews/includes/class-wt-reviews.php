<?php
/**
 * Main plugin class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WT_Reviews {
	
	private static $table_name = 'wtreviews'; // Своя таблица wtreviews (без префикса WordPress)

	private const SITE_LANGUAGE = 'ar';
	private const BACKGROUND_OPTION = 'wt_reviews_custom_background_image_id';

	/**
	 * Translation helper for the single-language Arabic site.
	 */
	private static function tr( $key, $fallback ) {
		if ( function_exists( 'get_theme_translation' ) ) {
			$value = get_theme_translation( $key );
			if ( is_string( $value ) && $value !== '' && $value !== $key ) {
				return $value;
			}
		}

		$file_lang = function_exists( 'traveliz_language_data_slug' )
			? traveliz_language_data_slug( self::SITE_LANGUAGE )
			: self::SITE_LANGUAGE;
		$file_path = get_template_directory() . '/languages-data/' . $file_lang . '.php';
		if ( file_exists( $file_path ) ) {
			$translations = include $file_path;
			if ( is_array( $translations ) && ! empty( $translations[ $key ] ) && $translations[ $key ] !== $key ) {
				return $translations[ $key ];
			}
		}

		return $fallback;
	}
	
	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Проверяем и добавляем недостающие поля в таблицу
		$this->check_and_add_missing_columns();
		
		// Add admin menu
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		
		// Handle form submissions
		add_action( 'admin_post_wt_reviews_save', array( $this, 'handle_save' ) );
		add_action( 'admin_post_wt_reviews_delete', array( $this, 'handle_delete' ) );
		add_action( 'admin_post_wt_reviews_save_settings', array( $this, 'handle_save_settings' ) );
		
		// Добавляем страницу редактирования
		add_action( 'admin_menu', array( $this, 'add_edit_page' ) );
		
		// Enqueue admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		
		// Enqueue frontend scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ), 25 );
		
		// Register shortcode for displaying reviews list
		add_shortcode( 'wt_reviews', array( $this, 'render_reviews_shortcode' ) );

		// Register shortcode for reviews list + front-end form & avatar upload
		add_shortcode( 'wt_reviews_form', array( $this, 'render_reviews_form_shortcode' ) );
		
		// AJAX handlers for feedback form
		add_action( 'wp_ajax_upload_feedback_image', array( $this, 'handle_upload_feedback_image' ) );
		add_action( 'wp_ajax_nopriv_upload_feedback_image', array( $this, 'handle_upload_feedback_image' ) );
		add_action( 'wp_ajax_submit_feedback_form', array( $this, 'handle_submit_feedback_form' ) );
		add_action( 'wp_ajax_nopriv_submit_feedback_form', array( $this, 'handle_submit_feedback_form' ) );
	}
	
	/**
	 * Check and add missing columns to the table
	 */
	private function check_and_add_missing_columns() {
		global $wpdb;
		$table_name = self::$table_name;
		
		// Проверяем существование таблицы
		$table_exists = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s",
			DB_NAME,
			$table_name
		) );
		
		if ( ! $table_exists ) {
			return; // Таблица не существует, активация создаст её
		}
		
		// Проверяем и добавляем новые поля
		$columns_to_check = array(
			'email'    => "ALTER TABLE `{$table_name}` ADD COLUMN `email` VARCHAR(255) NOT NULL DEFAULT '' AFTER `name`",
			'link'     => "ALTER TABLE `{$table_name}` ADD COLUMN `link` VARCHAR(255) NOT NULL DEFAULT '' AFTER `text`",
			'language' => "ALTER TABLE `{$table_name}` ADD COLUMN `language` VARCHAR(10) NOT NULL DEFAULT 'ar' AFTER `rating`",
			'rating'   => "ALTER TABLE `{$table_name}` ADD COLUMN `rating` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `reiting`",
		);
		
		foreach ( $columns_to_check as $column => $alter_query ) {
			$column_exists = $wpdb->get_results( $wpdb->prepare(
				"SELECT COUNT(*) as cnt FROM information_schema.COLUMNS 
				WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s",
				DB_NAME,
				$table_name,
				$column
			) );
			
			if ( empty( $column_exists ) || ( isset( $column_exists[0] ) && $column_exists[0]->cnt == 0 ) ) {
				$wpdb->query( $alter_query );
			}
		}
	}
	
	/**
	 * Create database table on activation
	 */
	public static function activate() {
		global $wpdb;

		// Используем кастомную таблицу без префикса (как в остальном коде плагина/темы)
		$table_name       = self::$table_name; // 'wtreviews'
		$charset_collate  = $wpdb->get_charset_collate();

		// Проверяем, существует ли таблица
		$table_exists = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s",
			DB_NAME,
			$table_name
		) );

		if ( ! $table_exists ) {
			// Создаем таблицу с полями, которые уже используются в теме и плагине
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$sql = "CREATE TABLE `{$table_name}` (
				`news_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255) NOT NULL DEFAULT '',
				`email` VARCHAR(255) NOT NULL DEFAULT '',
				`text` LONGTEXT NULL,
				`link` VARCHAR(255) NOT NULL DEFAULT '',
				`reiting` VARCHAR(255) NOT NULL DEFAULT '',
				`rating` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`language` VARCHAR(10) NOT NULL DEFAULT 'ar',
				`keywords` VARCHAR(10) NOT NULL DEFAULT '0',
				`date` DATE NULL,
				PRIMARY KEY (`news_id`)
			) $charset_collate;";

			dbDelta( $sql );
		} else {
			// Проверяем и добавляем новые поля, если таблица уже существует
			$columns_to_check = array(
				'email'    => "ALTER TABLE `{$table_name}` ADD COLUMN `email` VARCHAR(255) NOT NULL DEFAULT '' AFTER `name`",
				'link'     => "ALTER TABLE `{$table_name}` ADD COLUMN `link` VARCHAR(255) NOT NULL DEFAULT '' AFTER `text`",
				'language' => "ALTER TABLE `{$table_name}` ADD COLUMN `language` VARCHAR(10) NOT NULL DEFAULT 'ar' AFTER `rating`",
				'rating'   => "ALTER TABLE `{$table_name}` ADD COLUMN `rating` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `reiting`",
			);
			
			foreach ( $columns_to_check as $column => $alter_query ) {
				$column_exists = $wpdb->get_results( $wpdb->prepare(
					"SELECT COUNT(*) as cnt FROM information_schema.COLUMNS 
					WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s",
					DB_NAME,
					$table_name,
					$column
				) );
				
				if ( empty( $column_exists ) || ( isset( $column_exists[0] ) && $column_exists[0]->cnt == 0 ) ) {
					$wpdb->query( $alter_query );
				}
			}
		}
	}
	
	/**
	 * Deactivation hook
	 */
	public static function deactivate() {
		// Cleanup if needed
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			'WT Reviews',
			'WT Reviews',
			'manage_options',
			'wt-reviews',
			array( $this, 'render_list_page' ),
			'dashicons-star-filled',
			30
		);

		add_submenu_page(
			'wt-reviews',
			'Reviews Settings',
			'Settings',
			'manage_options',
			'wt-reviews-settings',
			array( $this, 'render_settings_page' )
		);
	}
	
	/**
	 * Add edit page (hidden submenu)
	 */
	public function add_edit_page() {
		add_submenu_page(
			null, // Скрытая страница
			'Edit Review',
			'Edit Review',
			'manage_options',
			'wt-reviews-edit',
			array( $this, 'render_edit_page' )
		);
	}
	
	/**
	 * Render list page
	 */
	public function render_list_page() {
		global $wpdb;
		
		// Простой запрос к таблице wtreviews - записи с именем (русским ИЛИ английским)
		$table_name = self::$table_name;
		$query = "SELECT * FROM `{$table_name}` WHERE (name != '' AND name IS NOT NULL) OR (name_en != '' AND name_en IS NOT NULL) ORDER BY news_id DESC";
		$reviews = $wpdb->get_results( $query );
		
		// Если пусто, пробуем без обратных кавычек
		if ( empty( $reviews ) ) {
			$query = "SELECT * FROM {$table_name} WHERE (name != '' AND name IS NOT NULL) OR (name_en != '' AND name_en IS NOT NULL) ORDER BY news_id DESC";
			$reviews = $wpdb->get_results( $query );
		}
		
		include WT_REVIEWS_PLUGIN_DIR . 'admin/views/list-page.php';
	}
	
	/**
	 * Render edit page
	 */
	public function render_edit_page() {
		$review_id = isset( $_GET['review_id'] ) ? intval( $_GET['review_id'] ) : 0;
		$review = null;
		
		if ( $review_id > 0 ) {
			$review = $this->get_review( $review_id );
		}
		
		include WT_REVIEWS_PLUGIN_DIR . 'admin/views/edit-page.php';
	}

	/**
	 * Render plugin settings page.
	 */
	public function render_settings_page() {
		$background_image_id  = (int) get_option( self::BACKGROUND_OPTION, 0 );
		$background_image_url = $background_image_id ? wp_get_attachment_image_url( $background_image_id, 'large' ) : '';

		include WT_REVIEWS_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Save plugin settings.
	 */
	public function handle_save_settings() {
		check_admin_referer( 'wt_reviews_save_settings', 'wt_reviews_settings_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}

		$background_image_id = isset( $_POST['background_image_id'] ) ? absint( wp_unslash( $_POST['background_image_id'] ) ) : 0;

		if ( $background_image_id > 0 ) {
			update_option( self::BACKGROUND_OPTION, $background_image_id );
		} else {
			delete_option( self::BACKGROUND_OPTION );
		}

		wp_redirect( add_query_arg( array( 'page' => 'wt-reviews-settings', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}
	
	/**
	 * Handle save action
	 */
	public function handle_save() {
		check_admin_referer( 'wt_reviews_save', 'wt_reviews_nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}
		
		global $wpdb;
		$table_name = self::$table_name;
		
		$review_id    = isset( $_POST['review_id'] ) ? intval( $_POST['review_id'] ) : 0;
		$name         = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$email        = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$text         = isset( $_POST['text'] ) ? wp_kses_post( $_POST['text'] ) : '';
		$language     = self::SITE_LANGUAGE;
		$rating       = isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0;
		$delete_photo = isset( $_POST['delete_photo'] ) && $_POST['delete_photo'] === '1';
		// Ограничиваем рейтинг от 0 до 5
		if ( $rating < 0 || $rating > 5 ) {
			$rating = 0;
		}
		// Status: keywords = '1' опубликовано, '0' не опубликовано. По умолчанию '1'
		$keywords = isset( $_POST['keywords'] ) ? ( $_POST['keywords'] == '1' ? '1' : '0' ) : '1';
		
		// Фото: если отмечено удаление, сбрасываем; иначе берем из формы или сохраняем существующее
		if ( $delete_photo ) {
		$photo_filename = '';
		} else {
			$photo_filename = isset( $_POST['photo_filename'] ) ? sanitize_text_field( $_POST['photo_filename'] ) : '';
		if ( $review_id > 0 ) {
				$existing       = $this->get_review( $review_id );
				$existing_photo = $existing ? $existing->reiting : '';
				// Если из формы не пришло новое имя файла, оставляем старое
				if ( empty( $photo_filename ) ) {
					$photo_filename = $existing_photo;
				}
			}
		}
		
		$data = array(
			'name' => $name,
			'email' => $email,
			'text'     => $text,
			'link'     => '', // ссылка больше не используется
			'reiting'  => $photo_filename,
			'rating'   => $rating,
			'language' => $language,
			'keywords' => $keywords,
		);
		
		if ( $review_id > 0 ) {
			// Update - только обновление существующих записей
			// Форматы: name, email, text, link, reiting, rating, language, keywords
			$wpdb->update(
				$table_name,
				$data,
				array( 'news_id' => $review_id ),
				array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' ),
				array( '%d' )
			);
			$redirect_url = add_query_arg( array( 'page' => 'wt-reviews', 'updated' => '1' ), admin_url( 'admin.php' ) );
		} else {
			// Добавление отключено - редирект на список
			$redirect_url = add_query_arg( array( 'page' => 'wt-reviews' ), admin_url( 'admin.php' ) );
		}
		
		wp_redirect( $redirect_url );
		exit;
	}
	
	/**
	 * Handle delete action
	 */
	public function handle_delete() {
		check_admin_referer( 'wt_reviews_delete', 'wt_reviews_delete_nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'You do not have sufficient permissions to access this page.' );
		}
		
		$review_id = isset( $_GET['review_id'] ) ? intval( $_GET['review_id'] ) : 0;
		
		if ( $review_id > 0 ) {
			global $wpdb;
			$table_name = self::$table_name;
			$wpdb->delete( $table_name, array( 'news_id' => $review_id ), array( '%d' ) );
		}
		
		$redirect_url = add_query_arg( array( 'page' => 'wt-reviews', 'deleted' => '1' ), admin_url( 'admin.php' ) );
		wp_redirect( $redirect_url );
		exit;
	}
	
	/**
	 * Handle photo upload - загружает в папку /uploads/ в корне сайта
	 */
	private function handle_photo_upload() {
		if ( empty( $_FILES['photo_file']['name'] ) ) {
			return '';
		}
		
		$upload_dir = ABSPATH . 'uploads/';
		
		// Создаем папку если не существует
		if ( ! file_exists( $upload_dir ) ) {
			wp_mkdir_p( $upload_dir );
		}
		
		$filename = sanitize_file_name( $_FILES['photo_file']['name'] );
		$target_file = $upload_dir . $filename;
		
		// Проверяем тип файла
		$allowed_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif' );
		$file_type = wp_check_filetype( $filename );
		
		if ( ! in_array( $_FILES['photo_file']['type'], $allowed_types ) ) {
			return '';
		}
		
		// Перемещаем файл
		if ( move_uploaded_file( $_FILES['photo_file']['tmp_name'], $target_file ) ) {
			return $filename; // Возвращаем только имя файла
		}
		
		return '';
	}
	
	/**
	 * Get review by ID
	 */
	public function get_review( $review_id ) {
		global $wpdb;
		$table_name = self::$table_name;
		
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$table_name}` WHERE news_id = %d", $review_id ) );
	}
	
	/**
	 * Get all reviews
	 */
	public function get_reviews( $per_page = 20, $page_number = 1 ) {
		global $wpdb;
		$table_name = self::$table_name; // Таблица без префикса в базе WordPress
		
		$offset = ( $page_number - 1 ) * $per_page;
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table_name ORDER BY news_id DESC LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);
		
		return $results;
	}
	
	/**
	 * Get total count of reviews
	 */
	public function get_reviews_count() {
		global $wpdb;
		$table_name = self::$table_name; // Таблица без префикса в базе WordPress
		
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
	}
	
	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'wt-reviews' ) === false ) {
			return;
		}

		wp_enqueue_media();
		
		wp_enqueue_style(
			'wt-reviews-admin',
			WT_REVIEWS_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			WT_REVIEWS_VERSION
		);
		
		// Подключаем FontAwesome для звезд в админке
		$fontawesome_path = get_template_directory_uri() . '/1/css/fontawesome-all.css';
		if ( file_exists( get_template_directory() . '/1/css/fontawesome-all.css' ) ) {
			wp_enqueue_style(
				'wt-reviews-fontawesome',
				$fontawesome_path,
				array(),
				WT_REVIEWS_VERSION
			);
		}

		// Скрипт админки (удаление фото, без Cropper.js)
		wp_enqueue_script(
			'wt-reviews-admin-js',
			WT_REVIEWS_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			WT_REVIEWS_VERSION,
			true
		);
	}
	
	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		// Подключаем FontAwesome для звезд на фронтенде
		$fontawesome_path = get_template_directory_uri() . '/1/css/fontawesome-all.css';
		if ( file_exists( get_template_directory() . '/1/css/fontawesome-all.css' ) ) {
			wp_enqueue_style(
				'wt-reviews-fontawesome',
				$fontawesome_path,
				array(),
				WT_REVIEWS_VERSION
			);
		}
		
		$translations = array(
			'error_name'               => self::tr( 'feedback_error_name', 'אנא הזן את שמך (לפחות 2 תווים)' ),
			'error_text'               => self::tr( 'feedback_error_text', 'אנא כתוב את המשוב שלך (לפחות 10 תווים)' ),
			'error_invalid_name'       => self::tr( 'feedback_error_invalid_name', 'השם מכיל תווים לא חוקיים' ),
			'error_email'              => self::tr( 'feedback_error_email', 'כתובת האימייל אינה תקינה' ),
			'error_captcha'            => self::tr( 'feedback_error_captcha', 'אנא אשר שאינך רובוט' ),
			// Ошибки загрузки файлов
			'error_security'           => self::tr( 'feedback_error_security', 'שגיאת אבטחה' ),
			'error_upload_file'        => self::tr( 'feedback_error_upload_file', 'שגיאה בהעלאת הקובץ' ),
			'error_invalid_file_type'  => self::tr( 'feedback_error_invalid_file_type', 'סוג קובץ לא חוקי' ),
			'error_save_file'          => self::tr( 'feedback_error_save_file', 'שגיאה בשמירת הקובץ' ),
			'error_crop'               => self::tr( 'feedback_error_crop', 'שגיאה בחיתוך התמונה' ),
			// Общие статусы
			'uploading'                => self::tr( 'feedback_uploading', 'מעלה...' ),
			'sending'                  => self::tr( 'feedback_sending', 'שולח...' ),
		);
		
		// Обрабатываем сообщение об успехе
		$success_msg = self::tr( 'feedback_success', 'תודה, {name}! המשוב שלך נשלח.' );
		if ( strpos( $success_msg, '{name}' ) !== false ) {
			$parts = explode( '{name}', $success_msg );
			$translations['success_prefix'] = $parts[0];
			$translations['success_suffix'] = isset( $parts[1] ) ? $parts[1] : '';
		} else {
			$translations['success_prefix'] = $success_msg;
			$translations['success_suffix'] = '';
		}
		
		// Локализуем скрипт, если он зарегистрирован
		if ( wp_script_is( 'cursor-script', 'registered' ) || wp_script_is( 'cursor-script', 'enqueued' ) ) {
			wp_localize_script( 'cursor-script', 'feedbackAjax', array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'feedback_upload_nonce' ),
				'form_nonce'    => wp_create_nonce( 'feedback_form_nonce' ),
				'translations'  => $translations,
				'current_lang'  => self::SITE_LANGUAGE,
			) );
		}
	}
	
	/**
	 * Get table name
	 */
	public static function get_table_name() {
		return self::$table_name; // Без префикса, так как это кастомная таблица
	}
	
	/**
	 * Get reviews count (static method for list table)
	 */
	public static function get_reviews_count_static() {
		global $wpdb;
		$table_name = self::$table_name; // Таблица без префикса в базе WordPress
		
		// Проверяем существование таблицы
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		if ( ! $table_exists ) {
			return 0;
		}
		
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$table_name`" );
	}
	
	/**
	 * Get reviews (static method for list table)
	 */
	public static function get_reviews_static( $per_page = 20, $page_number = 1 ) {
		global $wpdb;
		$table_name = self::$table_name; // Таблица без префикса в базе WordPress
		
		// Проверяем существование таблицы
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		if ( ! $table_exists ) {
			return array();
		}
		
		$offset = ( $page_number - 1 ) * $per_page;
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM `$table_name` ORDER BY news_id DESC LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);
		
		return $results ? $results : array();
	}
	
	/**
	 * Get photo URL from filename
	 */
	public static function get_photo_url( $filename ) {
		if ( empty( $filename ) ) {
			return '';
		}
		// Фото хранятся в папке /uploads/ в корне сайта
		$site_url = site_url();
		$photo_path = $site_url . '/uploads/' . $filename;
		return $photo_path;
	}
	
	/**
	 * Shortcode callback for displaying reviews block with form and avatar upload
	 *
	 * Usage: [wt_reviews_form]
	 */
	public function render_reviews_form_shortcode( $atts ) {
		$name_placeholder  = esc_attr( self::tr( 'feedback_name_placeholder', 'שם' ) );
		$email_placeholder = esc_attr( self::tr( 'feedback_email_placeholder', 'אימייל' ) );
		$link_placeholder  = esc_attr( self::tr( 'feedback_link_placeholder', 'קישור לדף שלך ברשת החברתית' ) );
		$text_placeholder  = esc_attr( self::tr( 'feedback_text_placeholder', 'המשוב שלך' ) );
		$submit_text       = esc_attr( self::tr( 'feedback_submit', 'שלח' ) );
		$select_photo_text = esc_html( self::tr( 'feedback_select_photo', 'בחר' ) );
		$cancel_text       = esc_html( self::tr( 'feedback_cancel', 'ביטול' ) );
		$crop_upload_text  = esc_html( self::tr( 'feedback_crop_upload', 'חתוך והעלה' ) );
		$rating_title      = esc_html( self::tr( 'feedback_rating_title', 'הדירוג שלך' ) );
		$captcha_text      = esc_html( self::tr( 'feedback_captcha', 'אני לא רובוט' ) );

		// Получаем отзывы для слайдера. Сайт одноязычный, фильтр по языку не нужен.
		global $wpdb;
		$slider_reviews = array();
		$table_name     = self::$table_name;

		// Загружаем опубликованные отзывы
		if ( ! empty( $table_name ) ) {
			$query   = "SELECT * FROM `{$table_name}` WHERE keywords = '1' AND name != '' AND name IS NOT NULL ORDER BY news_id DESC";
			$results = $wpdb->get_results( $query );

			if ( ! empty( $results ) ) {
				foreach ( $results as $item ) {
					$slider_reviews[] = $item;
				}
			}
		}

		// Заголовок и текст кнопки "Отзывы туристов" через ACF (опции), с дефолтами
		$tourist_reviews_title  = 'Отзывы туристов';
		$tourist_reviews_button = 'Оставить ваш отзыв';
		if ( function_exists( 'get_field' ) ) {
			$title_opt = get_field( 'tourist_reviews_title', 'option' );
			if ( ! empty( $title_opt ) ) {
				$tourist_reviews_title = $title_opt;
			}
			$btn_opt = get_field( 'tourist_reviews_button', 'option' );
			if ( ! empty( $btn_opt ) ) {
				$tourist_reviews_button = $btn_opt;
			}
		}

		ob_start();
		?>
		             <section class="reviews-section google treveler reviews2">
                
               <div class="df-reviews2"> 
                <img class="ellipse551" src="<?php echo get_template_directory_uri(); ?>/img/Ellipse551.webp" alt="" />
                <div class="container-3">

               
                  <h2 class="white"><?php echo esc_html( $tourist_reviews_title ); ?></h2>
                 
                    <div class="reviews-container-into caruael_t reviews2_caruael_t googl2">
                        <div class="carousel_m shadow_m">
                            <div class="carousel-wrapper_m">
                                <div class="carousel-items_m caruael_tt">
									<?php if ( ! empty( $slider_reviews ) ) : ?>
										<?php foreach ( $slider_reviews as $item ) : ?>
											<?php
											// Фото
											$photo_url = '';
											if ( ! empty( $item->reiting ) ) {
												$photo_url = site_url( '/uploads/' . esc_attr( $item->reiting ) );
											}
											if ( empty( $photo_url ) ) {
												$photo_url = get_template_directory_uri() . '/img/avatar.webp';
											}

											$review_name   = ! empty( $item->name ) ? $item->name : '';
											$review_text   = ! empty( $item->text ) ? $item->text : '';
											$review_rating = isset( $item->rating ) ? intval( $item->rating ) : 0;
											// Если рейтинг не задан — 5 звезд по умолчанию
											$display_rating = ( $review_rating > 0 ) ? $review_rating : 5;
											?>
											<div class="carousel-block_m">
												<div class="gogle3">
													<div class="image-rew2 googl3">
														<div class="foto-otzv">
															<img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $review_name ); ?>">
															<?php if ( ! empty( $review_name ) ) : ?>
																<span class="mg"><?php echo esc_html( $review_name ); ?></span>
															<?php endif; ?>
														</div>
														<div class="star-2 review-rating-display">
															<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
																<span class="star <?php echo $i <= $display_rating ? 'star-filled' : 'star-empty'; ?>">
																	<?php echo $i <= $display_rating ? '★' : '☆'; ?>
																</span>
															<?php endfor; ?>
														</div>
													</div>
													<?php if ( ! empty( $review_text ) ) : ?>
														<div class="sity-t">
															<?php echo wp_kses_post( $review_text ); ?>
														</div>
													<?php endif; ?>
												</div>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
                                </div>
                            </div>
                            <div class="wrap-dots-wra">
                                <div class="carousel-button-left_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-l.webp" alt=""></a></div>
                                <div class="carousel-button-right_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-r.webp" alt=""></a></div>
                            </div>
                        </div>
                        <div class="wrap-order-t">
                            <a class="order-mr gogle-button rew_click" href="#"> <span><?php echo esc_html( $tourist_reviews_button ); ?></span></a>
                        </div>
                    </div>

                    

                </div>
                </div>
            </section>
		<div class="reviews_form">
		
             <div class="container otziv-9 modul_2">
				<div class="z-z-z">
					<div class="z-image">
						<div class="nikolaev">
							<div class="f471">
								<div class="filesupload">
									<img class="d44" src="<?php echo esc_url( get_template_directory_uri() . '/img/avatar.webp' ); ?>" alt="avatar" />
								</div>
								<div class="open-cropper-modal-btn" id="open-cropper-btn"><?php echo $select_photo_text; ?></div>
								<div class="vibrat"></div>
							</div>
						</div>
					</div>

					<div id="ex1" class="z-form">
						<form class="my-form">
						<div class="review_stars_wrap">
							<h3 class="rating-title"><?php echo $rating_title; ?></h3>
		<div id="review_stars">
		    <input id="star-4" type="radio" name="stars" value="5"/>
		    <label title="5" for="star-4">
		    	<i class="fas fa-star"></i>
		    </label>
		    <input id="star-3" type="radio" name="stars" value="4"/>
		    <label title="4" for="star-3">
		    	<i class="fas fa-star"></i>
		    </label>
		    <input id="star-2" type="radio" name="stars" value="3"/>
		    <label title="3" for="star-2">
		    	<i class="fas fa-star"></i>
		    </label>
		    <input id="star-1" type="radio" name="stars" value="2"/>
		    <label title="2" for="star-1">
		    	<i class="fas fa-star"></i>
		    </label>
		    <input id="star-0" type="radio" name="stars" value="1"/>
		    <label title="1" for="star-0">
		    	<i class="fas fa-star"></i>
		    </label>
		</div>
	</div>
							<input class="title tname" placeholder="<?php echo $name_placeholder; ?>" name="name" type="text" required>
							<input class="title temail" placeholder="<?php echo $email_placeholder; ?>" name="email" type="email">
							<input class="ss tlink" placeholder="<?php echo $link_placeholder; ?>" name="link" type="url">
							<textarea placeholder="<?php echo $text_placeholder; ?>" cols="30" rows="10" class="textaraa" name="text" required></textarea>
							<div class="captcha-wrapper">
								<label>
									<input type="checkbox" name="captcha" required>
									<span><?php echo $captcha_text; ?></span>
								</label>
							</div>
							<input type="submit" class="submit" value="<?php echo $submit_text; ?>">
						</form>
					</div>
				</div>
			</div>
		</div>
		
		
		<div id="cropper-modal" class="cropper-modal-hidden">
			<div class="cropper-modal-content">
				<button id="cancel-crop-btn" class="cancel-crop-btn" aria-label="<?php echo esc_attr( $cancel_text ); ?>">
					<span class="close-icon">×</span>
				</button>
				<div class="cropper-file-select">
					<input type="file" id="file-input-in-modal" accept="image/*">
					<label for="file-input-in-modal" class="custom-file-button">
						<span class="file-button-icon">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M21 19V5C21 3.9 20.1 3 19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19ZM8.5 13.5L11 16.51L14.5 12L19 18H5L8.5 13.5Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="file-button-text"><?php echo $select_photo_text; ?></span>
					</label>
				</div>
				<div class="cropper-image-container">
					<img id="cropper-image" src="" class="cropper-image" alt="">
				</div>
				<div class="cropper-actions">
					<button id="crop-upload-btn" class="crop-upload-btn"><?php echo $crop_upload_text; ?></button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	

	public function render_reviews_shortcode( $atts ) {
		global $wpdb;
		
		// Получаем опубликованные отзывы (keywords = '1')
		$table_name = self::$table_name;
		
		// Проверяем существование таблицы (проверяем без префикса, так как это кастомная таблица)
		$db_name = DB_NAME;
		$table_exists = $wpdb->get_var( $wpdb->prepare( 
			"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s",
			$db_name,
			$table_name
		) );
		
		if ( ! $table_exists ) {
			// Пробуем альтернативный способ проверки
			$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
		}
		
		if ( ! $table_exists ) {
			// Таблица не существует - возвращаем пустую строку
			return '';
		}
		
		// Запрос к таблице для получения опубликованных отзывов
		$query = "SELECT * FROM `{$table_name}` WHERE keywords = '1' AND name != '' AND name IS NOT NULL ORDER BY news_id DESC";
		$reviews = $wpdb->get_results( $query );
		
		// Если нет опубликованных отзывов, возвращаем пустую строку
		if ( empty( $reviews ) ) {
			return '';
		}
		
		ob_start();
		?>
		<?php foreach ( $reviews as $item ) : 
			// Формируем полный URL к фото
			$photo_url = '';
			if ( ! empty( $item->reiting ) ) {
				$photo_url = site_url( '/uploads/' . esc_attr( $item->reiting ) );
			}
			
			// Если фото нет, используем аватар по умолчанию
			if ( empty( $photo_url ) ) {
				$photo_url = get_template_directory_uri() . '/img/avatar.webp';
			}
			
			// Используем единые поля
			$review_name   = ! empty( $item->name ) ? $item->name : '';
			$review_text   = ! empty( $item->text ) ? $item->text : '';
			$review_link   = isset( $item->link ) && ! empty( $item->link ) ? $item->link : '';
			$review_rating = isset( $item->rating ) ? intval( $item->rating ) : 0;
			// Если рейтинг не задан (0 или пусто), на фронте показываем 5 звезд по умолчанию
			$display_rating = ( $review_rating > 0 ) ? $review_rating : 5;

		?>
			<div class="otziv">
				<div class="img-otziv">
						<img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $review_name ); ?>" />
					<div class="vibrat2"></div>
				</div>
				
				<div class="text-otziv">
					<?php if ( ! empty( $review_name ) ) : ?>
						<h4><?php echo esc_html( $review_name ); ?></h4>
					<?php endif; ?>
					<div class="review-rating-display">
						<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
							<span class="star <?php echo $i <= $display_rating ? 'star-filled' : 'star-empty'; ?>"><?php echo $i <= $display_rating ? '★' : '☆'; ?></span>
						<?php endfor; ?>
					</div>
					<?php if ( ! empty( $review_text ) ) : ?>
						<div class="mytext"><?php echo wp_kses_post( $review_text ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $review_link ) ) : ?>
						<div class="mylink"><a href="<?php echo esc_url( $review_link ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $review_link ); ?></a></div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * AJAX handler for uploading cropped feedback image
	 */
	public function handle_upload_feedback_image() {
		$get_tr = function( $key, $fallback ) {
			return self::tr( $key, $fallback );
		};

		// Проверка nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'feedback_upload_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => $get_tr( 'feedback_error_security', 'Ошибка безопасности' ),
				)
			);
			return;
		}
		
		// Проверка наличия файла
		if ( ! isset( $_FILES['image'] ) || $_FILES['image']['error'] !== UPLOAD_ERR_OK ) {
			wp_send_json_error(
				array(
					'message' => $get_tr( 'feedback_error_upload_file', 'Ошибка загрузки файла' ),
				)
			);
			return;
		}
		
		$file = $_FILES['image'];
		
		// Проверка типа файла
		$allowed_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif' );
		if ( ! in_array( $file['type'], $allowed_types ) ) {
			wp_send_json_error(
				array(
					'message' => $get_tr( 'feedback_error_invalid_file_type', 'Недопустимый тип файла' ),
				)
			);
			return;
		}
		
		// Путь к папке uploads в корне сайта
		$upload_dir = ABSPATH . 'uploads/';
		
		// Создаем папку если не существует
		if ( ! file_exists( $upload_dir ) ) {
			wp_mkdir_p( $upload_dir );
		}
		
		// Генерируем уникальное имя файла
		$file_extension = pathinfo( $file['name'], PATHINFO_EXTENSION );
		$file_name = 'feedback_' . time() . '_' . wp_generate_password( 8, false ) . '.' . $file_extension;
		$target_file = $upload_dir . $file_name;
		
		// Перемещаем файл
		if ( move_uploaded_file( $file['tmp_name'], $target_file ) ) {
			// Возвращаем URL файла
			$file_url = site_url() . '/uploads/' . $file_name;
			wp_send_json_success( array(
				'url' => $file_url,
				'filename' => $file_name
			) );
		} else {
			wp_send_json_error(
				array(
					'message' => $get_tr( 'feedback_error_save_file', 'Ошибка при сохранении файла' ),
				)
			);
		}
	}
	
	/**
	 * AJAX handler for submitting feedback form
	 */
	public function handle_submit_feedback_form() {
		// Проверка nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'feedback_form_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Ошибка безопасности' ) );
			return;
		}
		
		global $wpdb;
		$table_name = self::$table_name;
		
		// Получаем данные из формы
		$name  = isset( $_POST['name'] ) ? trim( sanitize_text_field( $_POST['name'] ) ) : '';
		$email = isset( $_POST['email'] ) ? trim( sanitize_email( $_POST['email'] ) ) : '';
		$text = isset( $_POST['text'] ) ? trim( wp_kses_post( $_POST['text'] ) ) : '';
		$photo_filename = isset( $_POST['photo_filename'] ) ? sanitize_text_field( $_POST['photo_filename'] ) : '';
		$captcha = isset( $_POST['captcha'] ) && $_POST['captcha'] == 'on';
		$rating = isset( $_POST['rating'] ) ? intval( $_POST['rating'] ) : 0;
		// Ограничиваем рейтинг от 0 до 5
		if ( $rating < 0 || $rating > 5 ) {
			$rating = 0;
		}
		
		$get_tr = function( $key, $fallback ) {
			return self::tr( $key, $fallback );
		};
		
		// Проверка капчи
		if ( ! $captcha ) {
			$error_msg = $get_tr( 'feedback_error_captcha', 'Пожалуйста, подтвердите, что вы не робот' );
			wp_send_json_error( array( 'message' => $error_msg ) );
			return;
		}
		
		// Строгая валидация имени - обязательное поле
		if ( empty( $name ) || strlen( $name ) < 2 ) {
			$error_msg = $get_tr( 'feedback_error_name', 'Пожалуйста, укажите имя (минимум 2 символа)' );
			wp_send_json_error( array( 'message' => $error_msg ) );
			return;
		}
		
		// Проверка на валидные символы в имени (любые буквы Unicode, пробелы, дефисы, апострофы, точки)
		// \p{L} - любые буквы (включая иврит, арабский и др.), \p{M} - диакритические знаки
		if ( ! preg_match( '/^[\p{L}\p{M}\s\-\'.]+$/u', $name ) ) {
			$error_msg = $get_tr( 'feedback_error_invalid_name', 'Имя содержит недопустимые символы' );
			wp_send_json_error( array( 'message' => $error_msg ) );
			return;
		}
		
		// Валидация email (если указан)
		if ( ! empty( $email ) && ! is_email( $email ) ) {
			$error_msg = $get_tr( 'feedback_error_email', 'Некорректный email адрес' );
			wp_send_json_error( array( 'message' => $error_msg ) );
			return;
		}
		
		// Валидация текста отзыва
		if ( empty( $text ) || strlen( $text ) < 10 ) {
			$error_msg = $get_tr( 'feedback_error_text', 'Пожалуйста, напишите отзыв (минимум 10 символов)' );
			wp_send_json_error( array( 'message' => $error_msg ) );
			return;
		}
		
		// Подготавливаем данные для вставки (единая структура)
		$data = array(
			'name' => $name,
			'email'    => $email,
			'text'     => $text,
			'link'     => '', // ссылка больше не используется
			'reiting'  => $photo_filename,
			'rating'   => $rating,
			'language' => self::SITE_LANGUAGE,
			'keywords' => '0', // Не опубликовано по умолчанию
			'date'     => current_time( 'Y-m-d' ),
		);
		
		// Вставляем в базу данных
		// Форматы: name, email, text, link, reiting, rating, language, keywords, date
		$formats = array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' );
		$result = $wpdb->insert( $table_name, $data, $formats );
		
		if ( $result === false ) {
			$db_error_msg = $get_tr( 'feedback_error_db', 'Ошибка при сохранении в базу данных' );
			wp_send_json_error(
				array(
					'message' => $db_error_msg . ( ! empty( $wpdb->last_error ) ? ': ' . $wpdb->last_error : '' ),
				)
			);
			return;
		}
		
		// Формируем сообщение об успехе с переводом
		$success_msg = $get_tr( 'feedback_success', 'Спасибо, {name}! Ваш отзыв отправлен на модерацию.' );
		$success_msg = str_replace( '{name}', esc_html( $name ), $success_msg );
		
		wp_send_json_success( array(
			'message' => $success_msg,
			'review_id' => $wpdb->insert_id,
			'saved_name' => $name
		) );
	}
}

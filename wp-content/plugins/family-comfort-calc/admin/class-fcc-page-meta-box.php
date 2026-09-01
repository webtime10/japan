<?php
/**
 * Meta box на страницах (page): категории и теги Family Comfort.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Page_Meta_Box {

	const BOX_ID    = 'family_comfort_calc_page';
	const NONCE_KEY = 'fcc_page_meta_nonce';

	public static function register() {
		$box = new self();
		add_action( 'add_meta_boxes', array( $box, 'add_meta_box' ) );
		add_action( 'save_post_page', array( $box, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $box, 'enqueue_assets' ) );
		add_filter( 'postbox_classes_page_' . self::BOX_ID, array( $box, 'postbox_classes' ) );
	}

	/**
	 * По умолчанию метабокс свёрнут.
	 *
	 * @param array $classes
	 * @return array
	 */
	public function postbox_classes( $classes ) {
		$closed = get_user_option( 'closedpostboxes_page' );
		$should_close = ( false === $closed ) || ( is_array( $closed ) && in_array( self::BOX_ID, $closed, true ) );

		if ( $should_close && ! in_array( 'closed', $classes, true ) ) {
			$classes[] = 'closed';
		}

		return $classes;
	}

	public function add_meta_box() {
		add_meta_box(
			self::BOX_ID,
			__( 'Family Comfort', 'family-comfort-calc' ),
			array( $this, 'render' ),
			'page',
			'normal',
			'default'
		);
	}

	/**
	 * @param WP_Post $post
	 */
	public function render( $post ) {
		wp_nonce_field( 'fcc_page_meta_save', self::NONCE_KEY );

		$selected_ids = fcc_get_page_category_ids( (int) $post->ID );
		$groups         = fcc_get_group_types();
		$tags           = fcc_get_page_tags( (int) $post->ID );
		$max_tags       = fcc_get_page_tags_max();
		$image          = fcc_get_page_image( (int) $post->ID );
		$is_enabled     = fcc_is_page_card_enabled( (int) $post->ID );

		echo '<div class="fcc-page-meta-fields">';

		foreach ( $groups as $group => $label ) {
			$field_id = 'fcc-page-' . $group;
			$selected = isset( $selected_ids[ $group ] ) && is_array( $selected_ids[ $group ] ) ? $selected_ids[ $group ] : array();
			$options  = fcc_get_categories( $group );

			echo '<p class="fcc-page-meta-row fcc-page-meta-row--categories">';
			echo '<span class="fcc-page-meta-label"><strong>' . esc_html( $label ) . '</strong></span>';

			if ( empty( $options ) ) {
				echo '<span class="description">' . esc_html__( 'Категорий пока нет.', 'family-comfort-calc' ) . '</span>';
			} else {
				echo '<div class="fcc-page-meta-checkboxes" id="' . esc_attr( $field_id ) . '-list">';
				foreach ( $options as $cat ) {
					$cat_id = (int) $cat->category_id;
					if ( (int) $cat->status !== 1 && ! in_array( $cat_id, $selected, true ) ) {
						continue;
					}
					$name      = $cat->name ? $cat->name : '#' . $cat_id;
					$input_id  = $field_id . '-' . $cat_id;
					$is_checked = in_array( $cat_id, $selected, true );

					echo '<label class="fcc-page-meta-checkbox" for="' . esc_attr( $input_id ) . '">';
					printf(
						'<input type="checkbox" id="%1$s" name="fcc_page_meta[%2$s][]" value="%3$d" %4$s>',
						esc_attr( $input_id ),
						esc_attr( $group ),
						$cat_id,
						checked( $is_checked, true, false )
					);
					echo '<span>' . esc_html( $name ) . '</span>';
					echo '</label>';
				}
				echo '</div>';

				echo '<p class="fcc-page-meta-select-actions">';
				printf(
					'<button type="button" class="button button-link-delete fcc-page-meta-clear" data-target="%1$s">%2$s</button>',
					esc_attr( $field_id . '-list' ),
					esc_html__( 'Сбросить выбор', 'family-comfort-calc' )
				);
				echo '</p>';
			}

			echo '</p>';
		}

		echo '<p class="description fcc-page-meta-categories-hint">' . esc_html__( 'Можно не выбирать ничего или снять выбор кнопкой «Сбросить выбор». Направления — это город на карточке.', 'family-comfort-calc' ) . '</p>';

		echo '<div class="fcc-page-meta-card-settings">';
		echo '<p class="fcc-page-meta-row"><strong>' . esc_html__( 'Карточка на фронте', 'family-comfort-calc' ) . '</strong></p>';

		echo '<div class="form-group fcc-page-meta-media-field">';
		echo '<label class="fcc-page-meta-label" for="fcc-page-image">' . esc_html__( 'Фото', 'family-comfort-calc' ) . '</label>';
		echo '<div class="fcc-page-meta-media-field__controls">';
		echo '<input type="text" class="regular-text fcc-page-meta-media-input" id="fcc-page-image" name="fcc_page_image" value="' . esc_attr( $image ) . '" placeholder="https://">';
		echo '<button type="button" class="button fcc-page-meta-media-select">' . esc_html__( 'Выбрать', 'family-comfort-calc' ) . '</button>';
		echo '<button type="button" class="button fcc-page-meta-media-clear" ' . ( '' === $image ? 'disabled' : '' ) . ' aria-label="' . esc_attr__( 'Удалить фото', 'family-comfort-calc' ) . '">&times;</button>';
		echo '</div>';
		echo '<div class="fcc-page-meta-media-preview">';
		if ( '' !== $image ) {
			echo '<img src="' . esc_url( $image ) . '" alt="">';
		}
		echo '</div>';
		echo '</div>';

		echo '<p class="fcc-page-meta-row fcc-page-meta-status-row">';
		echo '<label><input type="checkbox" name="fcc_page_status" value="1" ' . checked( $is_enabled, true, false ) . '> ' . esc_html__( 'Статус: включено', 'family-comfort-calc' ) . '</label>';
		echo '<span class="description">' . esc_html__( 'Если выключено — страница не попадает в выдачу калькулятора.', 'family-comfort-calc' ) . '</span>';
		echo '</p>';
		echo '</div>';

		echo '<div class="fcc-page-tags" data-max="' . esc_attr( (string) $max_tags ) . '">';
		echo '<p class="fcc-page-meta-row fcc-page-tags__heading"><strong>' . esc_html__( 'Теги', 'family-comfort-calc' ) . '</strong></p>';

		echo '<div class="fcc-page-tags__list" id="fcc-page-tags-list">';
		foreach ( $tags as $tag ) {
			$this->render_tag_chip( $tag['label'], $tag['url'] );
		}
		echo '</div>';

		echo '<input type="hidden" name="fcc_page_tags_json" id="fcc-page-tags-json" value="' . esc_attr( wp_json_encode( $tags ) ) . '">';

		echo '<div class="fcc-page-tags__add-form" id="fcc-page-tags-add-form" hidden>';
		echo '<div class="fcc-page-tags__inputs">';
		echo '<input type="text" class="regular-text fcc-page-tags__input-label" id="fcc-page-tag-label" placeholder="' . esc_attr__( 'Тег', 'family-comfort-calc' ) . '" autocomplete="off">';
		echo '<input type="url" class="regular-text fcc-page-tags__input-url" id="fcc-page-tag-url" placeholder="' . esc_attr__( 'Ссылка', 'family-comfort-calc' ) . '" autocomplete="off">';
		echo '<button type="button" class="button button-primary fcc-page-tags__btn-ok" id="fcc-page-tag-ok">' . esc_html__( 'OK', 'family-comfort-calc' ) . '</button>';
		echo '<button type="button" class="button fcc-page-tags__btn-cancel" id="fcc-page-tag-cancel">' . esc_html__( 'Отмена', 'family-comfort-calc' ) . '</button>';
		echo '</div>';
		echo '</div>';

		echo '<p class="fcc-page-tags__actions">';
		echo '<button type="button" class="button" id="fcc-page-tag-add">' . esc_html__( 'Добавить тег', 'family-comfort-calc' ) . '</button>';
		echo '<span class="fcc-page-tags__counter" id="fcc-page-tags-counter">' . esc_html( sprintf( /* translators: 1: current count, 2: max count */ __( '%1$d / %2$d', 'family-comfort-calc' ), count( $tags ), $max_tags ) ) . '</span>';
		echo '</p>';

		echo '<p class="description">' . esc_html( sprintf( /* translators: %d: max tags */ __( 'До %d тегов. У каждого тега — надпись и необязательная ссылка.', 'family-comfort-calc' ), $max_tags ) ) . '</p>';
		echo '</div>';

		echo '</div>';
	}

	/**
	 * @param string $label
	 * @param string $url
	 */
	private function render_tag_chip( $label, $url = '' ) {
		echo '<span class="fcc-page-tag" data-label="' . esc_attr( $label ) . '" data-url="' . esc_attr( $url ) . '">';
		echo '<span class="fcc-page-tag__text">' . esc_html( $label ) . '</span>';
		if ( '' !== $url ) {
			echo '<span class="fcc-page-tag__url" title="' . esc_attr( $url ) . '">' . esc_html( $url ) . '</span>';
		}
		echo '<button type="button" class="fcc-page-tag__remove" aria-label="' . esc_attr__( 'Удалить тег', 'family-comfort-calc' ) . '">&times;</button>';
		echo '</span>';
	}

	/**
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public function save( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE_KEY ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_KEY ] ) ), 'fcc_page_meta_save' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		$posted = isset( $_POST['fcc_page_meta'] ) && is_array( $_POST['fcc_page_meta'] )
			? wp_unslash( $_POST['fcc_page_meta'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();

		foreach ( fcc_get_page_meta_keys() as $group => $meta_key ) {
			$raw_ids = array();
			if ( isset( $posted[ $group ] ) ) {
				$raw_ids = is_array( $posted[ $group ] ) ? $posted[ $group ] : array( $posted[ $group ] );
			}

			$valid_ids = fcc_sanitize_page_category_ids( $group, $raw_ids );

			if ( ! empty( $valid_ids ) ) {
				update_post_meta( $post_id, $meta_key, $valid_ids );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}

		$tags_raw = isset( $_POST['fcc_page_tags_json'] ) ? wp_unslash( $_POST['fcc_page_tags_json'] ) : '[]'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$decoded  = json_decode( (string) $tags_raw, true );
		$tags     = fcc_sanitize_page_tags( is_array( $decoded ) ? $decoded : array() );

		if ( ! empty( $tags ) ) {
			update_post_meta( $post_id, fcc_get_page_tags_meta_key(), $tags );
		} else {
			delete_post_meta( $post_id, fcc_get_page_tags_meta_key() );
		}

		$image = isset( $_POST['fcc_page_image'] ) ? esc_url_raw( wp_unslash( (string) $_POST['fcc_page_image'] ) ) : '';
		if ( '' !== $image ) {
			update_post_meta( $post_id, fcc_get_page_image_meta_key(), $image );
		} else {
			delete_post_meta( $post_id, fcc_get_page_image_meta_key() );
		}

		$status = isset( $_POST['fcc_page_status'] ) ? 1 : 0;
		update_post_meta( $post_id, fcc_get_page_status_meta_key(), $status );
	}

	/**
	 * @param string $hook
	 */
	public function enqueue_assets( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'page' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'fcc-page-meta',
			FCC_URL . 'assets/css/admin/page-meta.css',
			array(),
			FCC_VERSION
		);

		$deps = array( 'jquery', 'postbox' );
		if ( wp_script_is( 'my-acf-ai-importer-script', 'registered' ) ) {
			$deps[] = 'my-acf-ai-importer-script';
		}

		$closed_boxes = get_user_option( 'closedpostboxes_page' );
		$is_closed    = ( false === $closed_boxes ) || ( is_array( $closed_boxes ) && in_array( self::BOX_ID, $closed_boxes, true ) );

		wp_enqueue_script(
			'fcc-page-meta',
			FCC_URL . 'assets/js/admin/page-meta.js',
			$deps,
			FCC_VERSION,
			true
		);

		wp_localize_script(
			'fcc-page-meta',
			'fccPageMeta',
			array(
				'boxId'        => self::BOX_ID,
				'isClosed'     => $is_closed,
				'toggleLabel'  => __( 'Свернуть/развернуть Family Comfort', 'family-comfort-calc' ),
				'maxTags'      => fcc_get_page_tags_max(),
				'emptyLabel'   => __( 'Введите текст тега.', 'family-comfort-calc' ),
				'limitReached' => __( 'Можно добавить не больше 10 тегов.', 'family-comfort-calc' ),
				'mediaTitle'   => __( 'Выберите фото', 'family-comfort-calc' ),
				'mediaButton'  => __( 'Использовать', 'family-comfort-calc' ),
			)
		);
	}
}

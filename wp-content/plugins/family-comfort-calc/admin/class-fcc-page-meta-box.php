<?php
/**
 * Meta box на WP-страницах: город (radio), возраст, интересы, название тега.
 * Тег уходит на карточку города в Family Comfort (посты плагина).
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
	 * @param array $classes
	 * @return array
	 */
	public function postbox_classes( $classes ) {
		$closed       = get_user_option( 'closedpostboxes_page' );
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

		$selected = fcc_get_wp_page_category_ids( (int) $post->ID );
		$auto_tag = fcc_get_wp_page_auto_tag( (int) $post->ID );
		$city_id  = ! empty( $selected['direction'][0] ) ? (int) $selected['direction'][0] : 0;
		$groups   = fcc_get_group_types();

		echo '<div class="fcc-wp-page-meta">';
		echo '<p class="description">' . esc_html__( 'Выберите город (один), возраст и интересы. Укажите название тега — он появится на карточке этого города в калькуляторе Family Comfort.', 'family-comfort-calc' ) . '</p>';

		foreach ( $groups as $group => $label ) {
			$field_id = 'fcc-wp-page-' . $group;
			$options  = fcc_get_categories( $group );
			$is_city  = ( 'direction' === $group );
			$picked   = isset( $selected[ $group ] ) ? $selected[ $group ] : array();

			echo '<div class="fcc-page-meta-row fcc-page-meta-row--categories">';
			echo '<p class="fcc-page-meta-label"><strong>' . esc_html( $label ) . '</strong>';
			if ( $is_city ) {
				echo ' <span class="description">(' . esc_html__( 'радио — один город', 'family-comfort-calc' ) . ')</span>';
			}
			echo '</p>';

			if ( empty( $options ) ) {
				echo '<p class="description">' . esc_html__( 'Категорий пока нет. Добавьте их в Family Comfort.', 'family-comfort-calc' ) . '</p>';
				echo '</div>';
				continue;
			}

			$list_class = $is_city ? 'fcc-page-meta-radios' : 'fcc-page-meta-checkboxes';
			echo '<div class="' . esc_attr( $list_class ) . '" id="' . esc_attr( $field_id ) . '-list">';

			foreach ( $options as $cat ) {
				$cat_id = (int) $cat->category_id;
				$active = $is_city ? ( $city_id === $cat_id ) : in_array( $cat_id, $picked, true );

				if ( (int) $cat->status !== 1 && ! $active ) {
					continue;
				}

				$name     = $cat->name ? $cat->name : '#' . $cat_id;
				$input_id = $field_id . '-' . $cat_id;

				if ( $is_city ) {
					echo '<label class="fcc-page-meta-radio" for="' . esc_attr( $input_id ) . '">';
					printf(
						'<input type="radio" id="%1$s" name="fcc_wp_page_meta[direction]" value="%2$d" %3$s>',
						esc_attr( $input_id ),
						$cat_id,
						checked( $city_id === $cat_id, true, false )
					);
					echo '<span>' . esc_html( $name ) . '</span></label>';
				} else {
					echo '<label class="fcc-page-meta-checkbox" for="' . esc_attr( $input_id ) . '">';
					printf(
						'<input type="checkbox" id="%1$s" name="fcc_wp_page_meta[%2$s][]" value="%3$d" %4$s>',
						esc_attr( $input_id ),
						esc_attr( $group ),
						$cat_id,
						checked( $active, true, false )
					);
					echo '<span>' . esc_html( $name ) . '</span></label>';
				}
			}

			echo '</div>';
			echo '<p class="fcc-page-meta-select-actions">';
			printf(
				'<button type="button" class="button button-link-delete fcc-wp-page-meta-clear" data-target="%1$s" data-type="%2$s">%3$s</button>',
				esc_attr( $field_id . '-list' ),
				esc_attr( $is_city ? 'radio' : 'checkbox' ),
				esc_html__( 'Сбросить выбор', 'family-comfort-calc' )
			);
			echo '</p></div>';
		}

		echo '<div class="fcc-page-meta-auto-tag">';
		echo '<p class="fcc-page-meta-label"><label for="fcc-wp-page-auto-tag"><strong>' . esc_html__( 'Название тега', 'family-comfort-calc' ) . '</strong></label></p>';
		echo '<input type="text" class="regular-text" id="fcc-wp-page-auto-tag" name="fcc_wp_page_auto_tag" value="' . esc_attr( $auto_tag ) . '" placeholder="' . esc_attr__( 'например: Рейхстаг', 'family-comfort-calc' ) . '" autocomplete="off">';
		echo '<p class="description">' . esc_html__( 'Ссылка тега = адрес этой страницы. Нужны город + возраст + интересы.', 'family-comfort-calc' ) . '</p>';
		echo '</div>';

		echo '</div>';
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

		$posted = isset( $_POST['fcc_wp_page_meta'] ) && is_array( $_POST['fcc_wp_page_meta'] )
			? wp_unslash( $_POST['fcc_wp_page_meta'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();

		foreach ( fcc_get_wp_page_meta_keys() as $group => $meta_key ) {
			$raw_ids = array();

			if ( 'direction' === $group ) {
				if ( isset( $posted['direction'] ) && '' !== (string) $posted['direction'] ) {
					$raw_ids = array( $posted['direction'] );
				}
			} elseif ( isset( $posted[ $group ] ) ) {
				$raw_ids = is_array( $posted[ $group ] ) ? $posted[ $group ] : array( $posted[ $group ] );
			}

			$valid_ids = fcc_sanitize_page_category_ids( $group, $raw_ids );
			if ( 'direction' === $group && count( $valid_ids ) > 1 ) {
				$valid_ids = array( (int) $valid_ids[0] );
			}

			if ( ! empty( $valid_ids ) ) {
				update_post_meta( $post_id, $meta_key, $valid_ids );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}

		$auto_tag = isset( $_POST['fcc_wp_page_auto_tag'] )
			? sanitize_text_field( wp_unslash( (string) $_POST['fcc_wp_page_auto_tag'] ) )
			: '';

		if ( '' !== $auto_tag ) {
			update_post_meta( $post_id, fcc_get_wp_page_auto_tag_meta_key(), $auto_tag );
		} else {
			delete_post_meta( $post_id, fcc_get_wp_page_auto_tag_meta_key() );
		}
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

		wp_enqueue_style(
			'fcc-wp-page-meta',
			FCC_URL . 'assets/css/admin/page-meta.css',
			array(),
			FCC_VERSION
		);

		wp_enqueue_script(
			'fcc-wp-page-meta',
			FCC_URL . 'assets/js/admin/wp-page-meta.js',
			array( 'jquery', 'postbox' ),
			FCC_VERSION,
			true
		);

		$closed_boxes = get_user_option( 'closedpostboxes_page' );
		$is_closed    = ( false === $closed_boxes ) || ( is_array( $closed_boxes ) && in_array( self::BOX_ID, $closed_boxes, true ) );

		wp_localize_script(
			'fcc-wp-page-meta',
			'fccWpPageMeta',
			array(
				'boxId'       => self::BOX_ID,
				'isClosed'    => $is_closed,
				'toggleLabel' => __( 'Свернуть/развернуть Family Comfort', 'family-comfort-calc' ),
			)
		);
	}
}

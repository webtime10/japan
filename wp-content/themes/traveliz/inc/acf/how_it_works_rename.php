<?php
/**
 * Переименовать Options «How it works» → «Как мы работаем».
 */

add_filter(
	'acf/get_options_pages',
	static function ( $pages ) {
		if ( ! is_array( $pages ) ) {
			return $pages;
		}
		foreach ( $pages as $slug => $page ) {
			if ( 'how_it_works' === $slug || ( is_array( $page ) && isset( $page['menu_slug'] ) && 'how_it_works' === $page['menu_slug'] ) ) {
				$pages[ $slug ]['page_title'] = 'Как мы работаем';
				$pages[ $slug ]['menu_title'] = 'Как мы работаем';
			}
		}
		return $pages;
	},
	20
);

// ACF UI options page post (если создана через интерфейс).
add_action(
	'acf/init',
	static function () {
		global $wpdb;
		if ( ! $wpdb ) {
			return;
		}
		$ids = $wpdb->get_col(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'acf-ui-options-page' AND (post_title = 'How it works' OR post_name LIKE '%how_it_works%' OR post_name LIKE '%how-it-works%')"
		);
		foreach ( $ids as $id ) {
			$id = (int) $id;
			if ( $id <= 0 ) {
				continue;
			}
			$post = get_post( $id );
			if ( ! $post || 'Как мы работаем' === $post->post_title ) {
				continue;
			}
			wp_update_post(
				array(
					'ID'         => $id,
					'post_title' => 'Как мы работаем',
				)
			);
			$settings = maybe_unserialize( $post->post_content );
			if ( is_array( $settings ) ) {
				$settings['page_title'] = 'Как мы работаем';
				$settings['menu_title'] = 'Как мы работаем';
				wp_update_post(
					array(
						'ID'           => $id,
						'post_content' => maybe_serialize( $settings ),
					)
				);
			}
		}
	},
	30
);

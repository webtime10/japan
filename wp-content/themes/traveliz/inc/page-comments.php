<?php
/**
 * Все страницы (post_type page): комментарии открыты на фронте и при сохранении.
 */

add_filter(
	'comments_open',
	static function ( $open, $post_id ) {
		return ( 'page' === get_post_type( $post_id ) ) ? true : $open;
	},
	10,
	2
);

add_filter(
	'wp_insert_post_data',
	static function ( $data, $postarr ) {
		if ( isset( $data['post_type'] ) && 'page' === $data['post_type'] ) {
			$data['comment_status'] = 'open';
		}
		return $data;
	},
	10,
	2
);

add_action(
	'init',
	static function () {
		if ( get_option( 'traveliz_pages_comments_open_v1' ) ) {
			return;
		}

		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} SET comment_status = %s WHERE post_type = %s AND comment_status != %s",
				'open',
				'page',
				'open'
			)
		);

		update_option( 'traveliz_pages_comments_open_v1', 1, false );
	},
	20
);

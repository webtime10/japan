<?php
/**
 * Admin AJAX: search pages for post form.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register AJAX hooks.
 *
 * @return void
 */
function fcc_register_ajax() {
	add_action( 'wp_ajax_fcc_search_city_pages', 'fcc_ajax_search_city_pages' );
	add_action( 'wp_ajax_fcc_search_attraction_pages', 'fcc_ajax_search_attraction_pages' );
	add_action( 'wp_ajax_fcc_get_direction_auto_tags', 'fcc_ajax_get_direction_auto_tags' );
	add_action( 'wp_ajax_fcc_resolve_direction_by_name', 'fcc_ajax_resolve_direction_by_name' );
}

/**
 * City page picker — default template pages.
 *
 * @return void
 */
function fcc_ajax_search_city_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	check_ajax_referer( 'fcc_search_city_pages', 'nonce' );

	$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	if ( strlen( $term ) < fcc_page_search_min_chars() ) {
		wp_send_json_success( array( 'items' => array() ) );
	}

	wp_send_json_success(
		array(
			'items' => fcc_search_default_pages( $term, 20 ),
		)
	);
}

/**
 * Attractions picker — default template pages.
 *
 * @return void
 */
function fcc_ajax_search_attraction_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	check_ajax_referer( 'fcc_search_attraction_pages', 'nonce' );

	$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	if ( strlen( $term ) < fcc_page_search_min_chars() ) {
		wp_send_json_success( array( 'items' => array() ) );
	}

	wp_send_json_success(
		array(
			'items' => fcc_search_default_pages( $term, 20 ),
		)
	);
}

/**
 * Автотеги со страниц для выбранного направления (город).
 *
 * @return void
 */
function fcc_ajax_get_direction_auto_tags() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	check_ajax_referer( 'fcc_direction_auto_tags', 'nonce' );

	$direction_id = isset( $_GET['direction_id'] ) ? (int) $_GET['direction_id'] : 0;
	$tags         = fcc_get_wp_page_tags_for_direction( $direction_id );

	wp_send_json_success(
		array(
			'html'  => fcc_render_auto_tags_html( $tags ),
			'tags'  => array_map(
				static function ( $t ) {
					return array(
						'label' => $t['label'],
						'url'   => $t['url'],
					);
				},
				$tags
			),
			'count' => count( $tags ),
		)
	);
}

/**
 * Найти direction_id по названию города.
 *
 * @return void
 */
function fcc_ajax_resolve_direction_by_name() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	check_ajax_referer( 'fcc_direction_auto_tags', 'nonce' );

	$name = isset( $_GET['name'] ) ? sanitize_text_field( wp_unslash( $_GET['name'] ) ) : '';

	wp_send_json_success(
		array(
			'direction_id' => fcc_find_direction_id_by_name( $name ),
		)
	);
}

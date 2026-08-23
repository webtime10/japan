<?php
/**
 * AJAX админки.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Admin_Ajax {

	public static function register() {
		add_action( 'wp_ajax_ai_calculator_save_remote_site', array( __CLASS__, 'save_remote_site' ) );
		add_action( 'wp_ajax_ai_calculator_search_related_products', array( __CLASS__, 'search_related_products' ) );
		add_action( 'wp_ajax_ai_calculator_update_product_sort_order', array( __CLASS__, 'update_product_sort_order' ) );
		add_action( 'wp_ajax_ai_calculator_update_product_text_field', array( __CLASS__, 'update_product_text_field' ) );
	}

	public static function save_remote_site() {
		check_ajax_referer( 'ai_calculator_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'ai-calculator' ) ), 403 );
		}

		require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';

		$url = isset( $_POST['url'] ) ? AI_Calculator_Settings::sanitize_stored_url( wp_unslash( $_POST['url'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === $url ) {
			wp_send_json_error( array( 'message' => __( 'Enter endpoint (domain or URL).', 'ai-calculator' ) ), 400 );
		}

		if ( ! AI_Calculator_Settings::set_active_url( $url ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not save.', 'ai-calculator' ) ), 500 );
		}

		if ( isset( $_POST['api_key'] ) && ! defined( 'AI_CALCULATOR_LARA_API_KEY' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			AI_Calculator_Settings::set_api_key( wp_unslash( $_POST['api_key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		wp_send_json_success(
			array(
				'message'   => __( 'Saved.', 'ai-calculator' ),
				'activeUrl' => AI_Calculator_Settings::get_active_url(),
			)
		);
	}

	public static function search_related_products() {
		check_ajax_referer( 'ai_calculator_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'ai-calculator' ) ), 403 );
		}

		require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-product-model.php';

		$category_id        = isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$language_id        = isset( $_POST['language_id'] ) ? absint( wp_unslash( $_POST['language_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$product_id         = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$query              = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$exclude_ids_raw    = isset( $_POST['exclude_ids'] ) ? wp_unslash( $_POST['exclude_ids'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$exclude_ids        = is_array( $exclude_ids_raw ) ? array_map( 'intval', $exclude_ids_raw ) : array();

		if ( $category_id <= 0 ) {
			wp_send_json_success( array( 'items' => array() ) );
		}

		$model = new AI_Calculator_Product_Model();
		$items = $model->search_products_in_category(
			$category_id,
			$language_id,
			$query,
			$product_id,
			$exclude_ids
		);

		wp_send_json_success( array( 'items' => $items ) );
	}

	public static function update_product_sort_order() {
		check_ajax_referer( 'ai_calculator_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'ai-calculator' ) ), 403 );
		}

		require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-product-model.php';

		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$sort_order = isset( $_POST['sort_order'] ) ? (int) wp_unslash( $_POST['sort_order'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( $product_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid product.', 'ai-calculator' ) ), 400 );
		}

		$model = new AI_Calculator_Product_Model();
		if ( ! $model->update_sort_order( $product_id, $sort_order ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not save.', 'ai-calculator' ) ), 500 );
		}

		wp_send_json_success(
			array(
				'message'    => __( 'Saved.', 'ai-calculator' ),
				'product_id' => $product_id,
				'sort_order' => $sort_order,
			)
		);
	}

	public static function update_product_text_field() {
		check_ajax_referer( 'ai_calculator_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'ai-calculator' ) ), 403 );
		}

		require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-product-model.php';

		$product_id  = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$language_id = isset( $_POST['language_id'] ) ? absint( wp_unslash( $_POST['language_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$field       = isset( $_POST['field'] ) ? sanitize_key( wp_unslash( $_POST['field'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$value       = isset( $_POST['value'] ) ? wp_unslash( $_POST['value'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( $product_id <= 0 || $language_id <= 0 || ! in_array( $field, array( 'name', 'description' ), true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid product field.', 'ai-calculator' ) ), 400 );
		}

		$value = 'description' === $field
			? wp_kses_post( $value )
			: sanitize_text_field( $value );

		$model = new AI_Calculator_Product_Model();
		if ( ! $model->update_description_field( $product_id, $language_id, $field, $value ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not save.', 'ai-calculator' ) ), 500 );
		}

		wp_send_json_success(
			array(
				'message'     => __( 'Saved.', 'ai-calculator' ),
				'product_id'  => $product_id,
				'language_id' => $language_id,
				'field'       => $field,
				'value'       => wp_strip_all_tags( $value ),
			)
		);
	}
}

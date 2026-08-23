<?php
/**
 * Вывод страницы товара и блока «Рекомендуемые товары».
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Product_View {

	/**
	 * @param int $product_id
	 * @param int $language_id
	 * @return string
	 */
	public static function render( $product_id, $language_id = 0 ) {
		$product_id = (int) $product_id;
		if ( $product_id <= 0 ) {
			return '';
		}

		require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-product-model.php';
		$model = new AI_Calculator_Product_Model();
		$data  = $model->get( $product_id );

		if ( empty( $data['product'] ) || ! (int) $data['product']->status ) {
			return '';
		}

		if ( $language_id <= 0 ) {
			$language_id = self::resolve_language_id( $model );
		}

		$description_row = isset( $data['descriptions'][ $language_id ] ) ? $data['descriptions'][ $language_id ] : null;
		if ( ! $description_row && ! empty( $data['descriptions'] ) ) {
			$description_row = reset( $data['descriptions'] );
		}

		$product_name = $description_row && ! empty( $description_row->name )
			? (string) $description_row->name
			: '#' . $product_id;

		$product_description = $description_row && ! empty( $description_row->description )
			? (string) $description_row->description
			: '';

		$product_blocks = array(
			'block1' => $description_row && isset( $description_row->block1 ) ? (string) $description_row->block1 : '',
			'block2' => $description_row && isset( $description_row->block2 ) ? (string) $description_row->block2 : '',
			'block3' => $description_row && isset( $description_row->block3 ) ? (string) $description_row->block3 : '',
			'block4' => $description_row && isset( $description_row->block4 ) ? (string) $description_row->block4 : '',
			'block5' => $description_row && isset( $description_row->block5 ) ? (string) $description_row->block5 : '',
			'block6' => $description_row && isset( $description_row->block6 ) ? (string) $description_row->block6 : '',
		);

		$product = $data['product'];

		$related_products = $model->getRelatedProducts( $product_id, $language_id );

		ob_start();
		include AI_CALCULATOR_PATH . 'front/product/template-product.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param AI_Calculator_Product_Model $model
	 * @return int
	 */
	private static function resolve_language_id( $model ) {
		return $model->get_catalog_language_id();
	}
}

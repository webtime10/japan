<?php
/**
 * Shortcodes for AI Calculator.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Shortcodes {

	public function register() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
	}

	public function register_shortcodes() {
		add_shortcode( 'ai_calculator_product', array( $this, 'render_product' ) );
		add_shortcode( 'ai_calculator_remote_url', array( $this, 'render_remote_url' ) );
	}

	/**
	 * Страница товара каталога + блок «Рекомендуемые товары».
	 *
	 * @param array|string $atts id — product_id.
	 * @return string
	 */
	public function render_product( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'ai_calculator_product'
		);

		$product_id = (int) $atts['id'];
		if ( $product_id <= 0 && isset( $_GET['ai_product'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product_id = absint( wp_unslash( $_GET['ai_product'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( $product_id <= 0 ) {
			return '';
		}

		AI_Calculator_Front_Assets::require_calculator( 'product' );

		return AI_Calculator_Product_View::render( $product_id );
	}

	/**
	 * Вставка сохранённого URL (AI Calculator → Home).
	 *
	 * @param array|string $atts
	 * @return string
	 */
	public function render_remote_url( $atts ) {
		unset( $atts );
		return esc_url( ai_calculator_remote_url() );
	}
}

$ai_calculator_shortcodes = new AI_Calculator_Shortcodes();
$ai_calculator_shortcodes->register();

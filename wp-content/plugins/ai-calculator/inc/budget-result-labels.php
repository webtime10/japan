<?php
/**
 * Финальный экран калькулятора бюджета — подписи и модальное окно заказа.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Заголовок, текст и картинка модалки (те же ACF, что у popup на сайте).
 *
 * @return array{title: string, text: string, image_url: string, image_alt: string}
 */
function ai_calculator_budget_order_modal_meta() {
	$meta = array(
		'title'      => '',
		'text'       => '',
		'image_url'  => '',
		'image_alt'  => '',
	);

	if ( ! function_exists( 'get_field' ) ) {
		return $meta;
	}

	$title = get_field( 'pop_up_title', 'option' );
	$text  = get_field( 'pop_up_text', 'option' );
	$image = get_field( 'pop_up_img', 'option' );

	if ( is_string( $title ) ) {
		$meta['title'] = $title;
	}
	if ( is_string( $text ) ) {
		$meta['text'] = $text;
	}

	if ( is_array( $image ) ) {
		$meta['image_url'] = isset( $image['url'] ) ? (string) $image['url'] : '';
		$meta['image_alt'] = isset( $image['alt'] ) ? (string) $image['alt'] : '';
	}

	return $meta;
}

/**
 * HTML формы в модалке. Позже — Contact Form 7 через фильтр.
 *
 * @return string
 */
function ai_calculator_budget_order_modal_form_html() {
	$html = apply_filters( 'ai_calculator_budget_order_form_html', '' );

	if ( is_string( $html ) && '' !== trim( $html ) ) {
		return $html;
	}

	$labels = ai_calculator_budget_ui_labels();

	return '<p class="ai-bg__modal-placeholder">' . esc_html( $labels['form_placeholder'] ) . '</p>';
}

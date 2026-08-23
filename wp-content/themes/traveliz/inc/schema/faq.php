<?php
/**
 * FAQ schema collectors.
 *
 * @package traveliz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'traveliz_schema_collect_option_faq' ) ) {
	/**
	 * Build FAQ schema from options FAQ block.
	 *
	 * @return array|null
	 */
	function traveliz_schema_collect_option_faq() {
		$title = get_field( 'title_faq', 'option' );
		$rows  = get_field( 'faq', 'option' );

		if ( empty( $rows ) || ! is_array( $rows ) ) {
			return null;
		}

		$items = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$items[] = array(
				'question' => $row['question'] ?? '',
				'answer'   => $row['answer'] ?? '',
			);
		}

		return traveliz_schema_build_faq_page( $title, $items );
	}
}

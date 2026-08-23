<?php
/**
 * Standard JSON envelope for Laravel consumers.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AI_Calculator_Api_Response {

	/**
	 * @param string               $calculator_slug
	 * @param array<string, mixed> $input
	 * @param array<string, mixed> $data
	 * @return array<string, mixed>
	 */
	public static function wrap( $calculator_slug, array $input, array $data ) {
		return array(
			'calculator'     => sanitize_key( $calculator_slug ),
			'version'        => 1,
			'generated_at'   => gmdate( 'c' ),
			'input'          => $input,
			'data'           => $data,
		);
	}
}

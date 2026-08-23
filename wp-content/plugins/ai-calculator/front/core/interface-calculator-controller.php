<?php
/**
 * Front calculator controller (как в Laravel: точка входа HTTP/REST/шорткод).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface AI_Calculator_Controller_Interface {

	/**
	 * Slug для REST: /wp-json/ai-calculator/v1/{slug}
	 */
	public function slug(): string;

	/**
	 * Параметры запроса (region, month, …).
	 *
	 * @param array<string, mixed> $input
	 */
	public function collect( array $input ): array;

	/**
	 * Готовые данные для шаблона и API (через Service).
	 *
	 * @param array<string, mixed> $input
	 */
	public function run( array $input ): array;

	/**
	 * Тело ответа для Laravel.
	 *
	 * @param array<string, mixed> $data
	 */
	public function to_api( array $data ): array;

	/**
	 * HTML шорткода.
	 *
	 * @param array<string, mixed> $data
	 */
	public function render( array $data ): string;
}

<?php
/**
 * Базовый контроллер: run + обёртка API для Laravel.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class AI_Calculator_Controller_Base implements AI_Calculator_Controller_Interface {

	/**
	 * Полный ответ для REST (Laravel).
	 *
	 * @param array<string, mixed> $input
	 */
	public function handle( array $input ): array {
		$data = $this->run( $input );

		return AI_Calculator_Api_Response::wrap(
			$this->slug(),
			$input,
			$this->to_api( $data )
		);
	}
}

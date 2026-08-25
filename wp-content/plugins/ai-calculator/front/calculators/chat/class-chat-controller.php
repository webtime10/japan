<?php
/**
 * Chat calculator controller (skeleton).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Chat_Controller extends AI_Calculator_Controller_Base {

	public function slug(): string {
		return 'chat';
	}

	/**
	 * @param array<string, mixed> $input
	 */
	public function collect( array $input ): array {
		return array();
	}

	/**
	 * @param array<string, mixed> $input
	 */
	public function run( array $input ): array {
		$labels = function_exists( 'ai_calculator_get_chat_labels' )
			? ai_calculator_get_chat_labels()
			: array(
				'title'   => __( 'Не хотите читать всю статью?', 'ai-calculator' ),
				'summary' => __( 'Получить краткое резюме', 'ai-calculator' ),
				'regions' => __( 'Найти лучшие регионы', 'ai-calculator' ),
				'cost'    => __( 'Узнать стоимость', 'ai-calculator' ),
				'route'   => __( 'Подобрать маршрут', 'ai-calculator' ),
			);

		return array(
			'labels' => $labels,
		);
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function to_api( array $data ): array {
		return $data;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function render( array $data ): string {
		ob_start();
		$ai_chat = $data;
		include AI_CALCULATOR_FRONT_PATH . 'calculators/chat/template.php';
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed>|null $data
	 */
	public function enqueue_assets( $data = null ) {
		wp_enqueue_style(
			'ai_calculator_chat',
			plugins_url( 'assets/css/front/chat-calculator.css', AI_CALCULATOR_FILE ),
			array( 'ai_calculator_style' ),
			AI_CALCULATOR_VERSION . '.chat-1'
		);
	}
}

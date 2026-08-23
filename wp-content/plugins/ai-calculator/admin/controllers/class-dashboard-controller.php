<?php
/**
 * Dashboard controller.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Dashboard_Controller extends AI_Calculator_Controller {

	public function index() {
		$this->render(
			'dashboard/home',
			array(
				'title'         => __( 'Home', 'ai-calculator' ),
				'heading_title' => __( 'AI Calculator', 'ai-calculator' ),
			)
		);
	}
}

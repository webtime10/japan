<?php
/**
 * Admin MVC bootstrap.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once AI_CALCULATOR_PATH . 'admin/core/class-ai-calculator-model.php';
require_once AI_CALCULATOR_PATH . 'admin/core/class-ai-calculator-controller.php';
require_once AI_CALCULATOR_PATH . 'admin/core/class-ai-calculator-router.php';

require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-language-model.php';
require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-category-model.php';
require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-manufacturer-model.php';
require_once AI_CALCULATOR_PATH . 'admin/models/class-ai-calculator-product-model.php';

require_once AI_CALCULATOR_PATH . 'admin/controllers/class-dashboard-controller.php';
require_once AI_CALCULATOR_PATH . 'admin/controllers/class-language-controller.php';
require_once AI_CALCULATOR_PATH . 'admin/controllers/class-category-controller.php';
require_once AI_CALCULATOR_PATH . 'admin/controllers/class-manufacturer-controller.php';
require_once AI_CALCULATOR_PATH . 'admin/controllers/class-product-controller.php';

require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';
require_once AI_CALCULATOR_PATH . 'admin/class-ai-calculator-admin-ajax.php';
AI_Calculator_Admin_Ajax::register();

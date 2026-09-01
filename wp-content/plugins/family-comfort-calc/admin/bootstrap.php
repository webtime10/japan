<?php
/**
 * Admin MVC bootstrap.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once FCC_PATH . 'admin/core/class-fcc-model.php';
require_once FCC_PATH . 'admin/core/class-fcc-controller.php';
require_once FCC_PATH . 'admin/core/class-fcc-router.php';

require_once FCC_PATH . 'admin/models/class-fcc-category-model.php';

require_once FCC_PATH . 'admin/controllers/class-fcc-dashboard-controller.php';
require_once FCC_PATH . 'admin/controllers/class-fcc-category-controller.php';

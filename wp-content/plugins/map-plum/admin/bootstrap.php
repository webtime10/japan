<?php
/**
 * Admin MVC bootstrap.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once MAP_PLUM_PATH . 'admin/core/class-map-plum-model.php';
require_once MAP_PLUM_PATH . 'admin/core/class-map-plum-controller.php';
require_once MAP_PLUM_PATH . 'admin/core/class-map-plum-router.php';

require_once MAP_PLUM_PATH . 'admin/models/class-language-model.php';
require_once MAP_PLUM_PATH . 'admin/models/class-manufacturer-model.php';
require_once MAP_PLUM_PATH . 'admin/models/class-category-model.php';
require_once MAP_PLUM_PATH . 'admin/models/class-product-model.php';
require_once MAP_PLUM_PATH . 'admin/models/class-marker-model.php';

require_once MAP_PLUM_PATH . 'admin/controllers/class-dashboard-controller.php';
require_once MAP_PLUM_PATH . 'admin/controllers/class-language-controller.php';
require_once MAP_PLUM_PATH . 'admin/controllers/class-manufacturer-controller.php';
require_once MAP_PLUM_PATH . 'admin/controllers/class-category-controller.php';
require_once MAP_PLUM_PATH . 'admin/controllers/class-product-controller.php';
require_once MAP_PLUM_PATH . 'admin/controllers/class-marker-controller.php';

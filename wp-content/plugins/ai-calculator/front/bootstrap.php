<?php
/**
 * Front layer bootstrap (calculators, REST API, shortcodes).
 *
 * Структура каждого калькулятора (как в Laravel):
 *   Model      — БД и сырые данные
 *   Service    — бизнес-логика + формат для API
 *   Controller — шорткод, REST, связка Model + Service
 *   register.php — подключение в Manager
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AI_CALCULATOR_FRONT_PATH', AI_CALCULATOR_PATH . 'front/' );

require_once AI_CALCULATOR_FRONT_PATH . 'core/interface-calculator-controller.php';
require_once AI_CALCULATOR_FRONT_PATH . 'core/class-calculator-controller-base.php';
require_once AI_CALCULATOR_FRONT_PATH . 'core/class-calculator-manager.php';
require_once AI_CALCULATOR_FRONT_PATH . 'core/class-api-response.php';
require_once AI_CALCULATOR_FRONT_PATH . 'core/class-rest-router.php';
require_once AI_CALCULATOR_FRONT_PATH . 'core/class-vue3-assets.php';
require_once AI_CALCULATOR_FRONT_PATH . 'core/class-front-assets.php';

require_once AI_CALCULATOR_FRONT_PATH . 'calculators/weather/class-weather-model.php';
require_once AI_CALCULATOR_FRONT_PATH . 'calculators/weather/class-weather-service.php';
require_once AI_CALCULATOR_FRONT_PATH . 'calculators/weather/class-weather-controller.php';
require_once AI_CALCULATOR_FRONT_PATH . 'calculators/weather/register.php';

require_once AI_CALCULATOR_FRONT_PATH . 'calculators/budget/class-budget-model.php';
require_once AI_CALCULATOR_FRONT_PATH . 'calculators/budget/class-budget-service.php';
require_once AI_CALCULATOR_FRONT_PATH . 'calculators/budget/class-budget-controller.php';
require_once AI_CALCULATOR_FRONT_PATH . 'calculators/budget/register.php';

require_once AI_CALCULATOR_FRONT_PATH . 'calculators/family-comfort/class-family-comfort-controller.php';
require_once AI_CALCULATOR_FRONT_PATH . 'calculators/family-comfort/register.php';

require_once AI_CALCULATOR_FRONT_PATH . 'tools/db-dump/class-db-dump-tool.php';

AI_Calculator_Front_Rest_Router::init();
AI_Calculator_Front_Assets::init();

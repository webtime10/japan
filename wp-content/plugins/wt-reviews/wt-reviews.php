<?php
/**
 * Plugin Name: WT Reviews
 * Plugin URI: 
 * Description: Плагин для управления отзывами на арабском сайте
 * Version: 1.0.0
 * Author: 
 * Author URI: 
 * Text Domain: wt-reviews
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define plugin constants
define( 'WT_REVIEWS_VERSION', '1.0.0' );
define( 'WT_REVIEWS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WT_REVIEWS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WT_REVIEWS_PLUGIN_FILE', __FILE__ );

// Include the main plugin class
require_once WT_REVIEWS_PLUGIN_DIR . 'includes/class-wt-reviews.php';

// Initialize the plugin
function wt_reviews_init() {
	global $wt_reviews;
	$wt_reviews = new WT_Reviews();
	$wt_reviews->init();
}
add_action( 'plugins_loaded', 'wt_reviews_init' );

// Activation hook
register_activation_hook( __FILE__, array( 'WT_Reviews', 'activate' ) );

// Deactivation hook
register_deactivation_hook( __FILE__, array( 'WT_Reviews', 'deactivate' ) );

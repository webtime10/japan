<?php
/**
 * Dashboard controller.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map_Plum_Dashboard_Controller extends Map_Plum_Controller {

	public function index() {
		global $wpdb;
		$prefix = $wpdb->prefix . 'map_plum_';

		$stats = array(
			'products'      => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}product" ),
			'categories'    => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}category" ),
			'manufacturers' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}manufacturer" ),
			'markers'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}marker" ),
		);

		$this->render(
			'dashboard/index',
			array(
				'title'      => 'Обзор',
				'stats'      => $stats,
				'shortcodes' => map_plum_get_dashboard_shortcode_rows(),
			)
		);
	}
}

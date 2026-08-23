<?php
/**
 * Base model.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Map_Plum_Model {

	/** @var wpdb */
	protected $wpdb;

	/** @var string */
	protected $prefix;

	public function __construct() {
		global $wpdb;
		$this->wpdb   = $wpdb;
		$this->prefix = $wpdb->prefix . 'map_plum_';
	}

	/**
	 * @param string $name Table suffix without prefix.
	 */
	protected function table( $name ) {
		return $this->prefix . $name;
	}
}

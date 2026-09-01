<?php
/**
 * Base model.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class FCC_Model {

	/** @var wpdb */
	protected $wpdb;

	/** @var string */
	protected $prefix;

	public function __construct() {
		global $wpdb;
		$this->wpdb   = $wpdb;
		$this->prefix = $wpdb->prefix . 'fcc_';
	}

	/**
	 * @param string $name
	 */
	protected function table( $name ) {
		return $this->prefix . $name;
	}
}

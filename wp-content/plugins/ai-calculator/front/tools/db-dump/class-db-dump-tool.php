<?php
/**
 * Debug tool: dump all ai_calculator_* tables (not a Laravel calculator module).
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AI_Calculator_Db_Dump_Tool {

	/** @var wpdb */
	private $wpdb;

	/** @var string */
	private $prefix;

	public function __construct() {
		global $wpdb;
		$this->wpdb   = $wpdb;
		$this->prefix = $wpdb->prefix . 'ai_calculator_';
	}

	/**
	 * @return array<int, string>
	 */
	public function get_tables() {
		$like = $this->wpdb->esc_like( $this->prefix ) . '%';
		$rows = $this->wpdb->get_col(
			$this->wpdb->prepare( 'SHOW TABLES LIKE %s', $like )
		);

		if ( ! is_array( $rows ) ) {
			return array();
		}

		sort( $rows );
		return $rows;
	}

	/**
	 * @param string $table
	 * @return bool
	 */
	private function is_allowed_table( $table ) {
		return is_string( $table ) && strpos( $table, $this->prefix ) === 0;
	}

	/**
	 * @param string $table
	 * @return array<int, array<string, mixed>>
	 */
	public function get_table_rows( $table ) {
		if ( ! $this->is_allowed_table( $table ) ) {
			return array();
		}

		$results = $this->wpdb->get_results(
			"SELECT * FROM `{$table}`", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		);

		return is_array( $results ) ? $results : array();
	}

	/**
	 * @return array<string, array<int, array<string, mixed>>>
	 */
	public function get_dump() {
		$dump = array();
		foreach ( $this->get_tables() as $table ) {
			$dump[ $table ] = $this->get_table_rows( $table );
		}
		return $dump;
	}

	/**
	 * @return array{prefix: string, tables: array<int, array{name: string, short: string, count: int}>}
	 */
	public function get_summary() {
		$tables = array();
		foreach ( $this->get_tables() as $table ) {
			$rows     = $this->get_table_rows( $table );
			$tables[] = array(
				'name'  => $table,
				'short' => str_replace( $this->prefix, '', $table ),
				'count' => count( $rows ),
			);
		}
		return array(
			'prefix' => $this->prefix,
			'tables' => $tables,
		);
	}

	/**
	 * @return string
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<p class="ai-db-dump__denied">' . esc_html__( 'Доступ только для администратора.', 'ai-calculator' ) . '</p>';
		}

		wp_enqueue_style(
			'ai_calculator_db_dump',
			plugins_url( 'assets/css/front/db-dump.css', AI_CALCULATOR_FILE ),
			array(),
			AI_CALCULATOR_VERSION
		);

		$ai_db_dump_summary = $this->get_summary();
		$ai_db_dump_data    = $this->get_dump();

		ob_start();
		include AI_CALCULATOR_FRONT_PATH . 'tools/db-dump/template.php';
		return (string) ob_get_clean();
	}
}

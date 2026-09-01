<?php
/**
 * Front shortcode.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Shortcode {

	const SHORTCODE = 'fcc_family_directions';

	public static function register() {
		$instance = new self();
		add_action( 'init', array( $instance, 'register_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $instance, 'register_assets' ) );
	}

	public function register_shortcode() {
		add_shortcode( self::SHORTCODE, array( $this, 'render' ) );
	}

	public function register_assets() {
		wp_register_style(
			'fcc-directions',
			FCC_URL . 'assets/css/front/fcc-directions.css',
			array(),
			FCC_VERSION
		);

		wp_register_script(
			'fcc-directions',
			FCC_URL . 'assets/js/front/fcc-directions.js',
			array(),
			FCC_VERSION,
			true
		);
	}

	public function render() {
		wp_enqueue_style( 'fcc-directions' );
		wp_enqueue_script( 'fcc-directions' );

		$age_options      = fcc_get_select_options( 'age' );
		$interest_options = fcc_get_select_options( 'interest' );
		$cards            = fcc_get_direction_cards_data();
		$has_data         = ! empty( $age_options ) && ! empty( $interest_options );

		ob_start();
		include FCC_PATH . 'front/template.php';
		return (string) ob_get_clean();
	}
}

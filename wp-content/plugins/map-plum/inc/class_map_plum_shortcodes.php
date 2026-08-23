<?php
/**
 * Шорткоды Map Plum.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once MAP_PLUM_PATH . 'inc/map-plum-maps.php';

class Map_Plum_Shortcodes {

	public function register() {
		add_action( 'init', array( $this, 'register_shortcode' ) );
	}

	public function register_shortcode() {
		foreach ( map_plum_get_all_canton_shortcode_tags() as $tag ) {
			add_shortcode( $tag, array( $this, 'render_canton_map' ) );
		}
		add_shortcode( 'form_map_plum', array( $this, 'form_map_plum' ) );
		add_shortcode( 'form_plum', array( $this, 'form_map_plum' ) );
	}

	/**
	 * Карта префектуры: [tokyo], [kyoto], [jp13] … [height="600"]
	 *
	 * @param array|string $atts
	 * @return string
	 */
	public function render_canton_map( $atts, $content = '', $tag = '' ) {
		$slug = map_plum_normalize_canton_slug( (string) $tag );
		if ( ! map_plum_get_canton_meta( $slug ) ) {
			return '';
		}

		return $this->render_map_shortcode( $slug, $atts, $tag );
	}

	/**
	 * @param string       $slug
	 * @param array|string $atts
	 * @param string       $shortcode
	 * @return string
	 */
	private function render_map_shortcode( $slug, $atts, $shortcode ) {
		$atts = shortcode_atts(
			array(
				'height' => '600',
			),
			$atts,
			$shortcode
		);

		$cfg = map_plum_map_get_config( $slug );
		if ( empty( $cfg ) ) {
			return '';
		}

		map_plum_maps_enqueue_assets( $slug, $cfg );
		return map_plum_map_render_widget( $slug, $cfg, (int) $atts['height'] );
	}

	public function form_map_plum() {
		return '';
	}
}

$map_plum_shortcodes = new Map_Plum_Shortcodes();
$map_plum_shortcodes->register();

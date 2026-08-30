<?php
/**
 * Shared map shortcode engine.
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Иконка маркера на карте (img/marker.svg).
 *
 * @return array{url: string, width: int, height: int}
 */
function map_plum_map_marker_icon_config() {
	return array(
		'url'    => plugins_url( 'img/marker.svg', MAP_PLUM_FILE ),
		'width'  => 32,
		'height' => 40,
	);
}

/**
 * CARTO Basemaps API key (https://carto.com/basemaps/apikey).
 * Source: CARTO_API_KEY in .env, or CARTO_API_KEY / MAP_PLUM_CARTO_API_KEY constants.
 *
 * @return string
 */
function map_plum_get_carto_api_key() {
	if ( defined( 'CARTO_API_KEY' ) && is_string( CARTO_API_KEY ) && '' !== trim( CARTO_API_KEY ) ) {
		return trim( CARTO_API_KEY );
	}

	$sources = array(
		isset( $_ENV['CARTO_API_KEY'] ) ? $_ENV['CARTO_API_KEY'] : null,
		isset( $_SERVER['CARTO_API_KEY'] ) ? $_SERVER['CARTO_API_KEY'] : null,
		getenv( 'CARTO_API_KEY' ),
	);

	foreach ( $sources as $value ) {
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			return trim( $value );
		}
	}

	if ( defined( 'CARTO_API_KEY' ) && is_string( CARTO_API_KEY ) && '' !== trim( CARTO_API_KEY ) ) {
		return trim( CARTO_API_KEY );
	}

	if ( defined( 'MAP_PLUM_CARTO_API_KEY' ) && is_string( MAP_PLUM_CARTO_API_KEY ) && '' !== trim( MAP_PLUM_CARTO_API_KEY ) ) {
		return trim( MAP_PLUM_CARTO_API_KEY );
	}

	return trim( (string) get_option( 'map_plum_carto_api_key', '' ) );
}

/**
 * Leaflet tile URL template for CARTO dark raster basemap (with ?key= when configured).
 *
 * @return string
 */
function map_plum_get_carto_tile_url_template() {
	$base = 'https://{s}.basemaps.cartocdn.com/rastertiles/dark_all/{z}/{x}/{y}{r}.png';
	$key  = map_plum_get_carto_api_key();

	if ( '' === $key ) {
		return $base;
	}

	return $base . '?key=' . rawurlencode( $key );
}

/**
 *
 * @return array{readMore: string, panelEmpty: string, panelClose: string}
 */
function map_plum_get_map_i18n() {
	$defaults = array(
		'readMore'    => 'Read more',
		'panelEmpty'  => 'Select a category and click a marker.',
		'panelClose'  => 'Close',
	);

	if ( function_exists( 'get_theme_translation' ) ) {
		return array(
			'readMore'   => get_theme_translation( 'map_read_more' ),
			'panelEmpty' => get_theme_translation( 'map_panel_empty' ),
			'panelClose' => get_theme_translation( 'map_panel_close' ),
		);
	}

	if ( function_exists( 'pll__' ) ) {
		return array(
			'readMore'   => pll__( 'Read more' ),
			'panelEmpty' => pll__( 'Select a category and click a marker.' ),
			'panelClose' => pll__( 'Close' ),
		);
	}

	return array(
		'readMore'   => __( 'Read more', 'map-plum' ),
		'panelEmpty' => __( 'Select a category and click a marker.', 'map-plum' ),
		'panelClose' => __( 'Close', 'map-plum' ),
	);
}

/**
 * Заголовок плашки на карте (язык страницы через get_theme_translation).
 *
 * @param string              $slug bern|lucerne|luzern
 * @param array<string,mixed> $cfg
 * @return array{title: string, subtitle: string}
 */
function map_plum_get_map_header( $slug, $cfg = array() ) {
	$slug_norm = map_plum_normalize_canton_slug( $slug );
	$meta      = map_plum_get_canton_meta( $slug_norm );
	$title_key = 'map_' . $slug_norm . '_title';
	$sub_key   = 'map_canton_subtitle';

	$fallback_title = isset( $cfg['header_title'] ) ? (string) $cfg['header_title'] : '';
	$fallback_sub   = isset( $cfg['header_subtitle'] ) ? (string) $cfg['header_subtitle'] : '';
	if ( $meta ) {
		if ( '' === $fallback_title && ! empty( $meta['title'] ) ) {
			$fallback_title = (string) $meta['title'];
		}
		if ( '' === $fallback_sub && ! empty( $meta['subtitle'] ) ) {
			$fallback_sub = (string) $meta['subtitle'];
		}
	}
	if ( '' === $fallback_title ) {
		$fallback_title = ucfirst( $slug_norm );
	}

	if ( function_exists( 'get_theme_translation' ) ) {
		$title = get_theme_translation( $title_key );
		$sub   = get_theme_translation( $sub_key );
		if ( $title !== $title_key ) {
			return array(
				'title'    => $title,
				'subtitle' => ( $sub !== $sub_key ) ? $sub : '',
			);
		}
	}

	return array(
		'title'    => $fallback_title,
		'subtitle' => $fallback_sub,
	);
}

/**
 * @return array<string, array<string, string>>
 */
function map_plum_maps_registry() {
	static $maps = null;
	if ( null !== $maps ) {
		return $maps;
	}

	$maps = array();
	foreach ( map_plum_cantons_registry() as $slug => $meta ) {
		$dir = MAP_PLUM_PATH . 'sorts/jp/' . $slug;
		$maps[ $slug ] = array(
			'data'      => $dir . '/' . $slug . '-data.php',
			'districts' => $dir . '/' . $slug . '-districts.json',
		);
		if ( ! empty( $meta['aliases'] ) ) {
			foreach ( $meta['aliases'] as $alias ) {
				$maps[ $alias ] = $maps[ $slug ];
			}
		}
	}

	return $maps;
}

/**
 * @param string $slug
 * @return array<string,mixed>|null
 */
function map_plum_map_get_config( $slug ) {
	static $cache = array();

	$slug = map_plum_normalize_canton_slug( $slug );
	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	$registry = map_plum_maps_registry();
	if ( empty( $registry[ $slug ] ) ) {
		$cache[ $slug ] = null;
		return null;
	}

	$entry = $registry[ $slug ];
	if ( empty( $entry['data'] ) || ! is_readable( $entry['data'] ) ) {
		$cache[ $slug ] = null;
		return null;
	}

	$data = require $entry['data'];
	if ( ! is_array( $data ) ) {
		$cache[ $slug ] = null;
		return null;
	}

	$districts = array(
		'type'     => 'FeatureCollection',
		'features' => array(),
	);
	if ( ! empty( $entry['districts'] ) && is_readable( $entry['districts'] ) ) {
		$json = json_decode( (string) file_get_contents( $entry['districts'] ), true );
		if ( is_array( $json ) ) {
			$districts = $json;
		}
	}

	$data['districts'] = $districts;

	$lang_id = map_plum_get_front_language_id();
	$data['map_categories']    = map_plum_get_map_categories_for_front( $slug, $lang_id );
	$data['markers_by_category'] = map_plum_get_markers_by_category_for_map( $slug, $lang_id );

	$data['map_center']      = isset( $data['map_center'] ) ? (array) $data['map_center'] : array( 36.20, 138.25 );
	$data['map_zoom']        = isset( $data['map_zoom'] ) ? (int) $data['map_zoom'] : 9;
	$data['header_title']    = isset( $data['header_title'] ) ? (string) $data['header_title'] : ucfirst( $slug );
	$data['header_subtitle'] = isset( $data['header_subtitle'] ) ? (string) $data['header_subtitle'] : '';

	$cache[ $slug ] = $data;
	return $data;
}

/**
 * @param string              $slug
 * @param array<string,mixed> $cfg
 */
function map_plum_maps_enqueue_assets( $slug, $cfg ) {
	static $queued = false;

	if ( ! $queued ) {
		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );

		wp_enqueue_style(
			'map-plum-maps',
			plugins_url( 'front/maps/common.css', MAP_PLUM_FILE ),
			array( 'leaflet' ),
			MAP_PLUM_VERSION
		);
		wp_enqueue_script(
			'map-plum-maps',
			plugins_url( 'front/maps/common.js', MAP_PLUM_FILE ),
			array( 'leaflet' ),
			MAP_PLUM_VERSION,
			true
		);

		$queued = true;
	}

	$marker_icon = map_plum_map_marker_icon_config();
	$payload     = array(
		$slug => array(
			'categories'        => isset( $cfg['map_categories'] ) ? $cfg['map_categories'] : array(),
			'markersByCategory' => isset( $cfg['markers_by_category'] ) ? $cfg['markers_by_category'] : array(),
			'fallbackPhotos'    => isset( $cfg['fallback_photos'] ) ? $cfg['fallback_photos'] : array(),
			'districts'         => isset( $cfg['districts'] ) ? $cfg['districts'] : array(),
			'mapCenter'         => isset( $cfg['map_center'] ) ? $cfg['map_center'] : array( 36.20, 138.25 ),
			'mapZoom'           => isset( $cfg['map_zoom'] ) ? (int) $cfg['map_zoom'] : 9,
			'markerIcon'        => $marker_icon,
			'i18n'              => map_plum_get_map_i18n(),
		),
	);

	$inline = 'window.mapPlumMaps = window.mapPlumMaps || {}; Object.assign(window.mapPlumMaps, ' . wp_json_encode( $payload ) . ');';
	wp_add_inline_script( 'map-plum-maps', $inline, 'before' );
}

/**
 * @param string              $slug
 * @param array<string,mixed> $cfg
 * @param int                 $height
 * @return string
 */
function map_plum_map_render_widget( $slug, $cfg, $height ) {
	$height   = max( 200, (int) $height );
	$header   = map_plum_get_map_header( $slug, $cfg );
	$title    = $header['title'];
	$sub      = $header['subtitle'];
	$map_i18n = map_plum_get_map_i18n();

	ob_start();
	?>
	<div class="map-plum-map-wrap" style="<?php echo esc_attr( '--map-height:' . $height . 'px;' ); ?>">
		<div class="map-plum-map-widget" data-map-slug="<?php echo esc_attr( $slug ); ?>">
			<div class="app">
				<div class="map-wrap">
					<div class="map-header">
						<h3><?php echo esc_html( $title ); ?></h3>
						<p><?php echo esc_html( $sub ); ?></p>
					</div>
					<div class="map-toolbar" data-role="toolbar"></div>
					<div class="map-canvas" data-role="map"></div>
				</div>
				<aside class="panel" data-role="panel">
					<div class="panel-empty"><?php echo esc_html( $map_i18n['panelEmpty'] ); ?></div>
					<div class="panel-content">
						<div class="panel-head">
							<button type="button" class="panel-close" data-role="panel-close" aria-label="<?php echo esc_attr( $map_i18n['panelClose'] ); ?>">&times;</button>
							<h2 data-role="panel-title"></h2>
						</div>
						<div class="panel-photo" data-role="panel-photo-wrap" hidden>
							<img data-role="panel-photo" src="" alt="">
						</div>
						<div class="panel-desc" data-role="panel-desc" hidden></div>
						<p class="panel-link-wrap" data-role="panel-link-wrap" hidden>
							<a class="panel-link" data-role="panel-link" href="#" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $map_i18n['readMore'] ); ?></a>
						</p>
					</div>
				</aside>
			</div>
		</div>
	</div>
	<?php
	return (string) ob_get_clean();
}

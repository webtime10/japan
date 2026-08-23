<?php
/**
 * Генерация папок кантонов: data.php, districts.json (упрощённый контур), legacy .php
 *
 * Запуск из корня плагина:
 *   php tools/generate_cantons.php
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

$plugin_root = dirname( __DIR__ );
require_once $plugin_root . '/inc/map-plum-cantons-registry.php';

/**
 * @param array{0: float, 1: float, 2: float, 3: float} $bbox minLat, minLng, maxLat, maxLng
 * @param string                                       $name
 * @return array<string, mixed>
 */
function map_plum_bbox_feature( $bbox, $name ) {
	list( $min_lat, $min_lng, $max_lat, $max_lng ) = $bbox;

	return array(
		'type'       => 'Feature',
		'properties' => array(
			'v_kreis' => $name,
			'name'    => $name,
		),
		'geometry'   => array(
			'type'        => 'Polygon',
			'coordinates' => array(
				array(
					array( $min_lng, $min_lat ),
					array( $max_lng, $min_lat ),
					array( $max_lng, $max_lat ),
					array( $min_lng, $max_lat ),
					array( $min_lng, $min_lat ),
				),
			),
		),
	);
}

$registry = map_plum_cantons_registry();
$created  = 0;
$skipped  = 0;

foreach ( $registry as $slug => $meta ) {
	$dir = $plugin_root . '/sorts/sw/' . $slug;
	if ( ! is_dir( $dir ) ) {
		mkdir( $dir, 0755, true );
		++$created;
	}

	$data_file = $dir . '/' . $slug . '-data.php';
	if ( ! is_file( $data_file ) ) {
		$center = $meta['map_center'];
		$zoom   = (int) $meta['map_zoom'];
		$content = <<<PHP
<?php
/**
 * Настройки карты {$meta['name']} (маркеры и категории — из админки Map Plum).
 *
 * Границы: sorts/sw/{$slug}/{$slug}-districts.json
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'map_center' => array( {$center[0]}, {$center[1]} ),
	'map_zoom'   => {$zoom},
);

PHP;
		file_put_contents( $data_file, $content );
	}

	$legacy = $dir . '/' . $slug . '.php';
	if ( ! is_file( $legacy ) ) {
		$legacy_content = <<<PHP
<?php
/**
 * Legacy include: [{$slug}]
 *
 * @package map-plum
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return require __DIR__ . '/{$slug}-data.php';

PHP;
		file_put_contents( $legacy, $legacy_content );
	}

	$districts_file = $dir . '/' . $slug . '-districts.json';
	$use_placeholder = true;
	if ( is_file( $districts_file ) ) {
		$existing = json_decode( (string) file_get_contents( $districts_file ), true );
		if ( is_array( $existing ) && ! empty( $existing['features'] ) && count( $existing['features'] ) > 1 ) {
			$use_placeholder = false;
			++$skipped;
		}
	}

	if ( $use_placeholder && ! empty( $meta['bbox'] ) ) {
		$collection = array(
			'type'     => 'FeatureCollection',
			'features' => array(
				map_plum_bbox_feature( $meta['bbox'], $meta['name'] ),
			),
		);
		file_put_contents(
			$districts_file,
			json_encode( $collection, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
		);
	}
}

echo "Done. New dirs: {$created}, detailed districts kept: {$skipped}\n";

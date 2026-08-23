<?php
/**
 * Fetch canton Bezirke GeoJSON via geo.admin.ch WMS GetFeatureInfo.
 *
 * Usage: php tools/fetch_bezirke_geojson.php <slug>
 */

$configs = [
	'schwyz' => [
		'bbox'   => [ 46.88, 8.45, 47.18, 8.95 ],
		'points' => [
			[ 47.02, 8.65 ], // Schwyz
			[ 47.13, 8.75 ], // Einsiedeln
			[ 47.00, 8.52 ], // Gersau
			[ 47.20, 8.77 ], // Höfe
			[ 47.08, 8.44 ], // Küssnacht
			[ 47.20, 8.85 ], // March
		],
		'min'    => 6,
	],
	'zurich' => [
		'bbox'   => [ 47.13, 8.38, 47.70, 8.85 ],
		'points' => [
			[ 47.28, 8.48 ],
			[ 47.60, 8.68 ],
			[ 47.52, 8.55 ],
			[ 47.48, 8.53 ],
			[ 47.40, 8.40 ],
			[ 47.30, 8.75 ],
			[ 47.25, 8.55 ],
			[ 47.27, 8.65 ],
			[ 47.38, 8.78 ],
			[ 47.35, 8.72 ],
			[ 47.50, 8.73 ],
			[ 47.37, 8.55 ],
		],
		'min'    => 12,
	],
	'fribourg' => [
		'bbox'   => [ 46.52, 6.70, 47.05, 7.45 ],
		'points' => [
			[ 46.82, 6.94 ], // Broye
			[ 46.69, 6.91 ], // Glâne
			[ 46.62, 7.06 ], // Gruyère
			[ 46.80, 7.15 ], // Sarine
			[ 46.93, 7.12 ], // Lac / See
			[ 46.81, 7.28 ], // Sense
			[ 46.78, 6.89 ], // Veveyse
		],
		'min'    => 7,
	],
	'solothurn' => [
		'bbox'      => [ 47.05, 7.33, 47.50, 8.05 ],
		'id_filter' => [ 1101, 1110 ],
		'points'    => [
			[ 47.17, 7.55 ], // Gäu
			[ 47.31, 7.68 ], // Thal
			[ 47.18, 7.47 ], // Bucheggberg
			[ 47.48, 7.62 ], // Dorneck
			[ 47.32, 7.95 ], // Gösgen
			[ 47.19, 7.39 ], // Wasseramt
			[ 47.33, 7.50 ], // Lebern
			[ 47.35, 7.36 ], // Olten
			[ 47.208, 7.537, [ 47.18, 7.48, 47.24, 7.58 ], 400, 400 ], // Solothurn (small)
			[ 47.40, 7.58 ], // Thierstein
		],
		'min'       => 10,
	],
	'basel-landschaft' => [
		'bbox'   => [ 47.30, 7.50, 47.58, 7.95 ],
		'points' => [
			[ 47.45, 7.66 ], // Arlesheim
			[ 47.42, 7.50 ], // Laufen
			[ 47.48, 7.73 ], // Liestal
			[ 47.46, 7.80 ], // Sissach
			[ 47.38, 7.75 ], // Waldenburg
		],
		'min'    => 5,
	],
];

$slug = isset( $argv[1] ) ? strtolower( $argv[1] ) : '';
if ( $slug === '' || ! isset( $configs[ $slug ] ) ) {
	fwrite( STDERR, 'Usage: php fetch_bezirke_geojson.php ' . implode( '|', array_keys( $configs ) ) . "\n" );
	exit( 1 );
}

$cfg  = $configs[ $slug ];
$out  = dirname( __DIR__ ) . '/sorts/sw/' . $slug . '/' . $slug . '-districts.json';
$bbox = $cfg['bbox'];
$width  = 800;
$height = 600;

$features_by_id = [];
$id_filter      = $cfg['id_filter'] ?? null;

foreach ( $cfg['points'] as $pt ) {
	$lat       = $pt[0];
	$lng       = $pt[1];
	$pt_bbox   = isset( $pt[2] ) && is_array( $pt[2] ) ? $pt[2] : $bbox;
	$pt_width  = isset( $pt[3] ) ? (int) $pt[3] : $width;
	$pt_height = isset( $pt[4] ) ? (int) $pt[4] : $height;

	$j = (int) round( ( $lng - $pt_bbox[1] ) / ( $pt_bbox[3] - $pt_bbox[1] ) * ( $pt_width - 1 ) );
	$i = (int) round( ( $pt_bbox[2] - $lat ) / ( $pt_bbox[2] - $pt_bbox[0] ) * ( $pt_height - 1 ) );

	$url = 'https://wms.geo.admin.ch/?' . http_build_query(
		[
			'SERVICE'      => 'WMS',
			'VERSION'      => '1.3.0',
			'REQUEST'      => 'GetFeatureInfo',
			'LAYERS'       => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
			'QUERY_LAYERS' => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
			'CRS'          => 'EPSG:4326',
			'BBOX'         => implode( ',', $pt_bbox ),
			'WIDTH'        => $pt_width,
			'HEIGHT'       => $pt_height,
			'I'            => max( 0, min( $pt_width - 1, $j ) ),
			'J'            => max( 0, min( $pt_height - 1, $i ) ),
			'INFO_FORMAT'  => 'application/json',
		]
	);

	$ctx = stream_context_create(
		[
			'http' => [
				'timeout' => 60,
				'header'  => "User-Agent: map-plum/1.0\r\n",
			],
		]
	);

	$raw = @file_get_contents( $url, false, $ctx );
	if ( $raw === false ) {
		fwrite( STDERR, "Failed at {$lat},{$lng}\n" );
		continue;
	}

	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) || empty( $data['features'] ) ) {
		fwrite( STDERR, "No features at {$lat},{$lng}\n" );
		continue;
	}

	foreach ( $data['features'] as $feature ) {
		$id   = $feature['properties']['id'] ?? null;
		$name = $feature['properties']['name'] ?? '';
		if ( $id === null || $name === '' ) {
			continue;
		}
		if ( $id_filter ) {
			$num = (int) $id;
			if ( $num < $id_filter[0] || $num > $id_filter[1] ) {
				continue;
			}
		}
		$features_by_id[ (string) $id ] = [
			'type'       => 'Feature',
			'properties' => [
				'v_kreis' => $name,
				'name'    => $name,
				'id'      => $id,
			],
			'geometry'   => $feature['geometry'],
		];
	}

	usleep( 200000 );
}

$min = (int) $cfg['min'];
if ( count( $features_by_id ) < $min ) {
	fwrite( STDERR, "Expected {$min} districts, got " . count( $features_by_id ) . "\n" );
	exit( 1 );
}

$collection = [
	'type'     => 'FeatureCollection',
	'name'     => $slug . '-districts',
	'features' => array_values( $features_by_id ),
];

usort(
	$collection['features'],
	static function ( $a, $b ) {
		return (int) ( $a['properties']['id'] ?? 0 ) <=> (int) ( $b['properties']['id'] ?? 0 );
	}
);

$json = json_encode( $collection, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
if ( $json === false ) {
	fwrite( STDERR, "JSON encode failed\n" );
	exit( 1 );
}

file_put_contents( $out, $json . "\n" );
echo 'Saved ' . count( $features_by_id ) . " districts to {$out}\n";

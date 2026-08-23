<?php
/**
 * Fetch Zürich canton Bezirke GeoJSON via geo.admin.ch WMS GetFeatureInfo.
 * Run: php tools/fetch_zurich_geojson.php
 */

$out = dirname( __DIR__ ) . '/sorts/sw/zurich/zurich-districts.json';

// Sample points inside each Zürich Bezirk (WGS84 lat,lng for BBOX order in WMS 1.3.0).
$sample_points = [
	[ 47.28, 8.48 ], // Affoltern
	[ 47.60, 8.68 ], // Andelfingen
	[ 47.52, 8.55 ], // Bülach
	[ 47.48, 8.53 ], // Dielsdorf
	[ 47.40, 8.40 ], // Dietikon
	[ 47.30, 8.75 ], // Hinwil
	[ 47.25, 8.55 ], // Horgen
	[ 47.27, 8.65 ], // Meilen
	[ 47.38, 8.78 ], // Pfäffikon
	[ 47.35, 8.72 ], // Uster
	[ 47.50, 8.73 ], // Winterthur
	[ 47.37, 8.55 ], // Zürich
];

$bbox = [ 47.13, 8.38, 47.70, 8.85 ]; // minLat, minLon, maxLat, maxLon
$width  = 800;
$height = 600;

$features_by_id = [];

foreach ( $sample_points as $pt ) {
	$lat = $pt[0];
	$lng = $pt[1];

	$j = (int) round( ( $lng - $bbox[1] ) / ( $bbox[3] - $bbox[1] ) * ( $width - 1 ) );
	$i = (int) round( ( $bbox[2] - $lat ) / ( $bbox[2] - $bbox[0] ) * ( $height - 1 ) );

	$url = 'https://wms.geo.admin.ch/?' . http_build_query(
		[
			'SERVICE'      => 'WMS',
			'VERSION'      => '1.3.0',
			'REQUEST'      => 'GetFeatureInfo',
			'LAYERS'       => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
			'QUERY_LAYERS' => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
			'CRS'          => 'EPSG:4326',
			'BBOX'         => implode( ',', $bbox ),
			'WIDTH'        => $width,
			'HEIGHT'       => $height,
			'I'            => max( 0, min( $width - 1, $j ) ),
			'J'            => max( 0, min( $height - 1, $i ) ),
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
		fwrite( STDERR, "Failed: $url\n" );
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

if ( count( $features_by_id ) < 12 ) {
	fwrite( STDERR, 'Expected ~12 districts, got ' . count( $features_by_id ) . "\n" );
	exit( 1 );
}

$collection = [
	'type'     => 'FeatureCollection',
	'name'     => 'zurich-districts',
	'features' => array_values( $features_by_id ),
];

$json = json_encode( $collection, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
if ( $json === false ) {
	fwrite( STDERR, "JSON encode failed\n" );
	exit( 1 );
}

file_put_contents( $out, $json . "\n" );
echo 'Saved ' . count( $features_by_id ) . " districts to {$out}\n";

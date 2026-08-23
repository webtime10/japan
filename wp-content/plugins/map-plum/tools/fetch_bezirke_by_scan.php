<?php
/**
 * Grid-scan Bezirke and keep features matching canton ID range.
 *
 * Usage: php tools/fetch_bezirke_by_scan.php <slug> <minId> <maxId> <minLat> <minLon> <maxLat> <maxLon>
 */

if ( $argc < 8 ) {
	fwrite( STDERR, "Usage: php fetch_bezirke_by_scan.php slug minId maxId minLat minLon maxLat maxLon\n" );
	exit( 1 );
}

$slug   = $argv[1];
$min_id = (int) $argv[2];
$max_id = (int) $argv[3];
$bbox   = [ (float) $argv[4], (float) $argv[5], (float) $argv[6], (float) $argv[7] ];
$out    = dirname( __DIR__ ) . '/sorts/sw/' . $slug . '/' . $slug . '-districts.json';

$supplemental_bboxes = [
	'solothurn' => [
		[ 47.18, 7.48, 47.24, 7.58 ], // Bezirk Solothurn (small enclave)
	],
	'vaud' => [
		[ 46.50, 6.52, 46.56, 6.64 ], // Ouest lausannois (2229)
	],
];

$found = [];
scan_bezirke_bbox( $bbox, 10, $min_id, $max_id, $found );

$expected = $max_id - $min_id + 1;
if ( count( $found ) < $expected && ! empty( $supplemental_bboxes[ $slug ] ) ) {
	foreach ( $supplemental_bboxes[ $slug ] as $extra_bbox ) {
		scan_bezirke_bbox( $extra_bbox, 6, $min_id, $max_id, $found );
	}
}

/**
 * @param array{0:float,1:float,2:float,3:float} $bbox
 * @param array<string,array<string,mixed>>       $found
 */
function scan_bezirke_bbox( array $bbox, int $steps, int $min_id, int $max_id, array &$found ) {
	$width  = 800;
	$height = 600;
	$ctx    = stream_context_create(
		[
			'http' => [
				'timeout' => 60,
				'header'  => "User-Agent: map-plum/1.0\r\n",
			],
		]
	);

	for ( $row = 0; $row <= $steps; $row++ ) {
		for ( $col = 0; $col <= $steps; $col++ ) {
			$i = (int) round( $row / $steps * ( $height - 1 ) );
			$j = (int) round( $col / $steps * ( $width - 1 ) );

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
					'I'            => $j,
					'J'            => $i,
					'INFO_FORMAT'  => 'application/json',
				]
			);

			$raw  = @file_get_contents( $url, false, $ctx );
			$data = json_decode( $raw ?: '', true );

			foreach ( $data['features'] ?? [] as $feature ) {
				$id   = (int) ( $feature['properties']['id'] ?? 0 );
				$name = $feature['properties']['name'] ?? '';
				if ( $id < $min_id || $id > $max_id || $name === '' ) {
					continue;
				}
				$found[ (string) $id ] = [
					'type'       => 'Feature',
					'properties' => [
						'v_kreis' => $name,
						'name'    => $name,
						'id'      => $id,
					],
					'geometry'   => $feature['geometry'],
				];
			}

			usleep( 100000 );
		}
	}
}

if ( count( $found ) < $expected ) {
	fwrite( STDERR, 'Expected ' . $expected . ' districts, got ' . count( $found ) . "\n" );
	foreach ( array_keys( $found ) as $id ) {
		fwrite( STDERR, "  got id $id: {$found[$id]['properties']['name']}\n" );
	}
	exit( 1 );
}

$features = array_values( $found );
usort(
	$features,
	static function ( $a, $b ) {
		return ( $a['properties']['id'] ?? 0 ) <=> ( $b['properties']['id'] ?? 0 );
	}
);

$collection = [
	'type'     => 'FeatureCollection',
	'name'     => $slug . '-districts',
	'features' => $features,
];

file_put_contents( $out, json_encode( $collection, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . "\n" );
echo 'Saved ' . count( $features ) . " districts to {$out}\n";

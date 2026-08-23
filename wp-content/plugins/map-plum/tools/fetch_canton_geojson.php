<?php
/**
 * Build canton districts JSON from cividi/ch-municipalities (filter by kanton code).
 *
 * Cantons without Bezirke (Uri, Zug, …) use Gemeinde boundaries instead.
 *
 * Usage: php tools/fetch_canton_geojson.php uri UR
 */

if ( $argc < 3 ) {
	fwrite( STDERR, "Usage: php fetch_canton_geojson.php <slug> <kuerzel>\n" );
	exit( 1 );
}

$slug    = strtolower( $argv[1] );
$kuerzel = strtoupper( $argv[2] );

$src = __DIR__ . '/ch-gemeinden.geojson';
$out = dirname( __DIR__ ) . '/sorts/sw/' . $slug . '/' . $slug . '-districts.json';

if ( ! is_readable( $src ) ) {
	fwrite( STDERR, "Missing {$src} — run: curl -L -o tools/ch-gemeinden.geojson https://raw.githubusercontent.com/cividi/ch-municipalities/main/data/gemeinden.geojson\n" );
	exit( 1 );
}

$needle = '"kanton.KUERZEL": "' . $kuerzel . '"';
$features = [];

$fh = fopen( $src, 'r' );
if ( ! $fh ) {
	fwrite( STDERR, "Cannot open {$src}\n" );
	exit( 1 );
}

while ( ( $line = fgets( $fh ) ) !== false ) {
	if ( strpos( $line, $needle ) === false ) {
		continue;
	}

	$line = trim( $line, ", \t\r\n" );
	$row  = json_decode( $line, true );
	if ( ! is_array( $row ) || empty( $row['geometry'] ) ) {
		continue;
	}

	$name = $row['properties']['gemeinde.NAME'] ?? '';
	if ( $name === '' ) {
		continue;
	}

	$row['properties'] = [
		'v_kreis' => $name,
		'name'    => $name,
		'bfs'     => $row['properties']['gemeinde.BFS_NUMMER'] ?? null,
	];
	$row['geometry'] = strip_z_from_geometry( $row['geometry'] );
	$features[]      = $row;
}
fclose( $fh );

if ( count( $features ) < 1 ) {
	fwrite( STDERR, "No features for kanton {$kuerzel}\n" );
	exit( 1 );
}

usort(
	$features,
	static function ( $a, $b ) {
		return strcmp( $a['properties']['v_kreis'], $b['properties']['v_kreis'] );
	}
);

$collection = [
	'type'     => 'FeatureCollection',
	'name'     => $slug . '-districts',
	'features' => $features,
];

$json = json_encode( $collection, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
if ( $json === false ) {
	fwrite( STDERR, "JSON encode failed\n" );
	exit( 1 );
}

if ( ! is_dir( dirname( $out ) ) ) {
	mkdir( dirname( $out ), 0755, true );
}

file_put_contents( $out, $json . "\n" );
echo 'Saved ' . count( $features ) . " areas to {$out}\n";

/**
 * @param array<string,mixed> $geometry
 * @return array<string,mixed>
 */
function strip_z_from_geometry( array $geometry ) {
	if ( empty( $geometry['coordinates'] ) ) {
		return $geometry;
	}
	$geometry['coordinates'] = strip_z_coords( $geometry['coordinates'] );
	return $geometry;
}

/**
 * @param mixed $coords
 * @return mixed
 */
function strip_z_coords( $coords ) {
	if ( ! is_array( $coords ) ) {
		return $coords;
	}
	if ( isset( $coords[0] ) && is_numeric( $coords[0] ) ) {
		return [ $coords[0], $coords[1] ];
	}
	$out = [];
	foreach ( $coords as $c ) {
		$out[] = strip_z_coords( $c );
	}
	return $out;
}

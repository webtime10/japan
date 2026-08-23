<?php
if ( $argc < 6 ) {
	fwrite( STDERR, "Usage: scan_ids.php minId maxId minLat minLon maxLat maxLon\n" );
	exit( 1 );
}
$min_id = (int) $argv[1];
$max_id = (int) $argv[2];
$bbox   = [ (float) $argv[3], (float) $argv[4], (float) $argv[5], (float) $argv[6] ];
$width = 800; $height = 600; $found = [];
$ctx = stream_context_create( [ 'http' => [ 'timeout' => 60, 'header' => "User-Agent: map-plum/1.0\r\n" ] ] );
for ( $row = 0; $row <= 10; $row++ ) {
	for ( $col = 0; $col <= 10; $col++ ) {
		$i = (int) round( $row / 10 * ( $height - 1 ) );
		$j = (int) round( $col / 10 * ( $width - 1 ) );
		$url = 'https://wms.geo.admin.ch/?' . http_build_query( [
			'SERVICE' => 'WMS', 'VERSION' => '1.3.0', 'REQUEST' => 'GetFeatureInfo',
			'LAYERS' => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
			'QUERY_LAYERS' => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
			'CRS' => 'EPSG:4326', 'BBOX' => implode( ',', $bbox ),
			'WIDTH' => $width, 'HEIGHT' => $height, 'I' => $j, 'J' => $i,
			'INFO_FORMAT' => 'application/json',
		] );
		$data = json_decode( @file_get_contents( $url, false, $ctx ) ?: '', true );
		foreach ( $data['features'] ?? [] as $f ) {
			$id = (int) ( $f['properties']['id'] ?? 0 );
			if ( $id >= $min_id && $id <= $max_id ) {
				$found[ $id ] = $f['properties']['name'] ?? '';
			}
		}
		usleep( 80000 );
	}
}
ksort( $found );
foreach ( $found as $id => $name ) {
	echo "$id: $name\n";
}
echo 'Total: ' . count( $found ) . "\n";

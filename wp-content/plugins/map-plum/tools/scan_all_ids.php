<?php
$bbox = [ (float) $argv[1], (float) $argv[2], (float) $argv[3], (float) $argv[4] ];
$width = 800; $height = 600; $found = [];
$ctx = stream_context_create( [ 'http' => [ 'timeout' => 60, 'header' => "User-Agent: map-plum/1.0\r\n" ] ] );
for ( $row = 0; $row <= 6; $row++ ) {
	for ( $col = 0; $col <= 6; $col++ ) {
		$i = (int) round( $row / 6 * ( $height - 1 ) );
		$j = (int) round( $col / 6 * ( $width - 1 ) );
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
			if ( $id ) {
				$found[ $id ] = $f['properties']['name'] ?? '';
			}
		}
		usleep( 50000 );
	}
}
ksort( $found );
foreach ( $found as $id => $name ) {
	if ( $id >= 1500 && $id < 2000 ) {
		echo "$id: $name\n";
	}
}
echo 'All in range count: ' . count( array_filter( array_keys( $found ), fn( $id ) => $id >= 1500 && $id < 2000 ) ) . "\n";

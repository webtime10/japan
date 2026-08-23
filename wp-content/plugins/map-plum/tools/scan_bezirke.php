<?php
$bbox = [ 47.05, 7.35, 47.40, 7.75 ];
$width = 800;
$height = 600;
$found = [];

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
		$raw = @file_get_contents( $url, false, stream_context_create( [ 'http' => [ 'timeout' => 30, 'header' => "User-Agent: map-plum/1.0\r\n" ] ] ) );
		$data = json_decode( $raw ?: '', true );
		foreach ( $data['features'] ?? [] as $f ) {
			$id = $f['properties']['id'] ?? '';
			$name = $f['properties']['name'] ?? '';
			if ( $id && $name ) {
				$found[ $id ] = $name;
			}
		}
		usleep( 100000 );
	}
}

ksort( $found );
foreach ( $found as $id => $name ) {
	echo "$id: $name\n";
}
echo 'Total: ' . count( $found ) . "\n";

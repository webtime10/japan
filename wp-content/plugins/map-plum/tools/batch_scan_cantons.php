<?php
$cantons = [
	'jura'       => [ 47.20, 6.95, 47.52, 7.35 ],
	'neuchatel'  => [ 46.85, 6.55, 47.15, 7.05 ],
	'valais'     => [ 45.82, 6.75, 46.65, 8.15 ],
	'vaud'       => [ 46.25, 6.05, 46.88, 7.15 ],
	'ticino'     => [ 45.82, 8.40, 46.55, 9.05 ],
	'thurgau'    => [ 47.38, 8.65, 47.72, 9.35 ],
	'aargau'     => [ 47.20, 7.75, 47.55, 8.35 ],
];

$width = 800; $height = 600;
$ctx = stream_context_create( [ 'http' => [ 'timeout' => 60, 'header' => "User-Agent: map-plum/1.0\r\n" ] ] );

foreach ( $cantons as $name => $bbox ) {
	$found = [];
	for ( $row = 0; $row <= 8; $row++ ) {
		for ( $col = 0; $col <= 8; $col++ ) {
			$i = (int) round( $row / 8 * ( $height - 1 ) );
			$j = (int) round( $col / 8 * ( $width - 1 ) );
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
				if ( $id >= 1900 && $id < 2700 ) {
					$found[ $id ] = $f['properties']['name'] ?? '';
				}
			}
			usleep( 40000 );
		}
	}
	ksort( $found );
	echo "=== $name ===\n";
	foreach ( $found as $id => $n ) {
		echo "  $id: $n\n";
	}
	if ( $found ) {
		$ids = array_keys( $found );
		echo '  RANGE: ' . min( $ids ) . '-' . max( $ids ) . ' (' . count( $ids ) . ")\n\n";
	} else {
		echo "  (none)\n\n";
	}
}

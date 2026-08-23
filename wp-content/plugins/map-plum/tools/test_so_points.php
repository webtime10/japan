<?php
$points = [
	'Gäu'          => [ 47.152, 7.590, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Thal'         => [ 47.313, 7.694, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Bucheggberg'  => [ 47.145, 7.435, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Dorneck'      => [ 47.479, 7.617, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Gösgen'       => [ 47.302, 7.967, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Wasseramt'    => [ 47.192, 7.395, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Lebern'       => [ 47.315, 7.488, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Olten'        => [ 47.352, 7.358, [ 47.05, 7.33, 47.50, 8.05 ] ],
	'Solothurn'    => [ 47.208, 7.537, [ 47.18, 7.48, 47.24, 7.58 ], 400, 400 ],
	'Thierstein'   => [ 47.412, 7.602, [ 47.05, 7.33, 47.50, 8.05 ] ],
];

foreach ( $points as $label => $pt ) {
	$lat     = $pt[0];
	$lng     = $pt[1];
	$bbox    = $pt[2];
	$width   = $pt[3] ?? 800;
	$height  = $pt[4] ?? 600;
	$j       = (int) round( ( $lng - $bbox[1] ) / ( $bbox[3] - $bbox[1] ) * ( $width - 1 ) );
	$i       = (int) round( ( $bbox[2] - $lat ) / ( $bbox[2] - $bbox[0] ) * ( $height - 1 ) );
	$url     = 'https://wms.geo.admin.ch/?' . http_build_query( [
		'SERVICE' => 'WMS', 'VERSION' => '1.3.0', 'REQUEST' => 'GetFeatureInfo',
		'LAYERS' => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
		'QUERY_LAYERS' => 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill',
		'CRS' => 'EPSG:4326', 'BBOX' => implode( ',', $bbox ),
		'WIDTH' => $width, 'HEIGHT' => $height, 'I' => $j, 'J' => $i,
		'INFO_FORMAT' => 'application/json',
	] );
	$raw  = file_get_contents( $url, false, stream_context_create( [ 'http' => [ 'timeout' => 30, 'header' => "User-Agent: map-plum/1.0\r\n" ] ] ) );
	$data = json_decode( $raw ?: '', true );
	$names = [];
	foreach ( $data['features'] ?? [] as $f ) {
		$names[] = ( $f['properties']['id'] ?? '?' ) . ':' . ( $f['properties']['name'] ?? '?' );
	}
	echo "$label ($lat,$lng) => " . ( $names ? implode( ', ', $names ) : 'EMPTY' ) . "\n";
	usleep( 150000 );
}

<?php
$j = json_decode( file_get_contents( __DIR__ . '/so-districts.json' ), true );
$names = [ 'Gäu', 'Thal', 'Bucheggberg', 'Dorneck', 'Gösgen', 'Wasseramt', 'Lebern', 'Olten', 'Solothurn', 'Thierstein' ];
foreach ( $j['features'] as $f ) {
	$id = $f['id'] ?? $f['properties']['id'] ?? '?';
	$p  = $f['properties'];
	echo "id=$id props=" . json_encode( $p ) . "\n";
	if ( count( $names ) <= 20 ) {
		// show bbox center for matching
		$coords = $f['geometry']['coordinates'][0][0] ?? null;
		if ( $coords ) {
			echo "  sample: {$coords[0]}, {$coords[1]}\n";
		}
	}
}

echo 'total features: ' . count( $j['features'] ) . "\n";

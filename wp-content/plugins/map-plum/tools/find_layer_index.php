<?php
$j = json_decode( file_get_contents( __DIR__ . '/ech-layers.json' ), true );
foreach ( $j['layers'] as $i => $l ) {
	$id = $l['layerBodId'] ?? '';
	if ( $id === 'ch.swisstopo.swissboundaries3d-bezirk-flaeche.fill' ) {
		echo "index=$i id=$id\n";
		print_r( $l['attributes'] );
		break;
	}
}

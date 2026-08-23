<?php
$j = json_decode( file_get_contents( __DIR__ . '/ech-layers.json' ), true );
foreach ( $j['layers'] as $l ) {
	$id = $l['layerBodId'] ?? '';
	$t  = $l['attributes']['label'] ?? '';
	if ( stripos( $id, 'bezirk' ) !== false || stripos( $t, 'Bezirk' ) !== false ) {
		echo $id . ' | ' . $t . PHP_EOL;
	}
}

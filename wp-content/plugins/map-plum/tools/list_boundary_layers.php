<?php
$j = json_decode( file_get_contents( __DIR__ . '/ech-layers.json' ), true );
foreach ( $j['layers'] as $l ) {
	$id = $l['layerBodId'] ?? '';
	if ( preg_match( '/swissboundaries3d-(gemeinde|kanton|bezirk)/', $id ) ) {
		echo $id . "\n";
	}
}

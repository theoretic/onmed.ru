<?
/*
AT
30.10.21
*/

namespace Chunkify;

function findMarkerPositions( $text, $marker ) {
	$positions = [];
	$offset = 0;
	$prevOffset = -1;
	while( $offset != $prevOffset ) {
		$prevOffset = $offset;
		$positions[] = $offset = stripos( $text, $marker, $offset );
	}
	return array_unique($positions);;
}

function make( $text, $config ) {

	$config = (Object)$config;

	switch( 1 ) {
		case $config->tag !='' :
			$config->startMarker = "<{$config->tag}>";
			$config->stopMarker = "</{$config->tag}>";
		break;
	}

//echo '$config->tag: ', var_dump($config->tag);
//echo '$config: ', var_dump($config);

	if( !stripos($text, $config->startMarker) ) return false;

	//finding all start marker entries
	$starts = findMarkerPositions( $text, $config->startMarker );

	//finding all stop marker entries
	$stops = findMarkerPositions( $text, $config->stopMarker );

//echo '$starts: ',var_dump($starts);
//echo '$stops: ',var_dump($stops);

	//splitting text into marker-surrounded chunks
	//it's supposed that: 1) first start marker is not at the beginning of the text, 2) start marker is always followed by stop marker
	$chunks = [];
	for( $i=0; $i<=count($starts)+1; $i++ ) {

		switch( $i ) {
			case 0:
				$start = 0;
				$stop = $starts[0];
			break;

			case count($starts)+1:
				$start = $stops[$i-2];
				$stop = strlen($text);
			break;

			default:
				$start = $starts[$i-1];
				$stop = $stops[$i-1];
		}

//echo '$i. $start, $stop-$start: ', "$i. $start, ", $stop-$start, "\n\n";

		$chunk = substr( $text, $start, $stop-$start );
		$chunks[] = (Object)[
			'insideMarkers'		=> $i%2 > 0,
			'text'			=> $chunk
		];
	}

//echo '$chunks: ',var_dump($chunks);//

	return $chunks;
}
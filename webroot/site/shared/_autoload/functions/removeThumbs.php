<?
/*
removes auto-generated images for given $filename
AT
26.01.22
*/

function removeThumbs($file){
	$dir = dirname( $file );
	$filename = basename( $file );
	$thumbs = [];

	foreach( glob("$dir/*/$filename.*") as $thumb ) {
		$thumbs[] = $thumb;
		unlink($thumb);
	}

	return $thumbs;
}
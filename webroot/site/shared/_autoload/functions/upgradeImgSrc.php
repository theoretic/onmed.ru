<?
/*
/path/to/file.jpg -> /path/to/file.jpg.webp
AT
31.01.22
*/

function upgradeImgSrc($src,$format){
	if( !$format) return $src;
	return "$src.$format";
	}
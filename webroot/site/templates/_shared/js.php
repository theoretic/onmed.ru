<?
/*
js files

possible format:

$js = [
	'/path/to/file1.js',
	'/path/to/file2.js'		=> ['region':'head'],
	'/path/to/file3.js'		=> ['async','deferred'],
	'/path/to/file4.js'		=> ['region':'head','async','deferred'],
]

AT
18.11.23
*/

$jsDefaultMarkupRegion = 'end';

$js = (array)$js;

foreach($js as $index=>$value) {

	$jsFile = $value;
	$jsOptions = [];

	if (is_array($value) ) {
		$jsFile = $index;
		$jsOptions = $value;
	}

	if( $MARKUP_REGION===NULL && $jsOptions['region'] ) continue;
	if( $MARKUP_REGION != $jsDefaultMarkupRegion && !isset($jsOptions['region']) ) continue;

	$async = isset($jsOptions['async'])? 'async' : '';
	$deferred = isset($jsOptions['deferred'])? 'deferred' : '';

	$src = $jsFile;
	if( $settings->caching->disableJsCache ){
		$hash = md5_file( DOCUMENT_ROOT.$jsFile );
		$src = "$jsFile?$hash";
	}

	echo "<script $async $deferred src='$src'></script>";
}
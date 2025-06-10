<?
/*
css files

possible format:

$css[] = [
	'/path/to/file1.css',
	'/path/to/file2.css'		=> ['region':'head'],
]

AT
18.11.23
*/

$cssDefaultMarkupRegion = 'end';
$css = (array)$css;

foreach($css as $index=>$value) {
	$cssFile = $value;
	$cssOptions = [];

	if (is_array($value) ) {
		$cssFile = $index;
		$cssOptions = $value;
	}

	if( isset($cssOptions['region']) && $MARKUP_REGION === NULL ) continue;
	if( isset($cssOptions['region']) && $MARKUP_REGION !== $cssOptions['region'] ) continue;
	if( !isset($cssOptions['region']) && $MARKUP_REGION != $cssDefaultMarkupRegion ) continue;

//echo '$MARKUP_REGION:', var_dump($MARKUP_REGION), '<br>';
//echo '$cssFile:', var_dump($cssFile), '<br>';

	$href = $cssFile;
	if( $settings->caching->disableCssCache ){
		//Hash value will change if source *.*ss files were changed. We need to break client cache in this case
		$cssFile_ = str_replace('.css','',$cssFile);

		$hash_path = "{$_SERVER['DOCUMENT_ROOT']}/$cssFile_/.cache/.hash";
		$hash = file_exists($hash_path) ? file_get_contents($hash_path) : md5(microtime());
		$href = "$cssFile?$hash";
	}

	echo "<link href='$href' rel='stylesheet' type='text/css'>";
}
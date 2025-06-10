<?
/*
Style
AT
15.05.23
*/

//error_reporting(0);
error_reporting( E_ALL && ~E_WARNING );

require_once "{$_SERVER['DOCUMENT_ROOT']}/vendor/autoload.php";
require_once '_include/__autoload.php';

//

$Style = new Style( $_REQUEST['request'], include '_include/config.php' );

if( $Style->isActual() ){
	if( $Style->isCached() ){
		$Style->output();
		die();
	}
}

//style is no longer actual

try{
	if( $Style->isCached() ) $Style->wipeCache();
	$Style->make();
	if( $Style->config['removeUnusedCss'] !== false ) $Style->saveHTMLHash();
	if( $Style->config['save'] !== false ) $Style->save();
	$Style->output();
}
catch( Exception  $e ){
	echo $e->getMessage();
}
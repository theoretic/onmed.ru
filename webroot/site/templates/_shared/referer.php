<?
/*
AT
16.11.17
*/

//getting the referer page which called the data API
//$url = $_SERVER['REQUEST_URI'];

$referer = $_SERVER['HTTP_REFERER']? : $input->get->referer;

if( $referer && strstr($referer,$_SERVER['HTTP_HOST']) )
	{
	//path to referer page
	$referer = str_replace($_SERVER['HTTP_HOST'],'',$referer);
	$referer = str_replace('//','',$referer);
	$referer = str_replace('//','',$referer);

	$REFERER_PAGE = $pages->get("path=$referer");
	}
else
	$REFERER_PAGE = false;
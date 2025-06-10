<?
/*
no output here, only function/class loading
AT
17.08.23
*/

/*
//cURL patch -- Windows only!
if( strstr(PHP_OS,'WIN') )
	{
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}
*/

//this autoload can be used both in templates and api
include_once DOCUMENT_ROOT."/site/shared/autoload.php";

//template-dependent prepend
$prependFileCandidate = "{$page->template}/__prepend.php";
if( is_file($prependFileCandidate) ) include $prependFileCandidate;

//actions for all templates
//include '_shared/actions.php';
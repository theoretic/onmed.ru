<?
/*
API
AT
05.10.23
*/

namespace ProcessWire;

//PW
include "{$_SERVER['DOCUMENT_ROOT']}/index.php";
//include DOCUMENT_ROOT . "/site/templates/_shared/__prepend.php";

//this autoload can be used both in templates and api
include_once DOCUMENT_ROOT."/site/shared/autoload.php";

//setting language
if( $referer->page && $referer->language ) $user->language = $referer->language;

//echo '$referer->page: ', var_dump($referer->page);
//echo '$referer->language: ', var_dump($referer->language);

//controller
$parts = explode( '/', $_REQUEST['request'] );
$controller = $parts[0].'.php';

if( $controller != 'validator.php' ){
	$controller = "{$_REQUEST['request']}.php";
	$controller = str_replace( '/.php', '.php', $controller );
}
//echo '$controller: ', var_dump($controller);
if( !is_file($controller) ) die();

try{
	$response = (Array)include $controller;
}
catch ( Exception $e ) {
	$response = [
		//'error' => __('Unfortunately an error occured.',I18N_GLOBAL),
		'error' => $e->getMessage(),
	];
}

//csrf
$responseCSRF = [
	'csrf'	=> [
		'name'	=> $session->CSRF->getTokenName(),
		'value'	=> $session->CSRF->getTokenValue()
	]
];

if( !$skipCSRF )  $response = array_merge($response,$responseCSRF);

//outputting response
switch($outputFormat)
	{
	case 'raw':
		echo $response;
	break;

	case 'xml':
		header("Content-Type: application/xml");
		echo $response;
	break;

	case "sse":
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		header('Access-Control-Allow-Origin: *');//optional

		$output = "";
		foreach( $response as $name=>$value ) $output .= "$name: $value\n";
		$output .= "\n\n";

		echo $output;
	break;

	default: //JSON
		//$response = (object)$response;
		header("Content-Type: application/json");
		echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	}


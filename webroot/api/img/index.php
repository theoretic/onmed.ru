<?
/*
image shrinker
the url part after the dir name is parsed into $dir/$file
using external imagemagick
using gd
using php unsharp
using RewriteEngine
AT
02.02.23
*/

error_reporting( E_ERROR );

// Check that the request is GET
$request_type = $_SERVER['REQUEST_METHOD'];

if ($request_type != 'GET') {
	header("HTTP/1.0 405 Method Not Allowed");
	die();
}

// Request has to be set
$request = isset($_REQUEST['request']) ? $_REQUEST['request'] : null;

//var_dump($request); die();

if (!isset($request) || $request == '') {
	header("HTTP/1.0 406 Not Acceptable");
	die();
}

//config
include '_include/config.php';
include '_include/functions/__autoload.php';
include "_include/{$config['engine']}/Thumb.php";

$Thumb = new Thumb($request, $config);

//times
$timeFile = '.time';
$timePrevious = (float)trim(file_get_contents($timeFile));

//switch to batch mode if too many requests per second
$time = Benchmark::microtime2float();
$timeStep = $time - $timePrevious;
if ($timeStep < .01) $Thumb->config['mode'] = 'batch';

$Thumb->create();
$Thumb->output();

// rewrite time mark into .time file
file_put_contents($timeFile, $time);
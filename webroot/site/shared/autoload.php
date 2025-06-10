<?
/*
Autoload initialization
AT
17.08.23
*/

include_once DOCUMENT_ROOT."/site/shared/functions/rglob.php";

//class autoload
spl_autoload_register(function ($class) {
	$path = DOCUMENT_ROOT."/site/shared/classes/{$class}.php";
	$path = str_replace('\\','/',$path);
	include_once $path;
});

//non-class autoload
foreach( rglob( DOCUMENT_ROOT."/site/shared/_autoload/*.php") as $includeFile )
	include_once $includeFile;
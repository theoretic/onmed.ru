<?
/*
AT
php8 ready
10.12.20
*/

spl_autoload_register( function($_class) {
	$path = '_include/classes/'.$_class.'.php';
	$path = str_replace('\\','/',$path); //Windows issue
	include_once $path;
	});
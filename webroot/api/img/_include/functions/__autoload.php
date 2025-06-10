<?
/*
php8
AT
14.12.20
*/

spl_autoload_register( function($_class) {
	include_once '_include/classes/'.$_class.'.php';
});

?>
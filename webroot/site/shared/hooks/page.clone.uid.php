<?
/*
Creates new UID
AT
25.07.23
*/

$wire->addHookBefore("Pages::clone", function($event) {

	$page = $event->arguments(0);

	include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/_autoload/functions/uid.php";
	include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/uid.php";
	$page->uid = uid();
	$this->message("UID: {$page->uid}");

});
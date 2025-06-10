<?
/*
Creates new UID
AT
27.06.22
*/

$wire->addHookBefore("Pages::save", function($event) {

	$page = $event->arguments(0);

	if( !$page->hasField('uid') ) return;

	$count = \ProcessWire\wire("pages")->count("uid={$page->uid}");
	if( (!$page->uid && $count==0) || ( $page->uid && $count==1) ) return;

	//include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/uid.php";
	include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/_autoload/functions/uid.php";
	$page->uid = uid();
	$this->message("UID: {$page->uid}");

});
<?
/*
Unpublishes all warning pages except the published one
AT
27.06.22
*/

$wire->addHookBefore("Pages::save(template=warning)", function($event) {


	$page = $event->arguments(0);

//$this->message("saving warning..." . var_dump( $page->is(Page::statusUnpublished)) );

	//nothing to do if current page is unpublished
	if( $page->is(Page::statusUnpublished) ) return;


	//unpublishing every warning page except the current page which is published
	$warningPages = \ProcessWire\wire("pages")->find("template=warning,id!={$page->id},include=all");

	foreach( $warningPages as $warningPage ){
		$warningPage->addStatus(\ProcessWire\Page::statusUnpublished);
		$warningPage->save();
	}

$this->message(count($warningPages) . "warning(s) unpublished.");

});
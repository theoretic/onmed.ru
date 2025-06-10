<?
/*
genericDate hook
AT
29.10.24
*/

namespace ProcessWire;

wire()->addHookProperty('Page::genericDate', function($event) {
	$page = $event->object;

	//choosing field
	switch(true){
		case $page->date:
			$genericDate = $page->date;
		break;

		default:
			$genericDate = date( 'd.m.Y', $page->created );
		break;
	}

	$event->return = $genericDate;
});
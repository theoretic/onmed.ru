<?
/*
shouldHaveSubmenu hook
AT
01.09.25
*/

namespace ProcessWire;

wire()->addHookProperty('Page::shouldHaveSubmenu', function($event) {
	$page = $event->object;

	$return = false;

	switch(true){
		case count( $page->children() ):
			$return = true;
		break;

		case count( $page->siblings() ):
			$return = true;
		break;
	}

	$event->return = $return;
});
<?
/*
genericTimestamp hook
AT
19.11.24
*/

namespace ProcessWire;

wire()->addHookProperty('Page::genericTimestamp', function($event) {
	$page = $event->object;

	//choosing field
	switch(true){
		case $page->date:
			//$genericTimestamp = $page->date;
			list( $day, $month, $year ) = explode( '.', $page->date );
			$genericTimestamp = mktime( 0, 0, 0, $month, $day, $year );
		break;

		default:
			$genericTimestamp = $page->created;
		break;
	}

	$event->return = $genericTimestamp;
});
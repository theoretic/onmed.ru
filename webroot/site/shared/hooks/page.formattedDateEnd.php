<?
/*
formattedDateEnd hook
AT
04.09.25
*/

namespace ProcessWire;

wire()->addHookProperty('Page::formattedDateEnd', function($event) {

	$page = $event->object;
	if( !$page->hasField('date_end') ) return;
	if( !is_int($page->date_end) ) return;

	$event->return = date( 'd.m.Y', $page->date_end );
});
<?
/*
formattedDateStart hook
AT
04.09.25
*/

namespace ProcessWire;

wire()->addHookProperty('Page::formattedDateStart', function($event) {

	$page = $event->object;
	if( !$page->hasField('date_start') ) return;
	if( !is_int($page->date_start) ) return;

	$event->return = date( 'd.m.Y', $page->date_start );
});
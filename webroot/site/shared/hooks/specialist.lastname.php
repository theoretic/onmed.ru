<?
/*
Last name hook
for compatibility
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
AT
24.02.23
*/

wire()->addHookProperty('Page(template=specialist)::lastname', function($event) {
	$page = $event->object;
	$event->return = $page->title;
});
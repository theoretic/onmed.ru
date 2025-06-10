<?
/*
hasImagesInBody hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
http://wiki.archimed-soft.ru/main/quick-links
http://reg.onmed.ru/?mode=offline&spec=5&service=&doc=242

В Description наоборот - добавить ФИО в самое начало. Но так, чтобы Title и Description не совпадали.

AT
02.07.24
*/

wire()->addHookProperty('Page::hasImagesInBody', function($event) {
	$page = $event->object;
	$event->return = strpos($page->body,'<img');
});
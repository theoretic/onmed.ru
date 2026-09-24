<?
/*
hasImagesInBody hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637

В Description наоборот - добавить ФИО в самое начало. Но так, чтобы Title и Description не совпадали.

AT
02.07.24
*/

wire()->addHookProperty('Page::hasImagesInBody', function($event) {
	$page = $event->object;
	$event->return = strpos($page->body,'<img');
});
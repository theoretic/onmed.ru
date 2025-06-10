<?
/*
Extra headers
AT
02.11.23
*/

$wire->addHookBefore("Page::render", function($event) {
	//$page = $event->arguments(0);
	header("Content-Security-Policy: default-src https: 'self' 'unsafe-inline' 'unsafe-eval'; img-src https: data:");
	header('X-Content-Type-Options: nosniff');
});
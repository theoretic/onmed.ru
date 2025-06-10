<?
/*
Rating hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
AT
24.02.23
*/

wire()->addHookProperty('Page(template=specialist)::rating', function($event) {
	$page = $event->object;

	$feedbackPages = \ProcessWire\wire('pages')->find("template=feedback,specialists=$page");
	$feedbacksTotal = count($feedbackPages);
	if( $feedbacksTotal==0 ){
		$event->return = false;
		return;
	}

	$rating = 0;
	foreach( $feedbackPages as $feedbackPage ){
		$rating += $feedbackPage->rating;
	}

	$rating /= $feedbacksTotal;
	$rating = round( $rating, 1 );
	$event->return = $rating;
});
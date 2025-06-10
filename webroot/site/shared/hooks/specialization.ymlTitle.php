<?
/*
genericTitle hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
http://wiki.archimed-soft.ru/main/quick-links
http://reg.onmed.ru/?mode=offline&spec=5&service=&doc=242
AT
16.12.23
*/

wire()->addHookProperty('Page(template=specialization)::ymlTitle', function($event) {
	$page = $event->object;
	$ymlTitle = $page->title;

	switch(true){
		case
			stripos( $page->title, 'ультразвук' ) !== false
			|| stripos( $page->title, 'ультрaзвук' ) !== false
			:
			$ymlTitle = 'узи-специалист';
		break;

		case stripos( $specializationPage->title, 'детский' ) !== false:
			$ymlTitle = str_ireplace('детский', '', $ymlTitle );
		break;
	}

	$ymlTitle = str_replace( ' ()', '', $ymlTitle );
	$event->return = $ymlTitle;
});
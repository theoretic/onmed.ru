<?
/*
genericName hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
http://wiki.archimed-soft.ru/main/quick-links
http://reg.onmed.ru/?mode=offline&spec=5&service=&doc=242
AT
16.12.23
*/

wire()->addHookProperty('Page(template=specialization)::ymlName', function($event) {
	$page = $event->object;
	$ymlName = $page->name;

	switch(true){
		case
			stripos( $page->title, 'ультразвук' ) !== false
			|| stripos( $page->title, 'ультрaзвук' ) !== false
			:
			$ymlName = 'uzi-specialist';
		break;
	}

	$event->return = $ymlName;
});
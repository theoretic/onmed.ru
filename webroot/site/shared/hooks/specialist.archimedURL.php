<?
/*
Archimed URL hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
http://wiki.archimed-soft.ru/main/quick-links
http://reg.onmed.ru/?mode=offline&spec=5&service=&doc=242
AT
24.02.23
*/

wire()->addHookProperty('Page(template=specialist)::archimedURL', function($event) {
	$page = $event->object;

	$archimedURL = '//reg.onmed.ru/?mode=offline';
	if( $page->id_archimed ){
		//looking for specialization with archimed_id
		$specializationPage = $page->specializations->get('id_archimed!=');
		if($specializationPage) $archimedURL .= "&spec={$specializationPage->id_archimed}";
		$archimedURL .= "&doc={$page->id_archimed}";
	}

	$event->return = $archimedURL;
});
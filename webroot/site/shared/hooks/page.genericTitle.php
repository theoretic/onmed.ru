<?
/*
genericTitle hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
http://wiki.archimed-soft.ru/main/quick-links
http://reg.onmed.ru/?mode=offline&spec=5&service=&doc=242
AT
24.02.25
*/

namespace ProcessWire;

wire()->addHookProperty('Page::genericTitle', function($event) {
	$page = $event->object;

//echo 'wire(input)->urlSegments[1]: ', var_dump(wire('input')->urlSegments[1]);//
//echo '$page->template->name: ', var_dump($page->template->name);//

	//choosing field
	switch(true){
		case $page->title_plural:
			$genericTitle = $page->title_plural;
		break;

		case $page->longtitle:
			$genericTitle = $page->longtitle;
		break;

		case $page->title:
			$genericTitle = $page->title;
		break;
	}

	//template-dependent logic
	switch( $page->template->name ){
		case 'specialist':
			$genericTitle = "{$page->title} {$page->firstname} {$page->patronymic}";
		break;

		case 'specialists':
			if( wire('input')->urlSegments[1] ) {
				//specialization page
				$specializationPage = wire('pages')->get( "name=". wire('input')->urlSegments[1] );
//echo '$specializationPage: ', var_dump($specializationPage);//
				$genericTitle = $specializationPage->title_plural? : $specializationPage->longtitle? : $specializationPage->title;
			}
		break;
	}

	//pagination
	$pageCandidate = wire('input')->urlSegments[1];
	if( $pageCandidate && (int)$pageCandidate == $pageCandidate ){
		$genericTitle .= " , страница $pageCandidate";
	}

	$event->return = $genericTitle;
});
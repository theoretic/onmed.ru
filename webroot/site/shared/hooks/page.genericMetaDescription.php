<?
/*
genericDescription hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
http://wiki.archimed-soft.ru/main/quick-links
http://reg.onmed.ru/?mode=offline&spec=5&service=&doc=242

В Description наоборот - добавить ФИО в самое начало. Но так, чтобы Title и Description не совпадали.

AT
15.01.24
*/

wire()->addHookProperty('Page::genericMetaDescription', function($event) {
	$page = $event->object;
	$append = 'Клиника ОНМЕД в Москве. Онлайн запись через сайт или по телефону.';

	//choosing field
	switch(true){
		//SEO fields have the highest priority
		case $page->seo_description:
			$genericMetaDescription = $page->seo_description;
		break;

		case $page->summary:
			$genericMetaDescription = $page->summary;
		break;

		case $page->longtitle:
			$genericMetaDescription = $page->longtitle;
		break;

		case $page->title:
			$genericMetaDescription = $page->title;
		break;
	}

	//template-dependent logic
	switch( $page->template->name ){
		case 'discount':
			$append = 'Акции и пакетные предложения клиники ОНМЕД';
		break;

		case 'specialists':
			if( wire('input')->urlSegments[1] ) {
				//specialization page
				$specializationPage = wire('pages')->get( "name=". wire('input')->urlSegments[1] );
				$genericMetaDescription = $specializationPage->genericMetaDescription;
			}
		break;

		case 'specialist':
			$genericMetaDescription = "{$page->title} {$page->firstname} {$page->patronymic}";
			//specializations
			$specializations = [];
			foreach( $page->specializations as $specializationPage)
				$specializations[] = $specializationPage->title;
			$specializations = implode( ', ', $specializations );
			$genericMetaDescription .= " ($specializations)";
		break;

	}

	$genericMetaDescription .= ' - '. $append;

	$event->return = $genericMetaDescription;
});
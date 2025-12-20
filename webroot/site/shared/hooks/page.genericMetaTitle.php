<?
/*
genericMetaTitle hook
https://processwire.com/talk/topic/18457-selectors-and-aggregation-functions/?tab=comments#comment-161637
http://wiki.archimed-soft.ru/main/quick-links
http://reg.onmed.ru/?mode=offline&spec=5&service=&doc=242

В Title после ФИО добавить &mdash; [Перечисление специальностей]

AT
04.09.25
*/

namespace ProcessWire;

wire()->addHookProperty('Page::genericMetaTitle', function($event) {
	$page = $event->object;

	//choosing field
	switch(true){
		case $page->seo_title:
			$genericMetaTitle = $page->seo_title;
			$event->return = $genericMetaTitle;
			return;
		break;

		case $page->longtitle:
			$genericMetaTitle = $page->longtitle;
		break;

		case $page->title:
			$genericMetaTitle = $page->title;
		break;
	}

	$append = '';
	$append2 = 'Медицинский центр ОНМЕД в Москве, ВАО метро Первомайская, метро Измайловская, ул. Нижняя Первомайская';

/*
	//parent template-dependent logic
	switch( $page->parent->template->name ){
		case 'news':
			$append = 'Новости';
		break;
	}
*/

	//template-dependent logic
	switch( $page->template->name ){
		case 'discount':
			$append = 'Акции и пакетные предложения';
		break;

		case 'offer':
			//$append = 'Медицинский центр ОНМЕД в Москве, ВАО м. Первомайская и м. Измайловская, ул. Нижняя Первомайская';
		break;

		case 'specialist':
			$genericMetaTitle = "{$page->title} {$page->firstname} {$page->patronymic}";
			$genericMetaTitle .= ' — ';
			//specializations
			$specializations = [];
			foreach( $page->specializations as $specializationPage)
				$specializations[] = $specializationPage->title;
			$specializations = implode( ', ', $specializations );
			$genericMetaTitle .= $specializations;
			
		break;

		case 'specialists':
			if( wire('input')->urlSegments[1] ) {
				//specialization page
				$specializationPage = wire('pages')->get( "name=". wire('input')->urlSegments[1] );
				$genericMetaTitle = $specializationPage->genericMetaTitle;
			}
		break;
	}


	//pagination
	$pageCandidate = wire('input')->urlSegments[1];
	if( $pageCandidate && (int)$pageCandidate == $pageCandidate ){
		$genericMetaTitle .= " , страница $pageCandidate";
	}

	if( $append ) $genericMetaTitle .= ' - ' . $append;
	if( $append2 ) $genericMetaTitle .= ' - ' . $append2;

	$event->return = $genericMetaTitle;
});
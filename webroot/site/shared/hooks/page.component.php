<?
//component pages finder

$wire->addHookAfter('InputfieldPage::getSelectablePages', function($event) {
	if($event->object->hasField != 'component') return;
	$event->return = $event->arguments('page')->parent('template=offer')->find('template=component');
});
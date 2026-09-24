<?
/*
Medflex speciality ID hook
Triggered by saving a specialization page.
Specialization pages are duplicated per branch (same title), so:
- empty medflex_speciality_id: taken from a same-title copy, else matched by name against the Medflex speciality list;
- the resulting value is copied to same-title copies that are still empty (existing values are never overwritten).
The order of a specialist's specializations defines the order of services in <appointment-specialist>.
Bulk fill with doctor evidence: tools/import/medflex/assign_medflex_speciality_id.php
AT
24.09.26
*/

namespace ProcessWire;

wire()->addHookAfter('Pages::saveReady(template=specialization)', function(HookEvent $event) {

	$page = $event->arguments(0);
	$pages = $event->object;

	if( !$page->hasField('medflex_speciality_id') ) return;
	if( $page->isNew() ) return;
	if( !$page->title ) return;

	try {
		$title = wire('sanitizer')->selectorValue($page->title);
		$copies = $pages->find("template=specialization,title=$title,id!={$page->id},include=all");

		if( !$page->medflex_speciality_id ) {
			$source = $copies->get('medflex_speciality_id>0');
			if( $source ) {
				$page->medflex_speciality_id = $source->medflex_speciality_id;
			} else {
				require_once wire('config')->paths->root . 'api/medflex/_include/medflex.php';
				$apiKey = wire('modules')->get('SettingsFactory')->getSettings('medflex')->api_key;
				$specialities = Medflex::specialities($apiKey ?: null)['data'] ?? [];
				if( !$specialities ) {
					$pages->warning('Medflex: список специализаций недоступен, ID специализации Medflex не назначен.');
					return;
				}
				$id = Medflex::matchSpeciality($page->title, $specialities);
				if( !$id ) {
					$pages->warning("Специализация «{$page->title}» не найдена в Medflex, укажите ID специализации Medflex вручную.");
					return;
				}
				$page->medflex_speciality_id = $id;
			}
			$pages->message("ID специализации Medflex: {$page->medflex_speciality_id}");
		}

		//same-title copies with empty value: saving the single field only
		$assigned = 0;
		foreach( $copies as $copy ) {
			if( $copy->medflex_speciality_id ) continue;
			$copy->of(false);
			$copy->medflex_speciality_id = $page->medflex_speciality_id;
			$pages->saveField($copy, 'medflex_speciality_id');
			$assigned++;
		}
		if( $assigned ) $pages->message("ID специализации Medflex назначен также копиям в филиалах: $assigned");

	} catch( \Throwable $e ) {
		$pages->warning('Medflex: ' . $e->getMessage());
	}

});

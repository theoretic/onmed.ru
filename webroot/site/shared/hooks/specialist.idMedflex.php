<?
/*
Medflex ID hook
Triggered by saving a specialist page with empty id_medflex.
Assigns id_medflex to the saved page and to every other specialist page with empty id_medflex,
since the Medflex doctors list has to be fetched in full anyway.
Matching: Medflex efio vs title + firstname + patronymic (see tools/import/medflex/assign_id_medflex.php).
Medflex API: https://developer.medflex.ru/clinic-site/tag/models
/models/doctor/all/ returns all doctors, with or without schedule. No name filter available, so the whole list is fetched.
AT
24.09.26
*/

namespace ProcessWire;

wire()->addHookAfter('Pages::saveReady(template=specialist)', function(HookEvent $event) {

	$page = $event->arguments(0);
	$pages = $event->object;

	if( !$page->hasField('id_medflex') ) return;
	if( $page->id_medflex ) return;
	if( $page->isNew() ) return;

	$fio = fn(Page $p) => ( $p->title && $p->firstname && $p->patronymic )
		? "{$p->title} {$p->firstname} {$p->patronymic}"
		: '';

	//not enough data to find the doctor
	if( !$fio($page) ) return;

	try {
		$apiKey = wire('modules')->get('SettingsFactory')->getSettings('medflex')->api_key;
		if( !$apiKey ) {
			$pages->warning('Medflex: API key не задан, ID Medflex не назначен.');
			return;
		}

		require_once wire('config')->paths->root . 'api/medflex/_include/medflex.php';

		$warnings = [];
		$result = Medflex::fetchAllPages('https://api.medflex.ru/models/doctor/all/', $apiKey, $warnings);
		if( $result === null ) {
			$pages->warning('Medflex API недоступен, ID Medflex не назначен.');
			return;
		}

		//normalised efio → Medflex IDs (several IDs mean ambiguous name)
		$idsByName = [];
		foreach( $result['data'] as $doctor ) {
			$idsByName[ Medflex::normaliseName($doctor['efio'] ?? '') ][] = (int) $doctor['id'];
		}
		$findIds = fn(string $fio) => $idsByName[ Medflex::normaliseName($fio) ] ?? [];

		//current page: assigned here, stored by the save in progress
		$ids = $findIds( $fio($page) );
		switch( count($ids) ) {
			case 1:
				$page->id_medflex = $ids[0];
				$pages->message("ID Medflex: {$ids[0]}");
			break;

			case 0:
				$pages->warning("Врач «{$fio($page)}» не найден в Medflex" . ($warnings ? ' (' . implode(' ', $warnings) . ')' : '') . '.');
			break;

			default:
				$pages->warning("Врач «{$fio($page)}» найден в Medflex несколько раз (ID: " . implode(', ', $ids) . '), ID Medflex не назначен.');
			break;
		}

		//other specialist pages with empty id_medflex: saving the single field only
		$assigned = [];
		foreach( $pages->find("template=specialist,id_medflex='',id!={$page->id},include=unpublished") as $otherPage ) {
			$ids = $findIds( $fio($otherPage) );
			if( count($ids) !== 1 ) continue;

			$otherPage->of(false);
			$otherPage->id_medflex = $ids[0];
			$pages->saveField($otherPage, 'id_medflex');
			$assigned[] = "{$fio($otherPage)} ({$ids[0]})";
		}

		if( $assigned ) $pages->message('ID Medflex назначен также: ' . implode(', ', $assigned));

	} catch( \Throwable $e ) {
		$pages->warning('Medflex: ' . $e->getMessage());
	}

});

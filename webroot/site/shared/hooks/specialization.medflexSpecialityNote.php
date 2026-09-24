<?
/*
Medflex speciality name note
Shows the Medflex speciality name under the medflex_speciality_id input in the admin.
Cache only (speciality_global, see api/medflex/speciality.php): rendering the form never calls the Medflex API.
AT
24.09.26
*/

namespace ProcessWire;

wire()->addHookBefore('InputfieldInteger::render', function(HookEvent $event) {

	$inputfield = $event->object;
	if( $inputfield->name !== 'medflex_speciality_id' ) return;

	$id = (int) $inputfield->value;
	if( !$id ) return;

	require_once wire('config')->paths->root . 'api/medflex/_include/medflex.php';

	$name = null;
	foreach( Medflex::specialities()['data'] ?? [] as $speciality ) {
		if( (int) $speciality['id'] === $id ) { $name = $speciality['name']; break; }
	}

	$inputfield->notes = $name ? "Medflex: $name" : 'Medflex: ID не найден в кэше списка специализаций';

});

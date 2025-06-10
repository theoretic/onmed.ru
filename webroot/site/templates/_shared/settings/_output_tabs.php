<?
/*
AT
27.12.20
*/

namespace ProcessWire;

$inputfields = new InputfieldWrapper();

foreach( $tabs as $title => $fields ) {
	$tab = new InputfieldWrapper();
	$tab->attr('class', 'WireTab');
	$tab->attr( 'title', $title );
	$tab->add($fields);
	$inputfields->append($tab);
}

return $inputfields;
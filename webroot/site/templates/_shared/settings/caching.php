<?
/*
Caching
AT
18.11.23
 */

namespace ProcessWire;

return [
	[
		'name' => 'disableCssCache',
		'label' => 'Отключить кэширование CSS',
		'type' => 'InputfieldCheckbox',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'disableJsCache',
		'label' => 'Отключить кэширование Javascript',
		'type' => 'InputfieldCheckbox',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
];
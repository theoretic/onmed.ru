<?
/*
Requisites
AT
24.11.23
 */

namespace ProcessWire;

return [
	[
		'name' => 'company_name',
		'label' => 'Название организации',
		'type' => 'InputfieldText',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'company_fullname',
		'label' => 'Длинное название организации',
		'type' => 'InputfieldText',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'ogrn',
		'label' => 'ОГРН',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'inn',
		'label' => 'ИНН',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'kpp',
		'label' => 'КПП',
		'type' => 'InputfieldText',
		'columnWidth' => 34,
		//'required' => true,
		//'value' => '',
	],

	[
		'name' => 'bank',
		'label' => 'Банк',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'bik',
		'label' => 'БИК',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'checking_account',
		'label' => 'Расчётный счёт',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'correspondent_account',
		'label' => 'Корр. счёт',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],

];
<?
/*
Requisites
AT
09.09.25
 */

namespace ProcessWire;

return [
	[
		'name' => 'company_name',
		'label' => 'Название организации',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'company_fullname',
		'label' => 'Полное название организации',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'registration_date',
		'label' => 'Дата гос. регистрации',
		'type' => 'InputfieldText',
		'columnWidth' => 34,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'founders',
		'label' => 'Учредители',
		'type' => 'InputfieldText',
		//'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'legal_address',
		'label' => 'Юр. адрес',
		'type' => 'InputfieldText',
		//'columnWidth' => 25,
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
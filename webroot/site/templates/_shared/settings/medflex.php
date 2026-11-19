<?
/*
Medflex
AT
05.05.26
 */

namespace ProcessWire;

return [
	[
		'name' => 'api_key',
		'label' => 'API Key',
		'type' => 'InputfieldText',
		//'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'appointment_url',
		'label' => 'Ссылка на запись на услугу',
		'type' => 'InputfieldURL',
		'value' => 'https://booking.medflex.ru/?user=8b6a902e074e5c2df3c31b7baaa49c9a',
	],
];
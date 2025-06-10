<?
/**
 * since you can only return 1 array to the module, you have to wrap 
 * the tabs in an inputfield wrapper

Contacts

AT
23.11.23
 */

namespace ProcessWire;

return [
	[
		'name' => 'phone',
		'label' => 'Тел.',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'phone_yandex',
		'label' => 'Тел. для Yandex',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'phone_google',
		'label' => 'Тел. для Google',
		'type' => 'InputfieldText',
		'columnWidth' => 34,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'email',
		'label' => 'Публичный email',
		'type' => 'InputfieldEmail',
		'columnWidth' => 50,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'private_email',
		'label' => 'Приватный email',
		'type' => 'InputfieldEmail',
		'columnWidth' => 50,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'schedule',
		'label' => 'Режим работы',
		'type' => 'InputfieldTextarea',
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'address',
		'label' => 'Адрес',
		'type' => 'InputfieldTextarea',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'metro',
		'label' => 'Метро',
		'type' => 'InputfieldTextarea',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'getting_to',
		'label' => 'Как проехать',
		'type' => 'InputfieldTextarea',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'map_html',
		'label' => 'HTML-код Яндекс.Карты',
		'type' => 'InputfieldTextarea',
		//'required' => true,
		//'value' => '',
	],
];
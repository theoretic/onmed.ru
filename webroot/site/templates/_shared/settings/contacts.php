<?
/**
 * since you can only return 1 array to the module, you have to wrap 
 * the tabs in an inputfield wrapper

Contacts

AT
08.09.25
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
		'name' => 'address',
		'label' => 'Адрес',
		'type' => 'InputfieldTextarea',
		'columnWidth' => 100,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'city',
		'label' => 'Город',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'region',
		'label' => 'Регион',
		'type' => 'InputfieldText',
		'columnWidth' => 33,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'postal_code',
		'label' => 'Почтовый индекс',
		'type' => 'InputfieldText',
		'columnWidth' => 34,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'metro',
		'label' => 'Метро',
		'type' => 'InputfieldText',
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
	[
		'name' => 'schedule',
		'label' => 'Режим работы',
		'type' => 'InputfieldTextarea',
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
];
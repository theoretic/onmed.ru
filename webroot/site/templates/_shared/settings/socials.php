<?
/**
 * since you can only return 1 array to the module, you have to wrap 
 * the tabs in an inputfield wrapper

Socials
AT
20.03.26
 */

namespace ProcessWire;

return [
	[
		'name' => 'whatsapp',
		'label' => 'Whatsapp',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'telegram',
		'label' => 'Telegram',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'max',
		'label' => 'Max',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'youtube',
		'label' => 'Youtube',
		'type' => 'InputfieldText',
		'columnWidth' => 25,
		//'required' => true,
		//'value' => '',
	],
];
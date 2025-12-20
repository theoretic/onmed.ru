<?
/**
 * since you can only return 1 array to the module, you have to wrap 
 * the tabs in an inputfield wrapper
Privacy
AT
28.08.25
 */

namespace ProcessWire;

return [
	[
		'name' => 'cookie_consent_text',
		'label' => 'Текст про cookes',
		'type' => 'InputfieldTextarea',
		'columnWidth' => 100,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
]; 
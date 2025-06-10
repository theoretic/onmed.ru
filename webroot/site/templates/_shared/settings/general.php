<?
/**
 * since you can only return 1 array to the module, you have to wrap 
 * the tabs in an inputfield wrapper

AT
23.11.23
 */

namespace ProcessWire;

return [
	[
		'name' => 'site_name',
		'label' => 'Название сайта',
		'type' => 'InputfieldText',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'logo',
		'label' => 'Ссылка на лого',
		'type' => 'InputfieldText',
		'columnWidth' => 50,
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
	[
		'name' => 'discounts',
		'label' => 'Информация о скидках',
		'type' => 'InputfieldTextarea',
		//'useLanguages' => true,
		//'required' => true,
		//'value' => '',
	],
]; 
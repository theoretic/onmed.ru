<?
/*
Nonempty validator
AT
14.11.23
*/

namespace ProcessWire;

//var_dump(I18N_VALIDATOR);//

if(!$value || $value=='')
	return [ 'error' => defined('I18N_VALIDATOR')? __('is empty',I18N_VALIDATOR) : 'пусто' ];
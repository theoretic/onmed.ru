<?
/*
Number validator
AT
14.11.23
*/

namespace ProcessWire;

if(!is_numeric($value))
	return [ 'error' => defined('I18N_VALIDATOR')? __('not a number',I18N_VALIDATOR) : 'не число' ];
<?
/*
Number validator
AT
14.11.23
*/

namespace ProcessWire;

if(!is_float($value))
	return [ 'error' => defined('I18N_VALIDATOR')? __('not a float number',I18N_VALIDATOR) : 'не float' ];
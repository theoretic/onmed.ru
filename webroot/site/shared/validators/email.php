<?
/*
Email validator
AT
14.11.23
*/

namespace ProcessWire;

if(!filter_var($value, FILTER_VALIDATE_EMAIL))
	return [ 'error' => defined('I18N_VALIDATOR')? __('incorrect',I18N_VALIDATOR) : 'некорректный' ];
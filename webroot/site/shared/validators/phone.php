<?
/*
Phone validator
AT
14.11.23
*/

namespace ProcessWire;

if(!preg_match("/^[\+]{0,}[0-9()\-\s]{6,}$/",$value))
	return [ 'error' => defined('I18N_VALIDATOR')? __('incorrect',I18N_VALIDATOR) : 'некорректный' ];
<?
/*
Password validator
AT
21.09.23
*/

namespace ProcessWire;

//2do: move $minPasswordLength to $settings
$minPasswordLength = 6;

if( strlen($value) < $minPasswordLength )
	return [ 'error' => defined('I18N_VALIDATOR')? sprintf( __('shorter than %d symbols',I18N_VALIDATOR), $minPasswordLength ) : sprintf('короче %d символов'), $minPasswordLength ) ];
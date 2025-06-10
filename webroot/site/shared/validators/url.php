<?
/*
URL validator
AT
14.11.23
*/

namespace ProcessWire;

if(!filter_var($value, FILTER_VALIDATE_URL))
	return [ 'error' => defined('I18N_VALIDATOR')? __('incorrect',I18N_VALIDATOR) : 'некорректный' ];

$header = @get_headers($value);
if( !strstr($header[0],'200') )
	return [ 'error' => defined('I18N_VALIDATOR')? __('unavailable',I18N_VALIDATOR) : 'недоступен' ];
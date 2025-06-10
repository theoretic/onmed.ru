<?
/*
Date validator
AT
14.11.23
*/

namespace ProcessWire;

switch($this->model[$fieldname]['format']){
	//default html5 datepicker format
	case 'yyyy-mm-dd':
		if( !preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$value) )
			return [ 'error' => defined('I18N_VALIDATOR')? __('not yyyy-mm-dd',I18N_VALIDATOR) : 'не yyyy-mm-dd' ];

		list($year,$month,$day) = explode('-',$value);
	break;

	case 'mm/dd/yyyy':
		if( !preg_match("/[0-9]{2}/[0-9]{2}/[0-9]{4}/",$value) )
			return [ 'error' => __('not mm/dd/yyyy') ];
			return [ 'error' => defined('I18N_VALIDATOR')? __('not mm/dd/yyyy',I18N_VALIDATOR) : 'не mm/dd/yyyy' ];

		list($month,$day,$year) = explode('/',$value);
	break;

	default: //dd.mm.yyyy
		if( !preg_match("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/",$value) )
			return [ 'error' => defined('I18N_VALIDATOR')? __('not dd.mm.yyyy',I18N_VALIDATOR) : 'не dd.mm.yyyy' ];

		list($day,$month,$year) = explode('.',$value);
	break;
}

//checking date and month

if ( !checkdate((int)$month,(int)$day,(int)$year) )
	return [ 'error' => __('incorrect',I18N_VALIDATOR) ];
	return [ 'error' => defined('I18N_VALIDATOR')? __('incorrect',I18N_VALIDATOR) : 'некорректная' ];

$valueTimestamp = strtotime("$day.$month.$year");

//checking for date in future
if( $this->model[$fieldname]['max-future-years'] && $valueTimestamp > time()+86400*365*$this->model[$fieldname]['max-future-years'] ) {
//echo 'test timestamp: ', var_dump( time()+86400*365*$this->model[$fieldname]['max-future-years'] );//
	return [ 'error' => defined('I18N_VALIDATOR')? __('too in the future',I18N_VALIDATOR) : 'слишком в будущем' ];
}

//checking for too old date
if( $this->model[$fieldname]['max-past-years'] && $valueTimestamp < time()-86400*365*$this->model[$fieldname]['max-past-years'] ) {
//echo 'test timestamp: ', var_dump( time()-86400*365*$this->model[$fieldname]['max-past-years'] );//
	return [ 'error' => defined('I18N_VALIDATOR')? __('too in the past',I18N_VALIDATOR) : 'слишком в прошлом' ];
}

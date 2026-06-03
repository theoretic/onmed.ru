<?
/*
Birthdate validator
Includes date.php validation, then checks:
  - age >= 17 years
  - age < 120 years
AT
03.06.26
*/

namespace ProcessWire;

// Run base date validation first
$dateError = include __DIR__ . '/date.php';
if( isset($dateError['error']) ) return $dateError;

// $valueTimestamp set by date.php via mktime()
$now = time();
//$age17  = strtotime('-17 years', $now);
$age120 = strtotime('-120 years', $now);

/*
if( $valueTimestamp > $age17 )
	return [ 'error' => defined('I18N_VALIDATOR')? __('age must be at least 17',I18N_VALIDATOR) : 'возраст должен быть не менее 17 лет' ];
*/


if( $valueTimestamp > time() )
	return [ 'error' => defined('I18N_VALIDATOR')? __('age must be at least 0',I18N_VALIDATOR) : 'дата рождения не может быть в будущем' ];

if( $valueTimestamp < $age120 )
	return [ 'error' => defined('I18N_VALIDATOR')? __('age must be less than 120',I18N_VALIDATOR) : 'возраст не может превышать 120 лет' ];

<?
/*
feedback model
AT
12.02.25
*/

return [
	'specialist'=>[
		'validate-as'=>'nonempty',
		],
	'rating'=>[
		'validate-as'=>'number',
		//'affectsCompleteness' => true,
		],
/*
	'title'=>[
		'validate-as'=>'nonempty',
		//'affectsCompleteness' => true,
		],
*/
	'summary'=>[
		'validate-as'=>'nonempty',
		//'affectsCompleteness' => true,
		],
	'author'=>[
		'validate-as'=>'nonempty',
		],
	'email'=>[
		'validate-if'=>'nonempty',
		'validate-as'=>'email',
		],
	'phone'=>[
		'validate-as'=>'phone',
		],
/*
	'accept-pdp'=>[
		'validate-as'=>'nonempty',
		],
*/
	];
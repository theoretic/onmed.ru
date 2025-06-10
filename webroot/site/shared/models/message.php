<?
/*
message model
AT
12.09.22
*/

return [
	'order'=>[
		'validate-as'=>'nonempty',
		//'affectsCompleteness' => true,
		],
	'recipient'=>[
		'validate-as'=>'nonempty',
		//'affectsCompleteness' => true,
		],
/*
	'implementer_price'=>[
		'validate-if'=>'nonempty',
		'validate-as'=>'number',
		'group'=>'сообщение и цена',
		//'affectsCompleteness' => true,
		],
*/
	'message'=>[
		'validate-if'=>'nonempty',
		'validate-as'=>'nonempty',
		//'group'=>'сообщение и цена',
		//'affectsCompleteness' => true,
		],
	];
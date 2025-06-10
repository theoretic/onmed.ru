<?
/*
appointment model
AT
12.11.24
*/

return [
	'fullname'=>[
		'validate-as'=>'nonempty',
	],
/*
	'email'=>[
		'validate-if'=>'nonempty',
		'validate-as'=>'email',
	],
*/
	'phone'=>[
		'validate-as'=>'phone',
	],
	'message'=>[
		'validate-if'=>'nonempty',
		'validate-as'=>'nonempty',
		//'group'=>'сообщение и цена',
		//'affectsCompleteness' => true,
	],
];
<?
/*
window.data javascript vars
AT
09.08.23
*/

$data = [
	'language' => $user->language->title->getLanguageValue('default'),

	'csrf'	=> [
		'name'	=> $session->CSRF->getTokenName(),
		'value'	=> $session->CSRF->getTokenValue()
		],
	];

//userfiles

//$data['userfiles'] = include DOCUMENT_ROOT . "/api/userfiles/get.php";

return $data;
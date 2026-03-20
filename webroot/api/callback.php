<?
/*
Callback
AT
10.03.26
*/

namespace ProcessWire;

//robot / spam protection
if( $input->post->consent == 1){
	return [ 'success' => 'Большое спасибо!! Мы постараемся перезвонить как можно скорее.', ];
	die();
}

if( !$referer->page->id || $referer->page->id === null ){
	return [ 'success' => 'Большое спасибо!!! Мы постараемся перезвонить как можно скорее.', ];
	die();
}

//echo '$input->post: ', var_dump($input->post);

//include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/email.php";

//validating input
$model = include "{$_SERVER['DOCUMENT_ROOT']}/site/shared/models/callback.php";

$validator = new \Validator();
$validator->input = $input->post;
$validator->model = $model;
$validation = $validator->validate();

//echo '$validation: ', var_dump($validation);//

if( !$validation['success'] )
	return [ 'error' => 'Некоторые поля заполнены неверно.' ];

//sending mail
$emailData = [
	//'to'			=> 'tarasov.alexei@gmail.com',//tmp!
	//'to'			=> $settings->contacts->private_email,
	'to'			=> ENV=='dev'? 'tarasov.alexei@gmail.com' : $settings->contacts->private_email,
	'subject'		=> "{$settings->general->site_name}: {$input->post->author} просит перезвонить",
	'html'			=> $files->render( '_shared/email/callback.php', [ 'settings' => $settings, 'input' => $input, ] ),
	];

//echo '$emailData: ', var_dump($emailData);
$emailResult = email( $emailData );
//echo '$emailResult: ', var_dump($emailResult);

//return success
return [ 'success' => 'Большое спасибо! Мы постараемся перезвонить как можно скорее.', ];
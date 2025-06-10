<?
/*
Appointment (email)
AT
28.10.24
*/

namespace ProcessWire;

//echo '$input->post: ', var_dump($input->post);

//include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/email.php";

//validating input
$model = include "{$_SERVER['DOCUMENT_ROOT']}/site/shared/models/appointment.php";

$validator = new \Validator();
$validator->input = $input->post;
$validator->model = $model;
$validation = $validator->validate();

//echo '$validation: ', var_dump($validation);//

if( !$validation['success'] )
	return [ 'error' => 'Некоторые поля заполнены неверно.' ];

//getting offer page
$offerPage = $input->post->offer? $pages->get($input->post->offer) : $referer->page;

//sending mail
$emailData = [
	//'to'			=> 'tarasov.alexei@gmail.com',//tmp!
	'to'			=> $settings->contacts->private_email,
	'subject'		=> "{$settings->general->site_name}: {$input->post->fullname}, запись на приём",
	'html'			=> $files->render( '_shared/email/appointment.php', [ 'settings' => $settings, 'input' => $input, 'offerPage' => $offerPage, ] ),
	];

//echo '$emailData: ', var_dump($emailData);
$emailResult = email( $emailData );
//echo '$emailResult: ', var_dump($emailResult);

//return success
return [ 'success' => 'Большое спасибо, что записались к нам! Мы свяжемся с Вами для уточнения деталей.', ];
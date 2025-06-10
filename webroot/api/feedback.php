<?
/*
Feedback
AT
12.02.25
*/

namespace ProcessWire;

//echo '$input->post: ', var_dump($input->post);

//include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/email.php";

//validating input
$model = include "{$_SERVER['DOCUMENT_ROOT']}/site/shared/models/feedback.php";

$validator = new \Validator();
$validator->input = $input->post;
$validator->model = $model;
$validation = $validator->validate();

//echo '$validation: ', var_dump($validation);//

if( !$validation['success'] )
	return [ 'error' => 'Некоторые поля заполнены неверно.' ];

//looking for specialist page
$specialistPage = $pages->get($input->post->specialist);
if( !$specialistPage->id )
	return [ 'error' => 'Специалист не найден.' ];

////

//looking for specialist feedbacks
$specialistFeedbacksPage = $specialistPage->get('name=feedbacks');
if( !$specialistFeedbacksPage->id ){
	//creating new feedbacks page
	$specialistFeedbacksPage = new Page();
	$specialistFeedbacksPage->template = 'empty';
	$specialistFeedbacksPage->title = 'Отзывы';
	$specialistFeedbacksPage->name = 'feedbacks';
	$specialistFeedbacksPage->parent = $specialistPage;
	$specialistFeedbacksPage->save();
}

//creating feedback page
$newFeedbackPage = new Page();
$newFeedbackPage->template = 'feedback';
//$newFeedbackPage->parent = $pages->get('template=feedbacks');
$newFeedbackPage->parent = $specialistFeedbacksPage;

foreach( $input->post as $name=>$value ){
	switch( $name ){
		case 'specialist':
		break;

		default:
//echo '$value: ', var_dump($value);//
			$newFeedbackPage->$name = $value;
	}
}

$words = explode( ' ', $newFeedbackPage->summary );
$title = array_slice( $words, 0, 4 );
$title = implode( ' ', $title );
$title .= '...';

$newFeedbackPage->title = $title;
$newFeedbackPage->status = Page::statusUnpublished;
$newFeedbackPage->save();

//sending mail
$emailData = [
	//'to'			=> 'tarasov.alexei@gmail.com',//tmp!
	'to'			=> $settings->contacts->private_email,
	'subject'		=> "{$settings->general->site_name}: {$input->post->author} написал(а) новый отзыв",
	'html'			=> $files->render( '_shared/email/feedback.php', [ 'settings' => $settings, 'input' => $input, 'newFeedbackPage' => $newFeedbackPage, ] ),
	];

//echo '$emailData: ', var_dump($emailData);
$emailResult = email( $emailData );
//echo '$emailResult: ', var_dump($emailResult);


//return success
return [ 'success' => 'Большое спасибо, что уделили нам время! Ваш отзыв будет опубликован после модерации.', ];
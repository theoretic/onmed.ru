<?
/*
Appointment-specialist (email skipped, medflex API call only)

Medflex API POST request structure:

fetch('https://api.medflex.ru/direct_appointment/doctor/execute/', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    Authorization: 'Token '
  },
  body: JSON.stringify({
    doctor: {
      id: 1,
      lpu_id: 10,
      speciality_id: 100
    },
    appointment: {
      dt_start: '2024-04-16 11:00',
      dt_end: '2024-04-16 11:30',
      comment: 'Комментарий к приему',
      price: 1000
    },
    client: {
      first_name: 'Иван',
      second_name: 'Иванович',
      last_name: 'Иванов',
      mobile_phone: '79008007060',
      birthday: '2000-01-01'
    },
    call_trackings: {
      uis_id: '1234567890.12345678901.1234567890'
    }
  })
})

Medflex API response structure:

{
  "claim_id": "d1c060a0-8375-4ff9-bce5-9bb03029256f"
}

AT
06.05.26
*/

namespace ProcessWire;

$skipCSRF = true; // Disable CSRF protection for this endpoint

//echo '$input->post: ', var_dump($input->post);

//include_once "{$_SERVER['DOCUMENT_ROOT']}/site/shared/functions/email.php";

//validating input
$model = include "{$_SERVER['DOCUMENT_ROOT']}/site/shared/models/medflex/appointment-specialist.php";

$validator = new \Validator();
$validator->input = $input->post;
$validator->model = $model;
$validation = $validator->validate();

//echo '$validation: ', var_dump($validation);//

if( !$validation['success'] )
	return [ 'error' => 'Некоторые поля заполнены неверно.' ];

//can be called only from specialist page with valid id_medflex
if( !$referer->page || $referer->page->template != 'specialist' || !$referer->page->id_medflex ) {
	header("HTTP/1.1 403 Forbidden");
	return [
		'error' => 'Forbidden',
		'message' => 'This endpoint can only be accessed from the specialist page'
	];
}

////

//perform API request
$apiKey = $settings->medflex->api_key;
$apiUrl = "https://api.medflex.ru/direct_appointment/doctor/execute/";

require_once __DIR__ . '/_include/cache.php';

$payload = [
	'doctor' => [
		'id' => (int)$input->post->doctor_id,
		'lpu_id' => 37721, // hardcoded for now
		'speciality_id' => (int)$input->post->service_id,
	],
	'appointment' => [
		'dt_start' => $input->post->start_time,
		'dt_end' => $input->post->end_time ?? date('Y-m-d H:i', strtotime($input->post->start_time) + 30 * 60), // 30 min slot
		'comment' => $input->post->comment,
		'price' => $input->post->price,
	],
	'client' => [
		'first_name' => $input->post->first_name,
		'second_name' => $input->post->second_name,
		'last_name' => $input->post->last_name,
		'mobile_phone' => ltrim($input->post->mobile_phone, '+'),
		'birthday' => date('Y-m-d', strtotime($input->post->birthday)),
	],
];

$apiResponse = medflex_api_post($apiUrl, $apiKey, $payload);

//echo '$payload: ', var_dump($payload);//
//echo '$apiResponse: ', var_dump($apiResponse);//

//return error if API returned error message
if( !isset($apiResponse['claim_id']) ) {
	header("HTTP/1.1 400 Bad Request");
	return [
		'error' => "Не удалось записаться на приём. Пожалуйста, попробуйте ещё раз или позвоните нам по телефону.",
		//'message' => json_encode($apiResponse)
	];
}

//return success
return [ 'success' => 'Большое спасибо, что записались к нам! Мы свяжемся с Вами для уточнения деталей.' ];

/*
//getting offer page
$offerPage = $input->post->offer? $pages->get($input->post->offer) : $referer->page;

//sending mail
$emailData = [
	//'to'			=> 'tarasov.alexei@gmail.com',//tmp!
	'to'			=> $settings->contacts->private_email,
	'subject'		=> "{$settings->general->site_name}: {$input->post->full_name}, запись на приём",
	'html'			=> $files->render( '_shared/email/appointment-specialist.php', [ 'settings' => $settings, 'input' => $input, 'offerPage' => $offerPage, ] ),
	];

//echo '$emailData: ', var_dump($emailData);
$emailResult = email( $emailData );
//echo '$emailResult: ', var_dump($emailResult);

//return success
return [ 'success' => 'Большое спасибо, что записались к нам! Мы свяжемся с Вами для уточнения деталей.', ];
*/
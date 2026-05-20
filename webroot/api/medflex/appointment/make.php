<?
/*
Appointment make (moved from appointment-specialist.php)

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
15.05.26
*/

namespace ProcessWire;

$skipCSRF = true; // Disable CSRF protection for this endpoint

// Prevent Beget's max_execution_time from killing PHP mid-cURL.
// CURLOPT_TIMEOUT=15 governs the timeout; PHP just needs to survive that long.
set_time_limit(0);
// Keep processing even if iOS Safari (or any client) closes the connection
// before we finish — ensures the Medflex booking still completes and is logged.
ignore_user_abort(true);

// Logger first — must capture every POST attempt that reaches this file,
// even if downstream model load / validation throws. Without this, an iOS
// Safari user whose form silently fails will leave no trace at all.
require_once __DIR__ . '/../_include/logger.php';
MedflexLogger::log('appointment-make', (array)$input->post, null, [
	'http_code'        => 'received',
	'response_headers' => [],
	'response_body'    => 'request received — processing',
	'curl_error'       => '',
]);

//validating input
$model = include "{$_SERVER['DOCUMENT_ROOT']}/site/shared/models/medflex/appointment/make.php";

// Checkpoint: model loaded. If this line is the LAST one in the log for a
// given attempt, the validator class load / instantiation is what dies.
MedflexLogger::log('appointment-make', [], null, [
	'http_code'        => 'checkpoint',
	'response_headers' => [],
	'response_body'    => 'model loaded — instantiating validator',
	'curl_error'       => '',
]);

$validator = new \Validator();
$validator->input = $input->post;
$validator->model = $model;
$validation = $validator->validate();

// Checkpoint: validation complete. Always log the result (success or failure)
// so we can distinguish "validator returned false" from "PHP died inside the
// validator".
MedflexLogger::log('appointment-make', [], null, [
	'http_code'        => 'checkpoint',
	'response_headers' => [],
	'response_body'    => 'validation done — ' . json_encode($validation, JSON_UNESCAPED_UNICODE),
	'curl_error'       => '',
]);

if( !$validation['success'] ) {
	// Log raw POST even on validation failure — critical for catching
	// malformed iOS Safari autofill values that never reach the API.
	MedflexLogger::log('appointment-make', (array)$input->post, null, [
		'http_code'        => 'N/A — validation failed',
		'response_headers' => [],
		'response_body'    => json_encode($validation),
		'curl_error'       => '',
	]);
	return [ 'error' => 'Некоторые поля заполнены неверно.' ];
}

////

//perform API request
$apiKey = $settings->medflex->api_key;
$apiUrl = "https://api.medflex.ru/direct_appointment/doctor/execute/";

require_once __DIR__ . '/../_include/medflex.php';
Medflex::corsHeaders();

// Checkpoint: about to build payload and call Medflex. If this is the LAST
// entry, the failure is in apiPost — most likely a stalled upstream now
// caught by the new CURLOPT_TIMEOUT (15s).
MedflexLogger::log('appointment-make', [], null, [
	'http_code'        => 'checkpoint',
	'response_headers' => [],
	'response_body'    => 'validation OK — calling Medflex API',
	'curl_error'       => '',
]);

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
		// Strip every non-digit; normalize to 7XXXXXXXXXX format.
		// 10-digit → prepend 7; 11-digit starting with 8 → replace 8 with 7 (old Russian format).
		'mobile_phone' => (function($v) {
			$v = preg_replace('/\D/', '', (string)$v);
			if (strlen($v) === 10) $v = '7' . $v;
			elseif (strlen($v) === 11 && $v[0] === '8') $v = '7' . substr($v, 1);
			return $v;
		})($input->post->mobile_phone),
		// Explicit DD.MM.YYYY → YYYY-MM-DD parse. strtotime() is locale/format
		// dependent and silently misinterprets ISO/US autofill formats.
		'birthday' => (function($v) {
			$v = trim((string)$v);
			if( preg_match('/^(\d{1,2})[.\/-](\d{1,2})[.\/-](\d{4})$/', $v, $m) )
				return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
			if( preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $v, $m) )
				return sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
			return $v;
		})($input->post->birthday),
	],
];

$apiMeta = [];
try {
	$apiResponse = Medflex::apiPost($apiUrl, $apiKey, $payload, $apiMeta);
} catch (\Throwable $e) {
	MedflexLogger::log('appointment-make', [], null, [
		'http_code'        => 0,
		'response_headers' => [],
		'response_body'    => 'apiPost EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
		'curl_error'       => '',
	]);
	header("HTTP/1.1 500 Internal Server Error");
	return ['error' => 'Не удалось записаться на приём. Пожалуйста, попробуйте ещё раз или позвоните нам по телефону.'];
}

// Minimal safe post-apiPost log: contains only scalars guaranteed not to
// crash the logger. If the rich log below ever fails again, this still
// proves apiPost returned and gives us the http_code + curl_error.
MedflexLogger::log('appointment-make', [], null, [
	'http_code'        => $apiMeta['http_code'] ?? 'null',
	'response_headers' => [],
	'response_body'    => 'apiPost returned (checkpoint) — curl_error="'
		. ($apiMeta['curl_error'] ?? '') . '"',
	'curl_error'       => $apiMeta['curl_error'] ?? '',
]);

MedflexLogger::log(
    'appointment-make',
    (array)$input->post,
    $payload,
    $apiMeta
);

//return error if API returned error message
if( !isset($apiResponse['claim_id']) ) {
	header("HTTP/1.1 400 Bad Request");
	return [
		'error' => "Не удалось записаться на приём. Пожалуйста, попробуйте ещё раз или позвоните нам по телефону.",
	];
}

$claimId = htmlspecialchars((string)$apiResponse['claim_id'], ENT_QUOTES | ENT_HTML5, 'UTF-8');

//return success — embed claim_id in HTML so the frontend cancel button can read it
return [
	'success' => 'Большое спасибо, что записались к нам! Мы свяжемся с Вами для уточнения деталей.'
		. '<span class="as-claim-id" data-id="' . $claimId . '" hidden></span>',
];

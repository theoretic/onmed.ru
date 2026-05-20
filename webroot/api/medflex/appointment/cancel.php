<?
/*
Appointment cancel

Medflex API POST request structure:

$ch = curl_init("https://api.medflex.ru/direct_appointment/doctor/cancel/");

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Token ']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
  'uuid' => ''
]));

curl_exec($ch);
curl_close($ch);

AT
19.05.26
*/

namespace ProcessWire;

$skipCSRF = true;

set_time_limit(0);
ignore_user_abort(true);

require_once __DIR__ . '/../_include/medflex.php';
require_once __DIR__ . '/../_include/logger.php';

Medflex::corsHeaders();

$claimId = trim((string)$input->post->claim_id);

if (!$claimId) {
	header("HTTP/1.1 400 Bad Request");
	return ['error' => 'Не указан идентификатор записи.'];
}

$apiKey = $settings->medflex->api_key;
$apiUrl = "https://api.medflex.ru/direct_appointment/doctor/cancel/";

$payload = ['uuid' => $claimId];

$apiMeta = [];
try {
	$apiResponse = Medflex::apiPost($apiUrl, $apiKey, $payload, $apiMeta);
} catch (\Throwable $e) {
	MedflexLogger::log('appointment-cancel', [], null, [
		'http_code'        => 0,
		'response_headers' => [],
		'response_body'    => 'apiPost EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
		'curl_error'       => '',
	]);
	header("HTTP/1.1 500 Internal Server Error");
	return ['error' => 'Не удалось отменить запись. Пожалуйста, позвоните нам по телефону.'];
}

MedflexLogger::log('appointment-cancel', ['claim_id' => $claimId], $payload, $apiMeta);

if ($apiMeta['http_code'] < 200 || $apiMeta['http_code'] >= 300) {
	header("HTTP/1.1 400 Bad Request");
	return ['error' => 'Не удалось отменить запись. Пожалуйста, позвоните нам по телефону.'];
}

return ['success' => 'Запись успешно отменена.'];
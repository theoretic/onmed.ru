<?php
/*
Medflex API proxy
schedule endpoint

Medflex API schedule endpoint documentation:
https://developer.medflex.ru/clinic-site/tag/schedule/get_doctors_schedule_v2

Proxies: GET https://api.medflex.ru/schedule/?doctor_ids={id_medflex}
Doctor ID sourced from ?doctor_id param or $referer->page->id_medflex.
Always fetches live from Medflex API.
Response written to cache file as a log (never read back).

AT
07.05.26
*/

namespace ProcessWire;

$skipCSRF = true; // Disable CSRF protection for this endpoint

/*
// Only accessible from specialist pages
if( !$referer->page || $referer->page->template != 'specialist' ) {
	header("HTTP/1.1 403 Forbidden");
	return [
		'error' => 'Forbidden',
		'message' => 'This endpoint can only be accessed from the doctor page'
	];
}
*/

$doctorId = isset($_GET['doctor_id']) ? trim($_GET['doctor_id']) : '';
if( !$doctorId && $referer->page ) $doctorId = $referer->page->id_medflex;

if( !$doctorId ) {
	header("HTTP/1.1 400 Bad Request");
	return [
		'error' => 'Bad Request',
		'message' => 'doctor_id is required'
	];
}

require_once __DIR__ . '/_include/medflex.php';
Medflex::corsHeaders();

// town_id is required by API, but we have only one town, so hardcoding it for now
$apiKey = $settings->medflex->api_key;
$dateStart = date('Y-m-d');
$dateEnd = date('Y-m-d', strtotime('+1 month'));

$apiUrl = "https://api.medflex.ru/schedule/?town_id=1261&doctor_ids=$doctorId&date_start=$dateStart&date_end=$dateEnd";
$warnings = [];
$result = Medflex::fetchAllPages($apiUrl, $apiKey, $warnings);

if( $result === null ) {
    header("HTTP/1.1 502 Bad Gateway");
    return [
        'error' => 'API Error',
        'message' => 'Failed to fetch schedule from Medflex API'
    ];
}

// Write to cache as log — never read back
$cacheKey = "schedule_{$doctorId}_{$dateStart}_{$dateEnd}";
Medflex::cacheSet($cacheKey, $result, 0);

if( !empty($warnings) ) {
    header("HTTP/1.1 206 Partial Content");
    return array_merge($result, [
        'warning' => 'Расписание загружено частично. Возможны пропуски в расписании.'
    ]);
}

return $result;

<?php
/*
Medflex API proxy
schedule endpoint

Medflex API schedule endpoint documentation:
https://developer.medflex.ru/clinic-site/tag/schedule/get_doctors_schedule_v2

Proxies: GET https://api.medflex.ru/schedule/?doctor_ids={id_medflex}
Doctor ID sourced from $referer->page->id_medflex (set by ProcessWire specialist page).

Cache: filesystem, TTL 1h, key: schedule_{id_medflex}_{dateStart}_{dateEnd}
Stale cache served on API error.

AT
05.05.26
*/

namespace ProcessWire;

$skipCSRF = true; // Disable CSRF protection for this endpoint

// Only accessible from specialist pages
if( !$referer->page || $referer->page->template != 'specialist' ) {
	header("HTTP/1.1 403 Forbidden");
	return [
		'error' => 'Forbidden',
		'message' => 'This endpoint can only be accessed from the doctor page'
	];
}

// Getting id_medflex from the doctor page
$id_medflex = $referer->page->id_medflex;

if( !$id_medflex ) {
	header("HTTP/1.1 400 Bad Request");
	return [
		'error' => 'Bad Request',
		'message' => 'doctor_id is required'
	];
}

require_once __DIR__ . '/_include/cache.php';

// town_id is required by API, but we have only one town, so hardcoding it for now
$apiKey = $settings->medflex->api_key;
$dateStart = date('Y-m-d');
$dateEnd = date('Y-m-d', strtotime('+1 month'));
$cacheKey = "schedule_{$id_medflex}_{$dateStart}_{$dateEnd}";

$cached = medflex_cache_get($cacheKey);
if( $cached !== null ) return $cached;

$apiUrl = "https://api.medflex.ru/schedule/?town_id=1261&doctor_ids=$id_medflex&date_start=$dateStart&date_end=$dateEnd";
$warnings = [];
$result = medflex_fetch_all_pages($apiUrl, $apiKey, $warnings);

if( $result === null ) {
	$stale = medflex_cache_get_stale($cacheKey);
	if( $stale !== null ) return $stale;
	header("HTTP/1.1 502 Bad Gateway");
	return [
		'error' => 'API Error',
		'message' => 'Failed to fetch schedule from Medflex API'
	];
}

if( !empty($warnings) ) {
	// Partial data — do not cache, return HTTP 206 with warning
	header("HTTP/1.1 206 Partial Content");
	return array_merge($result, [
		'warning' => 'Расписание загружено частично. Возможны пропуски в расписании.'
	]);
}

medflex_cache_set($cacheKey, $result, 3600);
return $result;

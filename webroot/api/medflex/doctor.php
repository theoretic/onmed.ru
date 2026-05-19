<?php
/*
Medflex API proxy
doctor endpoint

Medflex API doctor endpoint documentation:
https://developer.medflex.ru/clinic-site/tag/models/get_doctors
API key: $settings->medflex->api_key

cURL request example:
curl https://api.medflex.ru/models/doctor/ \
  --header 'Authorization: Token '

Response structure: https://developer.medflex.ru/clinic-site/tag/models/get_doctors#response-example

?doctor_id={id} — explicit doctor ID (overrides page id_medflex).
If omitted, falls back to $referer->page->id_medflex.
If neither provided, fetches and caches all doctors — intended for cron.

Cache strategy:
  - No id: key doctor_all, TTL 12h. Fetches all pages via chained requests.
  - Id set: key doctor_{id}, TTL 12h.
    Lookup order: doctor_{id} cache → extract from doctor_all cache → fetch all doctors.
    Fetching all doctors always populates both doctor_all and doctor_{id} caches.
Stale cache served on API error.

AT
05.05.26
*/

namespace ProcessWire;

$skipCSRF = true; // Disable CSRF protection for this endpoint

// Resolve doctor ID: explicit param takes priority, then page field
$id_medflex = isset($_GET['doctor_id']) ? trim($_GET['doctor_id']) : '';

if( !$id_medflex && $referer->page ) {
	$id_medflex = $referer->page->id_medflex;
}

////

require_once __DIR__ . '/_include/medflex.php';
require_once __DIR__ . '/_include/logger.php';
Medflex::corsHeaders();
MedflexLogger::log('doctor', $_GET);
$apiKey = $settings->medflex->api_key;

// --- No id: return all doctors ---

if( !$id_medflex ) {
    $allKey = 'doctor_all';

    // ?cached=1 — serve from filesystem cache immediately, skip live fetch; fall through on miss
    if( isset($_GET['cached']) ) {
        $cached = Medflex::cacheGet($allKey);
        if( $cached !== null ) return $cached;
    }

    $cached = Medflex::cacheGet($allKey);
    if( $cached !== null ) return $cached;

    $warnings = [];
    $result = Medflex::fetchAllPages('https://api.medflex.ru/models/doctor/', $apiKey, $warnings);

    if( $result === null ) {
        $stale = Medflex::cacheGetStale($allKey);
        if( $stale !== null ) return $stale;
        header("HTTP/1.1 502 Bad Gateway");
        return [
            'error' => 'API Error',
            'message' => 'Failed to fetch all doctors from Medflex API'
        ];
    }

    Medflex::cacheSet($allKey, $result, 12 * 3600);
    return $result;
}

// --- Id set: single doctor ---

$singleKey = "doctor_{$id_medflex}";

// 1. Single doctor cache
$cached = Medflex::cacheGet($singleKey);
if( $cached !== null ) return $cached;

// 2. Extract from all-doctors cache
$allData = Medflex::cacheGet('doctor_all');
if( $allData !== null && !empty($allData['data']) ) {
    foreach( $allData['data'] as $doc ) {
        if( (string)($doc['id'] ?? '') === (string)$id_medflex ) {
            $extracted = ['data' => [$doc]];
            Medflex::cacheSet($singleKey, $extracted, 12 * 3600);
            return $extracted;
        }
    }
}

// 3. Fallback: fetch all doctors, populate doctor_all + doctor_{id} caches
$warnings = [];
$allResult = Medflex::fetchAllPages('https://api.medflex.ru/models/doctor/', $apiKey, $warnings);

if( $allResult === null ) {
    $stale = Medflex::cacheGetStale($singleKey);
    if( $stale !== null ) return $stale;
    header("HTTP/1.1 502 Bad Gateway");
    return [
        'error' => 'API Error',
        'message' => 'Failed to fetch doctor data from Medflex API'
    ];
}

Medflex::cacheSet('doctor_all', $allResult, 12 * 3600);

$extracted = null;
foreach( $allResult['data'] ?? [] as $doc ) {
    if( (string)($doc['id'] ?? '') === (string)$id_medflex ) {
        $extracted = ['data' => [$doc]];
        break;
    }
}

if( $extracted === null ) {
    // Doctor not found in full list — return empty result
    $extracted = ['data' => []];
}

Medflex::cacheSet($singleKey, $extracted, 12 * 3600);
return $extracted;

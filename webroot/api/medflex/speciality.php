<?php
/*
Medflex API proxy
speciality endpoint

Medflex API speciality endpoint documentation:
https://developer.medflex.ru/clinic-site/tag/models/get_doctor_specialities
API key: $settings->medflex->api_key

Proxies: GET https://api.medflex.ru/models/speciality/
Returns global list of all clinic specialities (not doctor-specific).

Optional: ?doctor_id={id} or $referer->page->id_medflex — filters to doctor's specialities.

Cache strategy:
  - Global list: key speciality_global, TTL 12h
  - Per-doctor filtered list: key speciality_doctor_{id}, TTL 12h
  - Cache always used (read + write).
  - When doctor_id set: speciality_doctor_{id} → speciality_global → external API.
  - Doctor data lookup: doctor_{id} → doctor_all → external API (filtered).
Stale cache served on API error.

AT
06.05.26
*/

namespace ProcessWire;

$skipCSRF = true; // Disable CSRF protection for this endpoint

require_once __DIR__ . '/_include/medflex.php';
Medflex::corsHeaders();

$doctorId = isset($_GET['doctor_id']) ? trim($_GET['doctor_id']) : '';
if( !$doctorId && $referer->page ) $doctorId = $referer->page->id_medflex;

$apiKey = $settings->medflex->api_key;

// --- doctor_id provided: try per-doctor cache first ---

if( $doctorId ) {
    $doctorCacheKey = "speciality_doctor_{$doctorId}";
    $cached = Medflex::cacheGet($doctorCacheKey);
    if( $cached !== null ) return $cached;
}

// --- Global speciality list: cache → external API ---

$globalKey = 'speciality_global';
$globalData = Medflex::cacheGet($globalKey);

if( $globalData === null ) {
    $warnings = [];
    $result = Medflex::fetchAllPages('https://api.medflex.ru/models/speciality/', $apiKey, $warnings);

    if( $result === null ) {
        $stale = Medflex::cacheGetStale($globalKey);
        if( $stale !== null ) {
            $globalData = $stale;
        } else {
            header("HTTP/1.1 502 Bad Gateway");
            return [
                'error' => 'API Error',
                'message' => 'Failed to fetch specialities from Medflex API'
            ];
        }
    } else {
        Medflex::cacheSet($globalKey, $result, 12 * 3600);
        $globalData = $result;
    }
}

// --- No doctor_id: return global list ---

if( !$doctorId ) return $globalData;

// --- Resolve doctor data: doctor_{id} cache → doctor_all cache → external API ---

$doctorData = Medflex::cacheGet("doctor_{$doctorId}");

if( $doctorData === null ) {
    $allDoctors = Medflex::cacheGet('doctor_all');
    if( $allDoctors !== null && !empty($allDoctors['data']) ) {
        foreach( $allDoctors['data'] as $doc ) {
            if( (string)($doc['id'] ?? '') === (string)$doctorId ) {
                $doctorData = ['data' => [$doc]];
                Medflex::cacheSet("doctor_{$doctorId}", $doctorData, 12 * 3600);
                break;
            }
        }
    }
}

if( $doctorData === null ) {
    $dWarnings = [];
    $doctorResult = Medflex::fetchAllPages(
        "https://api.medflex.ru/models/doctor/?doctor_ids=$doctorId",
        $apiKey,
        $dWarnings
    );
    if( $doctorResult !== null ) {
        Medflex::cacheSet("doctor_{$doctorId}", $doctorResult, 12 * 3600);
        $doctorData = $doctorResult;
    }
}

if( $doctorData === null || empty($doctorData['data']) ) {
	// Cannot resolve doctor — return full global list as fallback
	return $globalData;
}

$doctorEntry = $doctorData['data'][0] ?? null;
$specialityIds = isset($doctorEntry['specialities']) ? array_map('intval', $doctorEntry['specialities']) : [];

if( empty($specialityIds) ) return $globalData;

// Filter global speciality list
$filtered = array_values(array_filter(
	$globalData['data'] ?? [],
	fn($s) => in_array((int)$s['id'], $specialityIds, true)
));

$filteredResult = ['data' => $filtered];
Medflex::cacheSet($doctorCacheKey, $filteredResult, 12 * 3600);
return $filteredResult;

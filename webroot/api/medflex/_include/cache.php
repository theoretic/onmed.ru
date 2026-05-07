<?php
/*
Medflex API proxy — filesystem cache helper

Cache dir: {site}/assets/cache/medflex/
Cache file: md5(key).json  →  { "expires_at": <unix>, "data": <payload> }

Functions:
  medflex_cache_get($key)        — returns data if cached and not expired, else null
  medflex_cache_get_stale($key)  — returns data regardless of expiry (for fallback on API error)
  medflex_cache_set($key, $data, $ttl) — writes cache entry
  medflex_fetch_all_pages($url, $apiKey, &$warnings) — GET, paginates, merges data[]
  medflex_api_post($url, $apiKey, $payload) — single POST, returns decoded array or null on error

AT
06.05.26
*/

namespace ProcessWire;

function _medflex_cache_dir(): string {
	return wire('config')->paths->cache . 'medflex/';
}

function _medflex_cache_path(string $key): string {
	return _medflex_cache_dir() . md5($key) . '.json';
}

function medflex_cache_get(string $key): mixed {
	$file = _medflex_cache_path($key);
	if (!file_exists($file)) return null;
	$payload = json_decode(file_get_contents($file), true);
	if (!$payload || !isset($payload['expires_at'], $payload['data'])) return null;
	if (time() > $payload['expires_at']) return null; // expired
	return $payload['data'];
}

function medflex_cache_get_stale(string $key): mixed {
	$file = _medflex_cache_path($key);
	if (!file_exists($file)) return null;
	$payload = json_decode(file_get_contents($file), true);
	return $payload['data'] ?? null;
}

/**
 * Fetches all pages from a paginated Medflex API endpoint and merges results.
 *
 * @param string $baseUrl   Full URL for page 1 (no &page= param).
 * @param string $apiKey    Medflex API token.
 * @param array  $warnings  Output — populated with warning strings on partial failure.
 * @return array|null       ['data' => [...merged...]] on success (possibly partial),
 *                          null if page 1 itself fails (caller should use stale cache).
 */
function medflex_fetch_all_pages(string $baseUrl, string $apiKey, array &$warnings = []): array|null {
	$fetchPage = function(string $url) use ($apiKey): array|null {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Token $apiKey",
			"Accept: application/json"
		]);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if( $httpCode != 200 ) return null;
		return json_decode($response, true);
	};

	// Page 1
	$page1 = $fetchPage($baseUrl);
	if( $page1 === null ) return null;

	$merged = isset($page1['data']) && is_array($page1['data']) ? $page1['data'] : [];
	$numPages = isset($page1['num_pages']) ? (int) $page1['num_pages'] : 1;

	// Remaining pages
	$separator = str_contains($baseUrl, '?') ? '&' : '?';
	for( $p = 2; $p <= $numPages; $p++ ) {
		$pageData = $fetchPage($baseUrl . $separator . "page=$p");
		if( $pageData === null ) {
			$warnings[] = "Не удалось загрузить страницу $p из $numPages.";
			break; // stop on first failure, return partial
		}
		if( isset($pageData['data']) && is_array($pageData['data']) ) {
			$merged = array_merge($merged, $pageData['data']);
		}
	}

	return ['data' => $merged];
}

function medflex_cache_set(string $key, mixed $data, int $ttl): void {
	$dir = _medflex_cache_dir();
	if (!is_dir($dir)) mkdir($dir, 0755, true);
	file_put_contents(
		_medflex_cache_path($key),
		json_encode(['expires_at' => time() + $ttl, 'data' => $data])
	);
}

/**
 * Makes a single POST request to a Medflex API endpoint.
 *
 * @param string $url      Full endpoint URL.
 * @param string $apiKey   Medflex API token.
 * @param array  $payload  Data to JSON-encode and send as request body.
 * @return array|null      Decoded response array, or null on HTTP error / curl failure.
 */
function medflex_api_post(string $url, string $apiKey, array $payload): array|null {
	$body = json_encode($payload);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Token $apiKey",
		"Content-Type: application/json",
		"Accept: application/json",
	]);
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

//echo '$response: ', var_dump($response);//
//echo '$httpCode: ', var_dump($httpCode);//

	if( $httpCode < 200 || $httpCode >= 300 ) return null;
	return json_decode($response, true);
}

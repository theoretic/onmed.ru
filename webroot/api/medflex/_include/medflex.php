<?php
/*
Medflex API proxy — shared helpers

Cache dir: {site}/assets/cache/medflex/
Cache file: md5(key).json  →  { "expires_at": <unix>, "data": <payload> }

Usage (call from ProcessWire namespace context):
  Medflex::corsHeaders()
  Medflex::cacheGet($key)
  Medflex::cacheGetStale($key)
  Medflex::cacheSet($key, $data, $ttl)
  Medflex::fetchAllPages($url, $apiKey, $warnings)
  Medflex::apiPost($url, $apiKey, $payload)

AT
07.05.26
*/

namespace ProcessWire;

class Medflex {

    // -------------------------------------------------------------------------
    // CORS
    // -------------------------------------------------------------------------

    public static function corsHeaders(): void {
        $origin = (defined('ENV') && ENV === 'prod')
            ? (in_array($_SERVER['HTTP_ORIGIN'] ?? '', ['https://onmed.ru', 'https://www.onmed.ru'], true)
                ? $_SERVER['HTTP_ORIGIN']
                : 'https://onmed.ru')
            : '*';
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        if( ($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS' ) { http_response_code(204); exit; }
    }

    // -------------------------------------------------------------------------
    // Cache
    // -------------------------------------------------------------------------

    private static function cacheDir(): string {
        return wire('config')->paths->cache . 'medflex/';
    }

    private static function cachePath(string $key): string {
        return self::cacheDir() . md5($key) . '.json';
    }

    public static function cacheGet(string $key): mixed {
        $file = self::cachePath($key);
        if (!file_exists($file)) return null;
        $payload = json_decode(file_get_contents($file), true);
        if (!$payload || !isset($payload['expires_at'], $payload['data'])) return null;
        if (time() > $payload['expires_at']) return null;
        return $payload['data'];
    }

    public static function cacheGetStale(string $key): mixed {
        $file = self::cachePath($key);
        if (!file_exists($file)) return null;
        $payload = json_decode(file_get_contents($file), true);
        return $payload['data'] ?? null;
    }

    public static function cacheSet(string $key, mixed $data, int $ttl): void {
        $dir = self::cacheDir();
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents(
            self::cachePath($key),
            json_encode(['expires_at' => time() + $ttl, 'data' => $data], JSON_UNESCAPED_UNICODE)
        );
    }

    // -------------------------------------------------------------------------
    // HTTP helpers
    // -------------------------------------------------------------------------

    /**
     * Fetches all pages from a paginated Medflex API endpoint and merges results.
     *
     * @param string $baseUrl   Full URL for page 1 (no &page= param).
     * @param string $apiKey    Medflex API token.
     * @param array  $warnings  Output — populated with warning strings on partial failure.
     * @return array|null       ['data' => [...merged...]] on success (possibly partial),
     *                          null if page 1 itself fails (caller should use stale cache).
     */
    public static function fetchAllPages(string $baseUrl, string $apiKey, array &$warnings = []): array|null {
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

        $page1 = $fetchPage($baseUrl);
        if( $page1 === null ) return null;

        $merged   = isset($page1['data']) && is_array($page1['data']) ? $page1['data'] : [];
        $numPages = isset($page1['num_pages']) ? (int) $page1['num_pages'] : 1;

        $separator = str_contains($baseUrl, '?') ? '&' : '?';
        for( $p = 2; $p <= $numPages; $p++ ) {
            $pageData = $fetchPage($baseUrl . $separator . "page=$p");
            if( $pageData === null ) {
                $warnings[] = "Не удалось загрузить страницу $p из $numPages.";
                break;
            }
            if( isset($pageData['data']) && is_array($pageData['data']) ) {
                $merged = array_merge($merged, $pageData['data']);
            }
        }

        return ['data' => $merged];
    }

    /**
     * Makes a single POST request to a Medflex API endpoint.
     *
     * @param string $url      Full endpoint URL.
     * @param string $apiKey   Medflex API token.
     * @param array  $payload  Data to JSON-encode and send as request body.
     * @return array|null      Decoded response array, or null on HTTP error / curl failure.
     */
    public static function apiPost(string $url, string $apiKey, array $payload): array|null {
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

        if( $httpCode < 200 || $httpCode >= 300 ) return null;
        return json_decode($response, true);
    }

}

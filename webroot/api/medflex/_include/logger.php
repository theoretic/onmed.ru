<?php
/*
Medflex request/response logger

Toggle:
    MedflexLogger::ENABLED = true  → write logs
    MedflexLogger::ENABLED = false → silent no-op

Log location: {site}/assets/logs/medflex/YYYY-MM-DD.log
              One file per day, each entry separated by ════ divider.

AT
17.05.26
*/

namespace ProcessWire;

class MedflexLogger {

    // ── Toggle ─────────────────────────────────────────────────────────────
    const ENABLED = false;
    // ───────────────────────────────────────────────────────────────────────

    private static function logDir(): string {
        return wire('config')->paths->assets . 'logs/medflex/';
    }

    private static function logFile(): string {
        return self::logDir() . date('Y-m-d') . '.log';
    }

    // Parse OS + browser from UA string (good-enough for debug).
    private static function parseUA(string $ua): array {
        $os = 'Unknown OS';
        if (preg_match('/iPhone|iPad/i', $ua)) {
            $os = preg_match('/iPhone/', $ua) ? 'iOS (iPhone)' : 'iOS (iPad)';
        } elseif (preg_match('/Android/i', $ua)) {
            $os = 'Android';
        } elseif (preg_match('/Windows NT/i', $ua)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $ua)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $ua)) {
            $os = 'Linux';
        }

        $browser = 'Unknown Browser';
        // Order matters: test specific engines before generic ones.
        if (preg_match('/CriOS/i', $ua)) {
            $browser = 'Chrome (iOS)';
        } elseif (preg_match('/FxiOS/i', $ua)) {
            $browser = 'Firefox (iOS)';
        } elseif (preg_match('/EdgiOS/i', $ua)) {
            $browser = 'Edge (iOS)';
        } elseif (preg_match('/OPiOS/i', $ua)) {
            $browser = 'Opera (iOS)';
        } elseif (preg_match('/Version\/[\d.]+ Mobile.*Safari|Version\/[\d.]+ Safari/i', $ua)) {
            $browser = 'Safari';
        } elseif (preg_match('/Chrome/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/i', $ua)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Edge/i', $ua)) {
            $browser = 'Edge';
        }

        // iOS version (useful for "was it Safari 17.4 regression?")
        $iosVer = '';
        if (preg_match('/OS (\d+[_\d]+) like Mac OS X/', $ua, $m)) {
            $iosVer = ' iOS ' . str_replace('_', '.', $m[1]);
        }

        return ['os' => $os . $iosVer, 'browser' => $browser];
    }

    /**
     * Log a request + optional Medflex API exchange.
     *
     * @param string $endpoint  Short name, e.g. 'appointment-specialist'
     * @param array  $post      Raw $_POST / $input->post as array
     * @param array|null $payload  Payload sent to Medflex API (appointment only)
     * @param array|null $apiMeta  ['http_code', 'response_headers', 'response_body']
     *                             Returned by Medflex::apiPost() via $meta param
     */
    public static function log(
        string $endpoint,
        array  $post,
        ?array $payload  = null,
        ?array $apiMeta  = null
    ): void {
        if (!self::ENABLED) return;

        $dir = self::logDir();
        if (!is_dir($dir)) @mkdir($dir, 0750, true);

        $ua    = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $uaParsed = self::parseUA($ua);
        $ip    = $_SERVER['HTTP_X_FORWARDED_FOR']
              ?? $_SERVER['HTTP_X_REAL_IP']
              ?? $_SERVER['REMOTE_ADDR']
              ?? '';
        $method  = $_SERVER['REQUEST_METHOD'] ?? '';
        $referer = $_SERVER['HTTP_REFERER']   ?? '';
        $origin  = $_SERVER['HTTP_ORIGIN']    ?? '';
        $ts      = date('Y-m-d H:i:s');

        // ── Interesting request headers (iOS debug relevant) ───────────────
        $headerKeys = [
            'HTTP_ACCEPT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_ENCODING',
            'HTTP_CONNECTION', 'HTTP_CACHE_CONTROL', 'HTTP_PRAGMA',
            'HTTP_X_REQUESTED_WITH', 'CONTENT_TYPE', 'CONTENT_LENGTH',
        ];
        $reqHeaders = [];
        foreach ($headerKeys as $k) {
            if (!empty($_SERVER[$k])) {
                $name = str_replace(['HTTP_', '_'], ['', '-'], $k);
                $reqHeaders[$name] = $_SERVER[$k];
            }
        }

        // ── Mask API key in payload ────────────────────────────────────────
        $postSafe = $post;
        foreach (['api_key', 'password', 'token'] as $f) {
            if (isset($postSafe[$f])) $postSafe[$f] = '***';
        }

        $sep   = str_repeat('═', 64);
        $thin  = str_repeat('─', 64);

        $lines = [];
        $lines[] = "\n$sep";
        $lines[] = "$ts  [$endpoint]  {$uaParsed['os']}  /  {$uaParsed['browser']}";
        $lines[] = $sep;
        $lines[] = "IP:       $ip";
        $lines[] = "Method:   $method";
        $lines[] = "Referer:  $referer";
        $lines[] = "Origin:   $origin";
        $lines[] = "UA:       $ua";

        if ($reqHeaders) {
            $lines[] = "";
            $lines[] = "── Request Headers $thin";
            foreach ($reqHeaders as $k => $v) {
                $lines[] = "  $k: $v";
            }
        }

        $lines[] = "";
        $lines[] = "── POST from client $thin";
        $lines[] = json_encode($postSafe, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if ($payload !== null) {
            $lines[] = "";
            $lines[] = "── Payload → Medflex API $thin";
            $lines[] = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        if ($apiMeta !== null) {
            $code = $apiMeta['http_code'] ?? '?';
            $lines[] = "";
            $lines[] = "── Response ← Medflex API  HTTP $code $thin";

            if (!empty($apiMeta['response_headers'])) {
                $lines[] = "Response Headers:";
                foreach ($apiMeta['response_headers'] as $k => $v) {
                    $lines[] = "  $k: $v";
                }
            }

            $lines[] = "Body:";
            // Coerce to string explicitly: `$apiMeta['response_body']` is
            // `false` when curl_exec() fails (timeout, TLS, DNS, etc.). The
            // `??` operator does NOT fall back on `false`, and PHP 8+
            // json_decode(false, true) raises a fatal TypeError. That
            // exact crash was silently killing every iOS Safari appointment
            // request after Medflex started timing out.
            $body = (string)($apiMeta['response_body'] ?? '');
            // Pretty-print if JSON
            $decoded = $body !== '' ? json_decode($body, true) : null;
            $lines[] = $decoded !== null
                ? json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                : $body;
            if (!empty($apiMeta['curl_error'])) {
                $lines[] = "cURL error: " . $apiMeta['curl_error'];
            }
        }

        $lines[] = "";

        file_put_contents(self::logFile(), implode("\n", $lines), FILE_APPEND | LOCK_EX);
    }

}

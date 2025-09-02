<?php
class Captcha {
    private static ?string $site = null;
    private static ?string $secret = null;

    private static function loadConfig(): void {
        if (self::$secret !== null) return;
        $cfg = require __DIR__ . '/../config.php';
        self::$site   = $cfg['security']['recaptcha_site_key'] ?? '';
        self::$secret = $cfg['security']['recaptcha_secret_key'] ?? '';
    }

    public static function renderWidget(): void {
        self::loadConfig();
        if (empty(self::$site)) return; // no widget in dev
        echo '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars(self::$site) . '"></div>';
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    }

    public static function verify(?string $response): bool {
        self::loadConfig();

        // dev mode shortcut
        if (empty(self::$site) || empty(self::$secret)) {
            return true;
        }

        if (empty($response)) {
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = http_build_query([
            'secret'   => self::$secret,
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $data,
                'timeout' => 5
            ]
        ];

        $res = @file_get_contents($url, false, stream_context_create($opts));
        if (!$res) {
            return false;
        }

        $json = json_decode($res, true);
        if (!empty($json['success'])) {
            return true;
        }

        // Optional: log error codes for debugging
        if (!empty($json['error-codes'])) {
            error_log('reCAPTCHA failed: ' . implode(', ', $json['error-codes']));
        }

        return false;
    }
}

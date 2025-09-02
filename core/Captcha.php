<?php
class Captcha {
    public static function verify(?string $response): bool {
        $cfg = require __DIR__ . '/../config.php';
        $site = $cfg['security']['recaptcha_site_key'] ?? '';
        $secret = $cfg['security']['recaptcha_secret_key'] ?? '';
        if (empty($site) || empty($secret)) {
            return true; // dev mode: pass through
        }
        if (empty($response)) return false;
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = http_build_query(['secret'=>$secret,'response'=>$response,'remoteip'=>$_SERVER['REMOTE_ADDR'] ?? '']);
        $opts = ['http' => ['method'=>'POST','header'=>"Content-type: application/x-www-form-urlencoded
",'content'=>$data,'timeout'=>5]];
        $ctx = stream_context_create($opts);
        $res = @file_get_contents($url, false, $ctx);
        if (!$res) return false;
        $json = json_decode($res, true);
        return !empty($json['success']);
    }
}
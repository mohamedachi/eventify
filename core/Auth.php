<?php
class Auth {
    public static function user() {
        return $_SESSION['user'] ?? null;
    }
    public static function check(): bool {
        return isset($_SESSION['user']);
    }
    public static function id(): ?int {
        return self::check() ? (int)$_SESSION['user']['id'] : null;
    }
    public static function role(): ?string {
        return self::check() ? $_SESSION['user']['role'] : null;
    }
    public static function login(array $user) {
        if (!empty($user['blocked'])) throw new Exception('User blocked');
        $_SESSION['user'] = $user;
    }
    public static function logout() {
        unset($_SESSION['user']);
    }
    public static function requireRole(array $roles) {
        if (!self::check() || !in_array(self::role(), $roles)) {
            header('Location: ' . base_url('login'));
            exit;
        }
    }
    public static function csrfToken(): string {
        $cfg = require __DIR__.'/../config.php';
        $key = $cfg['security']['csrf_key'];
        if (empty($_SESSION[$key])) $_SESSION[$key] = bin2hex(random_bytes(16));
        return $_SESSION[$key];
    }
    public static function verifyCsrf() {
        $cfg = require __DIR__.'/../config.php';
        $key = $cfg['security']['csrf_key'];
        if (($_POST[$key] ?? '') !== ($_SESSION[$key] ?? '')) {
            http_response_code(419);
            die('CSRF token mismatch');
        }
    }
}
<?php
class Database {
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO {
        if (self::$pdo === null) {
            $cfg = require __DIR__.'/../config.php';
            $db = $cfg['db'];
            $dsn = sprintf('%s:host=%s;port=%d;dbname=%s;charset=%s',
                $db['driver'], $db['host'], $db['port'], $db['database'], $db['charset']
            );
            $opt = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            self::$pdo = new PDO($dsn, $db['username'], $db['password'], $opt);
        }
        return self::$pdo;
    }
}
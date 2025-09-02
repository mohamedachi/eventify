<?php

// Basic app config
return [
    'app' => [
        'name' => 'Event Platform MVC',
        'base_url' => '/eventify', // set for XAMPP
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'event_platform',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'security' => [
        'session_name' => 'event_sess',
        'csrf_key' => 'csrf_token',
        'recaptcha_site_key' => '',
        'recaptcha_secret_key' => '',
    ],
];
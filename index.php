<?php
declare(strict_types=1);
session_start();

$config = require __DIR__.'/config.php';

// Autoload (very simple)
spl_autoload_register(function ($class) {
    $prefixes = ['core', 'app/controllers', 'app/models'];
    foreach ($prefixes as $p) {
        $file = __DIR__ . '/' . $p . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helpers
function base_url(string $path = ''): string {
    $cfg = require __DIR__.'/config.php';
    $base = rtrim($cfg['app']['base_url'] ?? '', '/');
    return $base . '/' . ltrim($path, '/');
}

require_once __DIR__.'/core/Router.php';
require_once __DIR__.'/core/Database.php';
require_once __DIR__.'/core/Auth.php';
require_once __DIR__.'/core/Controller.php';
require_once __DIR__.'/core/Model.php';
require_once __DIR__.'/core/View.php';

$router = new Router();

// Routes
$router->get('/', 'EventController@index');
$router->get('/events', 'EventController@index');
$router->get('/events/create', 'EventController@create');
$router->post('/events/store', 'EventController@store');
$router->get('/events/show', 'EventController@show'); // ?id=
$router->get('/events/edit', 'EventController@edit'); // ?id=
$router->post('/events/update', 'EventController@update'); // ?id=
$router->post('/events/delete', 'EventController@delete'); // ?id=
$router->post('/events/approve', 'EventController@approve'); // admin approve

$router->post('/participations/toggle', 'ParticipationController@toggle'); // join/leave
$router->get('/participations/export', 'ParticipationController@exportCsv');
$router->post('/participations/checkin', 'ParticipationController@checkin');

// Auth
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@handleLogin');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@handleRegister');
$router->post('/logout', 'AuthController@logout');

$router->get('/password/request', 'AuthController@requestPasswordReset');
$router->post('/password/send', 'AuthController@sendPasswordReset');
$router->get('/reset_password', 'AuthController@resetPasswordForm');
$router->post('/reset_password', 'AuthController@performResetPassword');

// Admin/Organizer dashboards
$router->get('/dashboard', 'DashboardController@index');
$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users/store', 'UserController@store');
$router->get('/users/edit', 'UserController@edit'); // ?id=
$router->post('/users/update', 'UserController@update'); // ?id=
$router->post('/users/delete', 'UserController@delete'); // ?id=
$router->post('/users/block', 'UserController@toggleBlock');
// Routes pour les statistiques
$router->get('/statistics/dashboard', 'StatisticsController@dashboard');
$router->get('/statistics/participationChart', 'StatisticsController@participationChart');
$router->get('/statistics/revenueChart', 'StatisticsController@revenueChart');
$router->get('/statistics/exportParticipationReport', 'StatisticsController@exportParticipationReport');
$router->get('/statistics/exportRevenueReport', 'StatisticsController@exportRevenueReport');

// Routes pour le workflow des événements
$router->post('/events/submitForApproval', 'EventController@submitForApproval');
$router->post('/events/approve', 'EventController@approve');
$router->post('/events/reject', 'EventController@reject');
$router->post('/events/archive', 'EventController@archive');
$router->get('/events/pending', 'EventController@pendingEvents');
$router->get('/events/statusHistory', 'EventController@statusHistory');
$router->get('/events/quickStats', 'EventController@quickStats');

// Routes pour les QR codes
$router->get('/events/qrManagement', 'EventController@qrManagement');
$router->post('/events/generateMissingQRs', 'EventController@generateMissingQRs');
$router->get('/events/exportQRCodes', 'EventController@exportQRCodes');
$router->get('/participation/qr', 'ParticipationController@showQR');
$router->post('/participation/generateQR', 'ParticipationController@generateQR');
$router->get('/participation/checkStatus', 'ParticipationController@checkStatus');
$router->dispatch();

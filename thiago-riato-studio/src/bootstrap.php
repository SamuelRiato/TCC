<?php
declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/users.php';
require __DIR__ . '/layout.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

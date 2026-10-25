<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_name('studenthub_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => APP_BASE !== '' ? APP_BASE : '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Enforce inactivity timeout. Authentication checks are performed by auth.php.
if (isset($_SESSION['last_activity']) && time() - (int)$_SESSION['last_activity'] > SESSION_TIMEOUT) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], '', $params['secure'], $params['httponly']);
    }
    session_destroy();
    session_start();
    $_SESSION['flash'][] = ['type' => 'warning', 'message' => 'Your session expired due to inactivity. Please sign in again.'];
}
if (isset($_SESSION['user'])) $_SESSION['last_activity'] = time();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/remember.php';

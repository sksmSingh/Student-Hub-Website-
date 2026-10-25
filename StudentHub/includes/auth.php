<?php
declare(strict_types=1);

function current_user(): ?array {
    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}
function is_logged_in(): bool { return current_user() !== null; }
function is_admin(): bool { return is_logged_in(); }
function require_login(): void {
    if (!is_logged_in()) { flash('error', 'Please sign in to continue.'); redirect('login.php'); }
}
function require_admin(): void {
    require_login();
}
function require_student(): void {
    require_login();
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Security token invalid. Refresh the page and try again.');
    }
}
function set_authenticated_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$user['id'], 'username' => (string)$user['username'],
        'email' => (string)$user['email'], 'role' => 'admin',
        'name' => (string)($user['full_name'] ?? $user['username']),
    ];
    $_SESSION['last_activity'] = time();
}
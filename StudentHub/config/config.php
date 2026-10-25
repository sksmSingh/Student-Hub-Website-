<?php
declare(strict_types=1);

const APP_NAME = 'StudentHub';
const APP_BASE = '/studenthub'; // Default XAMPP folder. Set to '' when using a virtual host at the web root.
const SESSION_TIMEOUT = 1800; // 30 minutes of inactivity.
const MAX_POSTER_BYTES = 2 * 1024 * 1024; // 2 MB.
const ALLOWED_POSTER_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

function app_url(string $path = ''): string {
    return rtrim(APP_BASE, '/') . '/' . ltrim($path, '/');
}

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never {
    header('Location: ' . app_url($path));
    exit;
}

function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

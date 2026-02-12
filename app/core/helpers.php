<?php

declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    $base = $GLOBALS['config']['base_url'] ?? '';

    if ($base === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $scriptDir = rtrim($scriptDir, '/');
        $base = $scheme . '://' . $host . $scriptDir;
    }

    $base = rtrim($base, '/');
    $path = ltrim($path, '/');

    return $path === '' ? $base : $base . '/' . $path;
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type][] = $message;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function require_auth(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Please log in to continue.');
        redirect('login');
    }
}

function user_role(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

function is_admin(): bool
{
    return is_logged_in() && user_role() === 'admin';
}

function require_admin(): void
{
    if (!is_admin()) {
        flash('danger', 'Admin access required.');
        redirect('');
    }
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    $templatePath = BASE_PATH . '/app/views/' . $template . '.php';
    if (!file_exists($templatePath)) {
        http_response_code(500);
        echo 'View not found.';
        return;
    }

    require BASE_PATH . '/app/views/layouts/main.php';
}

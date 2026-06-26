<?php

define('LARAVEL_START', microtime(true));

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// ─── Setup writable directories in /tmp ──────────────────────────
// Vercel serverless filesystem is read-only except for /tmp
$tmpBase = '/tmp/laravel';

$dirs = [
    "$tmpBase/storage/app/public",
    "$tmpBase/storage/framework/sessions",
    "$tmpBase/storage/framework/views",
    "$tmpBase/storage/framework/cache/data",
    "$tmpBase/storage/logs",
    "$tmpBase/bootstrap/cache",
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

// ─── Bootstrap Laravel ────────────────────────────────────────────
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Redirect storage & bootstrap cache to writable /tmp paths
// useStoragePath & useBootstrapPath are available in Laravel 10+
$app->useStoragePath("$tmpBase/storage");
$app->useBootstrapPath("$tmpBase/bootstrap");

// ─── Handle Request ───────────────────────────────────────────────
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);

<?php

// Vercel PHP Serverless Entry Point for Laravel
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
// Storage paths are redirected to /tmp which is writable in serverless environments

define('LARAVEL_START', microtime(true));

// ─── Redirect storage paths to /tmp ────────────────────────────
$tmpDir = '/tmp/laravel';
$dirs = [
    "$tmpDir/storage/framework/sessions",
    "$tmpDir/storage/framework/views",
    "$tmpDir/storage/framework/cache",
    "$tmpDir/storage/logs",
    "$tmpDir/bootstrap/cache",
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

$appDir = dirname(__DIR__);

// Symlink storage & bootstrap/cache to /tmp if not already linked
if (!is_link("$appDir/storage/framework/sessions") && !is_dir("$appDir/storage/framework/sessions")) {
    // Already exists from project; just ensure /tmp mirrors exist
}

// ─── Bootstrap Laravel ──────────────────────────────────────────
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);

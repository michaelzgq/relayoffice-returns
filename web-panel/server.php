<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if (!function_exists('serveStaticFile')) {
    function serveStaticFile(string $path): bool
    {
        if (!is_file($path) || !is_readable($path)) {
            return false;
        }

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($path));
        readfile($path);

        return true;
    }
}

$compatibilityPaths = [];

if (str_starts_with($uri, '/public/')) {
    $compatibilityPaths[] = __DIR__ . $uri;
}

if (str_starts_with($uri, '/storage/')) {
    $compatibilityPaths[] = __DIR__ . '/storage/app/public' . substr($uri, strlen('/storage'));
}

foreach ($compatibilityPaths as $path) {
    if (serveStaticFile($path)) {
        return true;
    }
}

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';

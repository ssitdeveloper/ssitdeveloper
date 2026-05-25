<?php

/*
|--------------------------------------------------------------------------
| Laravel Index Entry Point
|--------------------------------------------------------------------------
|
| This file serves as the entry point for the application when accessed
| from the root directory. It includes the public/index.php which is the
| actual Laravel bootstrap file.
|
*/

// Serve static assets from public folder
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if request matches asset patterns (css, js, images, fonts)
if (preg_match('~/(css|js|images|fonts)/(.+)$~', $request_uri, $matches)) {
    $relative_path = $matches[1] . '/' . $matches[2];
    $file = __DIR__ . '/public/' . $relative_path;

    if (file_exists($file) && is_file($file)) {
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mime = $mime_types[$ext] ?? 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: public, max-age=31536000');
        readfile($file);
        exit;
    }
}

// Continue with Laravel
require_once __DIR__ . '/public/index.php';


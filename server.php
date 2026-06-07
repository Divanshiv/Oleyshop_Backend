<?php

// Laravel's router script for PHP built-in server.
// This project is designed with DOMAIN_POINTED_DIRECTORY = 'root', so assets
// use URLs like /public/assets/admin/.... But php artisan serve sets the document
// root to public/, which causes those URLs to 404.
//
// This router handles both setups by stripping the /public/ prefix from asset
// URLs when needed and serving the files from the correct location.

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// For paths with /public/ prefix (asset URLs), strip and serve from correct location
// We must use readfile() because returning false would serve the ORIGINAL URI from
// the document root (public/), which doesn't exist with the /public/ prefix.
if (str_starts_with($uri, '/public/')) {
    $stripped = substr($uri, strlen('/public'));
    $filePath = __DIR__.'/public'.$stripped;
    if ($stripped !== '/' && file_exists($filePath)) {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeMap = [
            'css' => 'text/css', 'js' => 'application/javascript',
            'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'ico' => 'image/x-icon',
            'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'json' => 'application/json', 'map' => 'application/json',
        ];
        header('Content-Type: '.($mimeMap[$ext] ?? (function_exists('mime_content_type') ? mime_content_type($filePath) : 'application/octet-stream')));
        header('Content-Length: '.filesize($filePath));
        readfile($filePath);
        return true;
    }
}

// Default: fall through to Laravel front controller
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';

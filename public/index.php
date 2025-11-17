<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Lightweight visitor logging for legacy admin dashboard metrics
// Safe no-op if DB not available
try {
    // Log only public requests: skip admin endpoints and common assets
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $isAsset = (bool) preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf|map)(\?.*)?$/i', $uri);
    $isAdmin = str_starts_with($uri, '/admin');
    if ($method === 'GET' && !$isAdmin && !$isAsset) {
        // Use mysqli helper that reads .env
        require_once __DIR__ . '/db.php';
        if (isset($mysqli) && $mysqli instanceof mysqli) {
            // Create table if not exists
            $mysqli->query('CREATE TABLE IF NOT EXISTS page_views (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                ip VARCHAR(45) NULL,
                user_agent VARCHAR(255) NULL,
                path VARCHAR(255) NULL,
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

            // Insert one row
            $ip = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
            $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
            $path = substr((string)$uri, 0, 255);
            if ($stmt = $mysqli->prepare('INSERT INTO page_views (ip, user_agent, path, created_at) VALUES (?, ?, ?, NOW())')) {
                $stmt->bind_param('sss', $ip, $ua, $path);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
} catch (Throwable $e) {
    // Silently ignore logging errors to avoid affecting public site
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());

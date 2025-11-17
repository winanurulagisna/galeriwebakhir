<?php
// Simple MySQLi connection helper for standalone PHP pages
// Tries to read Laravel .env DB_* first, then environment variables, then sensible defaults

function loadEnvVarsFromDotEnv(string $path): array {
    $vars = [];
    if (!is_file($path)) {
        return $vars;
    }
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $vars;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        // naive parser KEY=VALUE (no export, no quotes handling for complex cases)
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            // strip surrounding quotes if any
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) || (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }
            $vars[$key] = $val;
        }
    }
    return $vars;
}

$projectRoot = dirname(__DIR__);
$dotEnvPath = $projectRoot . DIRECTORY_SEPARATOR . '.env';
$env = loadEnvVarsFromDotEnv($dotEnvPath);

$DB_HOST = $env['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = $env['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$DB_NAME = $env['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'webgaleri';
$DB_USER = $env['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
$DB_PASS = $env['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

$mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int) $DB_PORT);

if ($mysqli->connect_errno) {
    // 1049 = Unknown database
    if ($mysqli->connect_errno === 1049) {
        http_response_code(500);
        die('Koneksi database gagal: Database "' . htmlspecialchars($DB_NAME, ENT_QUOTES, 'UTF-8') . '" tidak ditemukan. Perbarui DB_DATABASE di file .env atau buat database-nya di MySQL/phpMyAdmin.');
    }
    http_response_code(500);
    die('Koneksi database gagal: ' . htmlspecialchars($mysqli->connect_error, ENT_QUOTES, 'UTF-8'));
}

// Ensure UTF-8 charset
$mysqli->set_charset('utf8mb4');

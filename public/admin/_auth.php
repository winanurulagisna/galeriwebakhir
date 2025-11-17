<?php
session_start();

if (empty($_SESSION['petugas_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

function isPost(): bool { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function getKategoriId(mysqli $mysqli, string $judul, int $fallback): int {
    $id = $fallback;
    if ($stmt = $mysqli->prepare('SELECT id FROM kategori_new WHERE judul = ? LIMIT 1')) {
        $stmt->bind_param('s', $judul);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) { $id = (int)$row['id']; }
        }
        $stmt->close();
    }
    return $id;
}

function ensureKategoriId(mysqli $mysqli, string $judul, int $fallback): int {
    // Try by title first
    $id = getKategoriId($mysqli, $judul, -1);
    if ($id > 0) { return $id; }

    // If fallback ID exists, use it
    if ($stmt = $mysqli->prepare('SELECT id FROM kategori_new WHERE id = ? LIMIT 1')) {
        $stmt->bind_param('i', $fallback);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) { $stmt->close(); return (int)$row['id']; }
        }
        $stmt->close();
    }

    // Create new category with provided title
    if ($stmt = $mysqli->prepare('INSERT INTO kategori_new (judul, created_at, updated_at) VALUES (?, NOW(), NOW())')) {
        $stmt->bind_param('s', $judul);
        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $stmt->close();
            return (int)$newId;
        }
        $stmt->close();
    }

    // As last resort, return fallback even if not present; caller should handle FK error if still missing
    return $fallback;
}

function tableExists(mysqli $mysqli, string $table): bool {
    $res = $mysqli->query("SHOW TABLES LIKE '" . $mysqli->real_escape_string($table) . "'");
    $ok = $res && $res->num_rows > 0;
    if ($res) { $res->close(); }
    return $ok;
}

function clientRedirect(string $url): void {
    // Use client-side redirect to avoid 'headers already sent'
    echo '<script>window.location.href="' . e($url) . '";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . e($url) . '"></noscript>';
    exit;
}



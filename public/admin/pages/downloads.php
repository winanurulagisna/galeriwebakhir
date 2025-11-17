<?php
// Unduhan Foto - gabungkan catatan baru (database) dan lama (JSON legacy)
$root = realpath(__DIR__ . '/../../..');
$subFile = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'photo_download_submissions.json';

function _dl_key($row){
    $a = (string)($row['created_at'] ?? '');
    $b = strtolower(trim((string)($row['email'] ?? '')));
    $c = (string)($row['photo_id'] ?? '');
    return sha1($a.'|'.$b.'|'.$c);
}

function _dl_format_time(?string $time): array {
    if (!$time) { return ['-', '']; }
    $ts = strtotime($time);
    if ($ts === false) { return [$time, $time]; }
    return [date('Y-m-d H:i:s', $ts), date('c', $ts)];
}

// Handle delete actions
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (isset($_POST['delete_db_id'])) {
        $deleteId = (int)($_POST['delete_db_id'] ?? 0);
        if ($deleteId > 0 && isset($mysqli) && $mysqli && function_exists('tableExists') && tableExists($mysqli, 'photo_downloads')) {
            if ($stmt = $mysqli->prepare('DELETE FROM photo_downloads WHERE id = ?')) {
                $stmt->bind_param('i', $deleteId);
                $stmt->execute();
                $stmt->close();
            }
        }
        clientRedirect('?page=downloads');
    }

    if (isset($_POST['delete_key'])) {
        $delKey = $_POST['delete_key'];
        $subsRaw = file_exists($subFile) ? @file_get_contents($subFile) : '';
        $subsArr = $subsRaw ? json_decode($subsRaw, true) : [];
        if (is_array($subsArr)) {
            $newArr = [];
            foreach ($subsArr as $row) {
                if (_dl_key($row) === $delKey) {
                    continue;
                }
                $newArr[] = $row;
            }
            @file_put_contents($subFile, json_encode($newArr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }
        clientRedirect('?page=downloads');
    }
}

$entries = [];
$totalDownloads = 0;

// Load database entries
if (isset($mysqli) && $mysqli && function_exists('tableExists') && tableExists($mysqli, 'photo_downloads')) {
    $sql = "SELECT pd.id, pd.photo_id, pd.user_id, pd.session_id, pd.ip, pd.user_agent, pd.downloaded_at, pd.created_at,
                   u.name AS user_name, u.email AS user_email
            FROM photo_downloads pd
            LEFT JOIN users u ON u.id = pd.user_id
            ORDER BY COALESCE(pd.downloaded_at, pd.created_at) DESC";
    if ($result = $mysqli->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $timeRaw = $row['downloaded_at'] ?? $row['created_at'] ?? null;
            [$timeDisplay, $timeSort] = _dl_format_time($timeRaw);
            $entries[] = [
                'source' => 'database',
                'id' => (int)$row['id'],
                'name' => trim((string)($row['user_name'] ?? '')) ?: 'Pengguna Terdaftar',
                'email' => trim((string)($row['user_email'] ?? '')),
                'photo_id' => (int)($row['photo_id'] ?? 0),
                'status' => 'downloaded',
                'time_display' => $timeDisplay,
                'time_sort' => $timeSort,
                'ip' => $row['ip'] ?? '',
                'session_id' => $row['session_id'] ?? '',
                'user_agent' => $row['user_agent'] ?? '',
            ];
        }
        $result->close();
    }
    $totalDownloads += count(array_filter($entries, fn($e) => $e['source'] === 'database'));
}

// Load legacy JSON entries (guest form)
$legacySubs = [];
if (file_exists($subFile)) {
    $raw = @file_get_contents($subFile);
    $arr = $raw ? json_decode($raw, true) : [];
    if (is_array($arr)) { $legacySubs = $arr; }
}

foreach ($legacySubs as $row) {
    $timeRaw = $row['downloaded_at'] ?? $row['created_at'] ?? null;
    [$timeDisplay, $timeSort] = _dl_format_time($timeRaw);
    $status = $row['status'] ?? 'authorized';
    if ($status === 'downloaded') { $totalDownloads++; }
    $entries[] = [
        'source' => 'legacy',
        'key' => _dl_key($row),
        'name' => trim((string)($row['name'] ?? '')) ?: 'Pengunjung',
        'email' => trim((string)($row['email'] ?? '')),
        'photo_id' => (int)($row['photo_id'] ?? 0),
        'status' => $status,
        'time_display' => $timeDisplay,
        'time_sort' => $timeSort,
        'ip' => $row['ip'] ?? '',
        'session_id' => '',
        'user_agent' => $row['ua'] ?? '',
    ];
}

usort($entries, fn($a, $b) => strcmp($b['time_sort'], $a['time_sort']));
?>
<div class="card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <h3>Unduhan Foto</h3>
    <span class="muted">Total Unduhan: <?php echo (int)$totalDownloads; ?></span>
  </div>
  <table style="width:100%; border-collapse:collapse;">
    <thead>
      <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
        <th style="padding:10px; text-align:left;">Waktu</th>
        <th style="padding:10px; text-align:left;">Nama</th>
        <th style="padding:10px; text-align:left;">Email</th>
        <th style="padding:10px; text-align:left;">Photo ID</th>
        <th style="padding:10px; text-align:left;">Status</th>
        <th style="padding:10px; text-align:left;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($entries)): ?>
        <tr><td colspan="6" class="muted" style="padding:12px;">Belum ada data.</td></tr>
      <?php else: foreach ($entries as $entry): ?>
        <tr style="border-bottom:1px solid #eef2f7;">
          <td style="padding:10px;" class="muted"><?php echo e($entry['time_display']); ?></td>
          <td style="padding:10px; font-weight:600; color:#0f172a;"><?php echo e($entry['name']); ?></td>
          <td style="padding:10px;">
            <?php if (!empty($entry['email'])): ?>
              <a href="mailto:<?php echo e($entry['email']); ?>"><?php echo e($entry['email']); ?></a>
            <?php else: ?>
              <span class="muted">-</span>
            <?php endif; ?>
          </td>
          <td style="padding:10px;">#<?php echo (int)($entry['photo_id'] ?? 0); ?></td>
          <td style="padding:10px;">
            <?php if ($entry['status'] === 'downloaded'): ?>
              <span style="background:#dcfce7;color:#166534;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:700;">Downloaded</span>
            <?php else: ?>
              <span style="background:#fef9c3;color:#92400e;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:700;">Authorized</span>
            <?php endif; ?>
          </td>
          <td style="padding:10px;">
            <?php if ($entry['source'] === 'database'): ?>
              <form method="post" onsubmit="return confirm('Hapus entri ini?');" style="display:inline">
                <input type="hidden" name="delete_db_id" value="<?php echo (int)$entry['id']; ?>">
                <button type="submit" class="btn danger" style="padding:5px 10px;">Hapus</button>
              </form>
            <?php else: ?>
              <form method="post" onsubmit="return confirm('Hapus entri ini?');" style="display:inline">
                <input type="hidden" name="delete_key" value="<?php echo e($entry['key']); ?>">
                <button type="submit" class="btn danger" style="padding:5px 10px;">Hapus</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

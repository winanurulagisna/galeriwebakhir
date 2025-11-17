<?php
// Laporan Mingguan: Like Foto, Upload Foto Baru, Unduh Foto
// Menggunakan data dari tabel: photo_likes, photos, photo_downloads
// Fungsi tableExists() sudah tersedia dari _auth.php

// Helper tanggal: dapatkan awal & akhir minggu (Senin - Minggu) dari input 'from' atau minggu berjalan
function weekRange($from = null) {
  $dt = $from ? strtotime($from) : time();
  // Normalisasi ke Senin minggu itu
  $w = (int)date('N', $dt); // 1..7 (Mon..Sun)
  $monday = strtotime('-'.($w-1).' days', strtotime(date('Y-m-d', $dt)));
  $sunday = strtotime('+6 days', $monday);
  return [date('Y-m-d 00:00:00', $monday), date('Y-m-d 23:59:59', $sunday)];
}

// Ambil parameter
$fromParam = isset($_GET['from']) ? trim($_GET['from']) : '';
if ($fromParam !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromParam)) { $fromParam = ''; }
list($startAt, $endAt) = weekRange($fromParam ?: null);

// Fungsi hitung dari DB (aman jika tabel tidak ada)
function countFromDb($mysqli, $table, $column, $startAt, $endAt) {
  if (!tableExists($mysqli, $table)) return 0;
  $sql = "SELECT COUNT(*) AS c FROM {$table} WHERE {$column} BETWEEN ? AND ?";
  if ($st = $mysqli->prepare($sql)) {
    $st->bind_param('ss', $startAt, $endAt);
    $st->execute();
    $rs = $st->get_result();
    $row = $rs ? $rs->fetch_assoc() : null;
    $st->close();
    return (int)($row['c'] ?? 0);
  }
  return 0;
}

// Hitung like foto per minggu
$likesCount = countFromDb($mysqli, 'photo_likes', 'created_at', $startAt, $endAt);

// Hitung upload foto baru (tabel: photos)
$uploadsCount = countFromDb($mysqli, 'photos', 'created_at', $startAt, $endAt);

// Hitung unduhan dari tabel photo_downloads
$downloadsCount = countFromDb($mysqli, 'photo_downloads', 'created_at', $startAt, $endAt);

// Hitung agenda dari tabel agenda
$agendaCount = countFromDb($mysqli, 'agenda', 'created_at', $startAt, $endAt);

// Hitung berita dari posts_new (kategori_id = 1)
function countBerita($mysqli, $startAt, $endAt) {
  if (!tableExists($mysqli, 'posts_new')) return 0;
  $sql = "SELECT COUNT(*) AS c FROM posts_new WHERE kategori_id = 1 AND created_at BETWEEN ? AND ?";
  if ($st = $mysqli->prepare($sql)) {
    $st->bind_param('ss', $startAt, $endAt);
    $st->execute();
    $rs = $st->get_result();
    $row = $rs ? $rs->fetch_assoc() : null;
    $st->close();
    return (int)($row['c'] ?? 0);
  }
  return 0;
}
$beritaCount = countBerita($mysqli, $startAt, $endAt);
// Prepare data structure for feature-based rows
$reportRows = [];

// Get berita with likes and downloads count
if (tableExists($mysqli, 'posts_new')) {
  $sql = "SELECT p.judul, DATE(p.created_at) as tanggal,
          (SELECT COUNT(*) FROM photo_likes WHERE photo_id IN (SELECT id FROM photos WHERE related_type='berita' AND related_id=p.id)) as likes,
          (SELECT COUNT(*) FROM photo_downloads WHERE photo_id IN (SELECT id FROM photos WHERE related_type='berita' AND related_id=p.id)) as downloads
          FROM posts_new p WHERE p.kategori_id = 1 AND p.created_at BETWEEN ? AND ? ORDER BY p.created_at DESC";
  if ($st = $mysqli->prepare($sql)) {
    $st->bind_param('ss', $startAt, $endAt);
    $st->execute();
    $rs = $st->get_result();
    while($r = $rs->fetch_assoc()) {
      $reportRows[] = [
        'tanggal' => $r['tanggal'],
        'fitur' => 'Berita',
        'tipe' => 'User',
        'judul' => $r['judul'],
        'likes' => (int)$r['likes'],
        'uploads' => 0,
        'downloads' => (int)$r['downloads'],
        'agenda' => 0
      ];
    }
    $st->close();
  }
}

// Get galeri (photos) uploads count
if (tableExists($mysqli, 'photos')) {
  $sql = "SELECT DATE(created_at) as tanggal, COUNT(*) as uploads
          FROM photos WHERE created_at BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY created_at DESC";
  if ($st = $mysqli->prepare($sql)) {
    $st->bind_param('ss', $startAt, $endAt);
    $st->execute();
    $rs = $st->get_result();
    while($r = $rs->fetch_assoc()) {
      $reportRows[] = [
        'tanggal' => $r['tanggal'],
        'fitur' => 'Galeri',
        'tipe' => 'Admin',
        'judul' => 'Upload Foto',
        'likes' => 0,
        'uploads' => (int)$r['uploads'],
        'downloads' => 0,
        'agenda' => 0
      ];
    }
    $st->close();
  }
}

// Get agenda count
if (tableExists($mysqli, 'agenda')) {
  $sql = "SELECT judul, DATE(created_at) as tanggal FROM agenda WHERE created_at BETWEEN ? AND ? ORDER BY created_at DESC";
  if ($st = $mysqli->prepare($sql)) {
    $st->bind_param('ss', $startAt, $endAt);
    $st->execute();
    $rs = $st->get_result();
    while($r = $rs->fetch_assoc()) {
      $reportRows[] = [
        'tanggal' => $r['tanggal'],
        'fitur' => 'Agenda',
        'tipe' => 'Admin',
        'judul' => $r['judul'],
        'likes' => 0,
        'uploads' => 0,
        'downloads' => 0,
        'agenda' => 1
      ];
    }
    $st->close();
  }
}

// Sort by date descending
usort($reportRows, function($a, $b) {
  return strcmp($b['tanggal'], $a['tanggal']);
});


// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
  // Bersihkan semua output buffer agar HTML dari index.php tidak ikut terkirim
  if (function_exists('ob_get_level')) {
    while (ob_get_level() > 0) { @ob_end_clean(); }
  }
  $filename = 'laporan-mingguan-'.substr($startAt,0,10).'_sd_'.substr($endAt,0,10).'.csv';
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename='.$filename);
  header('Pragma: no-cache');
  header('Expires: 0');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['Periode', substr($startAt,0,10).' s/d '.substr($endAt,0,10)]);
  fputcsv($out, []);
  fputcsv($out, ['Tanggal','Fitur','Tipe','Likes','Upload Foto','Unduh Foto','Agenda']);
  foreach ($reportRows as $row) {
    fputcsv($out, [$row['tanggal'], $row['fitur'], $row['tipe'], $row['likes'], $row['uploads'], $row['downloads'], $row['agenda']]);
  }
  fputcsv($out, []);
  fputcsv($out, ['TOTAL', $likesCount, $uploadsCount, $downloadsCount, $beritaCount, $agendaCount]);
  fclose($out);
  exit;
}
?>
<div class="card report-card">
  <div class="report-card__header">
    <div>
      <h3>Laporan Mingguan</h3>
      <p class="muted">Periode laporan mengikuti rentang Senin–Minggu.</p>
    </div>
  </div>

  <form method="get" class="report-filter">
    <input type="hidden" name="page" value="reports">
    <label class="filter-field">
      <span>Mulai Minggu (Senin)</span>
      <input type="date" name="from" value="<?php echo htmlspecialchars(substr($startAt,0,10)); ?>">
    </label>
    <div class="filter-actions">
      <button class="btn" type="submit">Terapkan</button>
      <a class="btn success" href="?page=reports&from=<?php echo urlencode(substr($startAt,0,10)); ?>&export=csv">Unduh CSV</a>
      <button class="btn primary" type="button" id="btn-download-image">Unduh Gambar</button>
    </div>
  </form>

  <style>
    .report-card { padding:18px; }
    .report-card__header h3 { margin:0; font-size:18px; color:#023859; font-weight:700; }
    .report-card__header p { margin:4px 0 0; font-size:12px; }
    .report-filter { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; margin-bottom:16px; }
    .report-filter input[type="date"] { padding:8px 10px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px; min-width:180px; }
    .filter-field { display:flex; flex-direction:column; gap:6px; font-weight:600; color:#334155; font-size:13px; }
    .filter-actions { display:flex; gap:8px; }
    .btn.success { background:#10b981; }
    .btn.primary { background:#2563eb; }
    .report-wrap { max-width:960px; margin:0 auto; background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; box-shadow:0 10px 30px rgba(2,56,89,.08); }
    .report-header { text-align:center; margin-bottom:20px; }
    .report-header__title { font-size:22px; font-weight:800; letter-spacing:.4px; color:#023859; margin-bottom:4px; }
    .report-header__subtitle { font-weight:600; color:#1e293b; }
    .report-header__meta { margin-top:6px; font-size:12px; color:#64748b; }
    .report-divider { height:1px; background:#e2e8f0; margin:16px 0; }
    .report-grid { display:grid; gap:14px; }
    .report-grid--primary { grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); margin-bottom:18px; }
    .report-grid--secondary { grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); margin-bottom:12px; }
    .stat-card { padding:16px; border-radius:12px; border:1px solid #e2e8f0; background:linear-gradient(180deg,#f8fafc 0%,#fff 100%); box-shadow:0 4px 12px rgba(15,23,42,.06); }
    .stat-title { font-size:12px; text-transform:uppercase; letter-spacing:.6px; color:#64748b; font-weight:700; margin-bottom:6px; }
    .stat-value { font-size:26px; font-weight:800; color:#0f172a; }
    .report-table { width:100%; border-collapse:collapse; font-size:13px; }
    .report-table th, .report-table td { padding:10px 12px; border-bottom:1px solid #e2e8f0; text-align:left; }
    .report-table th { background:#f8fafc; font-weight:700; color:#0f172a; }
    .report-table tbody tr:hover { background:#f8fafc; }
    .pill { display:inline-flex; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:600; }
    .pill--user { background:#dbeafe; color:#1e40af; }
    .pill--admin { background:#fef3c7; color:#92400e; }
    .report-summary { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; margin-top:16px; }
    .summary-card { padding:14px; border-radius:10px; background:#f8fafc; border:1px solid #e2e8f0; }
    .summary-card__label { font-size:12px; color:#64748b; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.5px; }
    .summary-card__value { font-size:18px; color:#023859; font-weight:700; margin-bottom:4px; }
    .summary-card__meta { font-size:11px; color:#94a3b8; }
    @media(max-width:768px){
      .report-wrap { padding:18px; border-radius:12px; }
      .report-table th, .report-table td { padding:8px; }
    }
  </style>

  <div id="report-capture" class="report-wrap">
    <div class="report-header">
      <div class="report-header__title">LAPORAN MINGGUAN</div>
      <div class="report-header__subtitle">SMKN 4 KOTA BOGOR</div>
      <div class="report-header__meta">
        Periode: <?php echo htmlspecialchars(substr($startAt,0,10)); ?> s/d <?php echo htmlspecialchars(substr($endAt,0,10)); ?> • Dibuat: <?php echo date('d M Y H:i'); ?>
      </div>
    </div>

    <div class="report-grid report-grid--primary">
      <div class="stat-card">
        <div class="stat-title">Like Foto</div>
        <div class="stat-value"><?php echo (int)$likesCount; ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Upload Foto Baru</div>
        <div class="stat-value"><?php echo (int)$uploadsCount; ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Unduh Foto</div>
        <div class="stat-value"><?php echo (int)$downloadsCount; ?></div>
      </div>
    </div>

    <div class="report-grid report-grid--secondary">
      <div class="stat-card">
        <div class="stat-title">Berita Sekolah</div>
        <div class="stat-value"><?php echo (int)$beritaCount; ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-title">Agenda Sekolah</div>
        <div class="stat-value"><?php echo (int)$agendaCount; ?></div>
      </div>
    </div>

    <div class="report-divider"></div>

    <table class="report-table">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Fitur</th>
          <th>Tipe</th>
          <th>Likes</th>
          <th>Upload Foto</th>
          <th>Unduh Foto</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($reportRows)): ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">Tidak ada aktivitas pada periode ini</td>
          </tr>
        <?php else: ?>
          <?php foreach ($reportRows as $row): ?>
            <tr>
              <td class="muted"><?php echo htmlspecialchars($row['tanggal']); ?></td>
              <td style="font-weight:600; color:#0f172a;"><?php echo htmlspecialchars($row['fitur']); ?></td>
              <td>
                <span class="pill <?php echo $row['tipe'] === 'User' ? 'pill--user' : 'pill--admin'; ?>">
                  <?php echo htmlspecialchars($row['tipe']); ?>
                </span>
              </td>
              <td><?php echo $row['likes'] > 0 ? $row['likes'] : '-'; ?></td>
              <td><?php echo $row['uploads'] > 0 ? $row['uploads'] : '-'; ?></td>
              <td><?php echo $row['downloads'] > 0 ? $row['downloads'] : '-'; ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="report-summary">
      <div class="summary-card">
        <div class="summary-card__label">Total Berita</div>
        <div class="summary-card__value"><?php echo (int)$beritaCount; ?> Berita</div>
        <div class="summary-card__meta"><?php echo (int)$likesCount; ?> Like • <?php echo (int)$downloadsCount; ?> Unduh</div>
      </div>
      <div class="summary-card">
        <div class="summary-card__label">Total Galeri</div>
        <div class="summary-card__value"><?php echo (int)$uploadsCount; ?> Upload Foto</div>
        <div class="summary-card__meta">Periode <?php echo htmlspecialchars(substr($startAt,0,10)); ?> s/d <?php echo htmlspecialchars(substr($endAt,0,10)); ?></div>
      </div>
      <div class="summary-card">
        <div class="summary-card__label">Total Agenda</div>
        <div class="summary-card__value"><?php echo (int)$agendaCount; ?> Kegiatan</div>
        <div class="summary-card__meta">Agenda yang dipublikasikan selama periode</div>
      </div>
    </div>
  </div>
</div>

<!-- html2canvas for image export -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('btn-download-image');
  if (!btn) return;
  btn.addEventListener('click', function(){
    const target = document.getElementById('report-capture');
    if (!target) return;
    // Ensure full white background for export
    const oldBg = target.style.backgroundColor;
    target.style.backgroundColor = '#ffffff';
    html2canvas(target, {scale:2, backgroundColor:'#ffffff'}).then(canvas => {
      // Downscale to portrait medium if too large (maxWidth ~ 1080px)
      const maxW = 1080; // medium portrait width
      let outCanvas = canvas;
      if (canvas.width > maxW) {
        const ratio = maxW / canvas.width;
        const w = Math.round(canvas.width * ratio);
        const h = Math.round(canvas.height * ratio);
        const cnv = document.createElement('canvas');
        cnv.width = w; cnv.height = h;
        const ctx = cnv.getContext('2d');
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(canvas, 0, 0, w, h);
        outCanvas = cnv;
      }
      const link = document.createElement('a');
      link.download = 'laporan-mingguan-<?php echo substr($startAt,0,10); ?>_sd_<?php echo substr($endAt,0,10); ?>.png';
      link.href = outCanvas.toDataURL('image/png');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      target.style.backgroundColor = oldBg;
    });
  });
});
</script>

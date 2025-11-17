<?php
// CRUD Acara Sekolah (tabel khusus) + upload foto langsung (related_type='acara')

// Pastikan helper tersedia: $mysqli, tableExists()
if (!function_exists('tableExists')) {
    function tableExists($mysqli, $table) {
        if (!$mysqli) return false;
        $stmt = $mysqli->prepare('SHOW TABLES LIKE ?');
        $stmt->bind_param('s', $table);
        $stmt->execute();
        $res = $stmt->get_result();
        $ok = $res && $res->num_rows > 0;
        $stmt->close();
        return $ok;
    }
}

// Buat tabel acara jika belum ada
$createSql = "CREATE TABLE IF NOT EXISTS acara_new (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi MEDIUMTEXT NULL,
    tanggal DATETIME NULL,
    lokasi VARCHAR(200) NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'published',
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$mysqli->query($createSql);

if (!function_exists('e')) {
    function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('isPost')) {
    function isPost(){ return strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'; }
}

// Ensure a Gallery exists for a specific Acara (per-acara album) and return its ID
if (!function_exists('ensureAcaraGallery')) {
    function ensureAcaraGallery(mysqli $mysqli, int $acaraId): int {
        // Fetch acara title & description
        $judul = '';
        $deskripsi = '';
        if ($st = $mysqli->prepare('SELECT judul, deskripsi FROM acara_new WHERE id=? LIMIT 1')) {
            $st->bind_param('i', $acaraId);
            $st->execute();
            $res = $st->get_result();
            if ($row = $res->fetch_assoc()) {
                $judul = trim((string)($row['judul'] ?? ''));
                $deskripsi = trim((string)($row['deskripsi'] ?? ''));
            }
            $st->close();
        }
        if ($judul === '') { $judul = 'Acara Sekolah'; }

        // Try find existing album by category + title match
        if ($st = $mysqli->prepare('SELECT id FROM galery WHERE category = ? AND title = ? LIMIT 1')) {
            $cat = 'acara sekolah';
            $st->bind_param('ss', $cat, $judul);
            $st->execute();
            $res = $st->get_result();
            if ($row = $res->fetch_assoc()) { $st->close(); return (int)$row['id']; }
            $st->close();
        }

        // Create album per acara
        $cat = 'acara sekolah';
        $status = 'published';
        if ($st = $mysqli->prepare('INSERT INTO galery (title, description, category, status, created_at, updated_at) VALUES (?,?,?,?, NOW(), NOW())')) {
            $st->bind_param('ssss', $judul, $deskripsi, $cat, $status);
            $st->execute();
            $newId = $st->insert_id;
            $st->close();
            return (int)$newId;
        }
        return 0;
    }
}

if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        // Form tambah saat ini tidak mengirim 'status', default ke 'published'
        $rawStatus = $_POST['status'] ?? 'published';
        $status = in_array($rawStatus, ['draft','published'], true) ? $rawStatus : 'published';
        if ($judul !== '') {
            $st = $mysqli->prepare('INSERT INTO acara_new (judul, deskripsi, tanggal, lokasi, status, created_at, updated_at) VALUES (?,?,?,?,?,NOW(),NOW())');
            $st->bind_param('sssss', $judul, $deskripsi, $tanggal, $lokasi, $status);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=acara'); exit;
    }

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $rawStatus = $_POST['status'] ?? 'published';
        $status = in_array($rawStatus, ['draft','published'], true) ? $rawStatus : 'published';
        if ($id > 0 && $judul !== '') {
            $st = $mysqli->prepare('UPDATE acara_new SET judul=?, deskripsi=?, tanggal=?, lokasi=?, status=?, updated_at=NOW() WHERE id=?');
            $st->bind_param('sssssi', $judul, $deskripsi, $tanggal, $lokasi, $status, $id);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=acara&edit='.$id); exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // hapus foto terkait
            $st = $mysqli->prepare('SELECT id, file_path FROM photos WHERE related_type="acara" ORDER BY id DESC');
            $res = $st->get_result();
            while ($row = $res->fetch_assoc()) {
                $fileRef = (string)($row['file_path'] ?? '');
                if ($fileRef !== '' && !preg_match('/^https?:\/\//i', $fileRef)) {
                    // Try delete from public root first
                    $pubRoot = realpath(__DIR__ . '/..' . '/..');
                    $candidate = $pubRoot ? realpath($pubRoot . '/' . ltrim($fileRef,'/')) : false;
                    if ($candidate && file_exists($candidate)) {
                        @unlink($candidate);
                    } else {
                        // Fallback legacy path under /public/admin
                        $adminRoot = realpath(__DIR__ . '/..');
                        $legacy = $adminRoot ? realpath($adminRoot . '/' . ltrim($fileRef,'/')) : false;
                        if ($legacy && file_exists($legacy)) { @unlink($legacy); }
                    }
                }
                $del = $mysqli->prepare('DELETE FROM foto WHERE id=?');
                $pid = (int)$row['id'];
                $del->bind_param('i', $pid);
                $del->execute();
                $del->close();
            }
            $st->close();
            // hapus acara
            $st = $mysqli->prepare('DELETE FROM acara_new WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=acara'); exit;
    }

    if ($action === 'merge_acara_albums') {
        // Move all acara photos into a single default gallery titled 'Acara Sekolah'
        // 1) Ensure/get default gallery id
        $defaultGalId = 0;
        if ($st = $mysqli->prepare('SELECT id FROM galery WHERE category=? AND title=? LIMIT 1')) {
            $cat = 'acara sekolah'; $title = 'Acara Sekolah';
            $st->bind_param('ss', $cat, $title);
            $st->execute();
            $res = $st->get_result();
            if ($row = $res->fetch_assoc()) { $defaultGalId = (int)$row['id']; }
            $st->close();
        }
        if ($defaultGalId === 0) {
            if ($st = $mysqli->prepare('INSERT INTO galery (title, description, category, status, created_at, updated_at) VALUES (?,?,?,?, NOW(), NOW())')) {
                $title = 'Acara Sekolah'; $desc = 'Dokumentasi acara sekolah'; $cat = 'acara sekolah'; $status = 'published';
                $st->bind_param('ssss', $title, $desc, $cat, $status);
                $st->execute();
                $defaultGalId = (int)$st->insert_id;
                $st->close();
            }
        }
        if ($defaultGalId > 0) {
            // 2) Update all acara photos to point to default gallery
            $mysqli->query('UPDATE photos SET gallery_id=' . (int)$defaultGalId . " WHERE related_type='acara'");
        }
        header('Location: ?page=acara'); exit;
    }

    if ($action === 'upload_foto') {
        $acaraId = (int)($_POST['acara_id'] ?? 0);
        if ($acaraId > 0 && isset($_FILES['foto']) && !empty($_FILES['foto']['name'][0])) {
            // Arahkan semua foto baru ke album default 'Acara Sekolah'
            $targetGalId = ensureAcaraGallery($mysqli, 0);
            // Save files under public/images/acara
            $publicRoot = realpath(__DIR__ . '/..' . '/..'); // .../public
            $uploadDir = ($publicRoot ? $publicRoot : dirname(__DIR__,2)) . '/images/acara/';
            $webPrefix = '/images/acara/';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
            for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                if ($_FILES['foto']['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['foto']['name'][$i], PATHINFO_EXTENSION);
                    $fileName = uniqid('acara_').'.'.$ext;
                    $destPath = $uploadDir . $fileName;
                    $judulFoto = trim($_POST['photo_judul'][$i] ?? '');
                    if (move_uploaded_file($_FILES['foto']['tmp_name'][$i], $destPath)) {
                        $webPath = $webPrefix . $fileName;
                        $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file_path, caption, related_type, created_at, updated_at) VALUES (?, ?, ?, "acara", NOW(), NOW())');
                        $st->bind_param('iss', $targetGalId, $webPath, $judulFoto);
                        $st->execute();
                        $st->close();
                    }
                }
            }
        }
        header('Location: ?page=acara&edit=' . $acaraId); exit;
    }

    if ($action === 'delete_photo') {
        $photoId = (int)($_POST['id'] ?? 0);
        $ownerId = (int)($_POST['acara_id'] ?? 0);
        if ($photoId > 0) {
            $st = $mysqli->prepare('SELECT file FROM foto WHERE id=? LIMIT 1');
            $st->bind_param('i', $photoId);
            $st->execute();
            $resPhoto = $st->get_result();
            if ($row = $resPhoto->fetch_assoc()) {
                $fileRef = (string)($row['file'] ?? '');
                if ($fileRef !== '' && !preg_match('/^https?:\/\//i', $fileRef)) {
                    $candidate = realpath(__DIR__ . '/../' . ltrim($fileRef, '/'));
                    $publicRoot = realpath(__DIR__ . '/..');
                    if ($candidate && $publicRoot && strncmp($candidate, $publicRoot, strlen($publicRoot)) === 0 && file_exists($candidate)) {
                        @unlink($candidate);
                    }
                }
            }
            $st->close();
            $st = $mysqli->prepare('DELETE FROM foto WHERE id=?');
            $st->bind_param('i', $photoId);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=acara&edit=' . $ownerId); exit;
    }
}

// Fetch list acara
$rows = [];
if ($q = $mysqli->query('SELECT id, judul, tanggal, lokasi, status, created_at FROM acara_new ORDER BY id DESC')) {
    while ($r = $q->fetch_assoc()) { $rows[] = $r; }
    $q->close();
}
?>
<div class="card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; gap:8px;">
    <h3>Acara Sekolah</h3>
    <div style="display:flex; gap:8px;">
      <a href="#" class="btn" onclick="document.getElementById('acara-create').style.display='block'">Tambah</a>
      <form method="post" action="?page=acara" onsubmit="return confirm('Gabungkan semua foto acara ke album \"Acara Sekolah\"?')">
        <input type="hidden" name="action" value="merge_acara_albums">
        <button type="submit" class="btn" title="Sinkronkan album 'Acara Sekolah'">Sinkronkan Album</button>
      </form>
    </div>
  </div>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Judul</th>
        <th>Kategori</th>
        <th>Ringkasan</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <?php
        $dibuat = ($r['created_at'] ?? '') ? date('Y-m-d H:i:s', strtotime($r['created_at'])) : '-';
        // Fetch short description for summary (ringkasan)
        $ringkasan = '';
        if ($st = $mysqli->prepare('SELECT deskripsi FROM acara_new WHERE id=? LIMIT 1')) {
            $idtmp = (int)$r['id'];
            $st->bind_param('i', $idtmp);
            $st->execute();
            $resD = $st->get_result();
            if ($d = $resD->fetch_assoc()) {
                $ringkasan = trim(strip_tags((string)($d['deskripsi'] ?? '')));
            }
            $st->close();
        }
        if (strlen($ringkasan) > 40) { $ringkasan = substr($ringkasan, 0, 40) . '...'; }
      ?>
      <tr>
        <td><?php echo e((string)$r['id']); ?></td>
        <td><?php echo e($r['judul']); ?></td>
        <td>
          <span style="display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:12px; font-weight:600; color:#155e75; background:#e0f2fe; border:1px solid #bae6fd;">Acara Sekolah</span>
        </td>
        <td class="muted"><?php echo e($ringkasan !== '' ? $ringkasan : '—'); ?></td>
        <td class="muted"><?php echo e($dibuat); ?></td>
        <td class="actions">
          <a href="?page=acara&edit=<?php echo e((string)$r['id']); ?>" class="btn">Edit</a>
          <form method="post" action="?page=acara" onsubmit="return confirm('Hapus acara ini?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
            <button class="btn danger" type="submit">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Create -->
<div id="acara-create" class="card" style="margin-top:12px; display:none;">
  <h3>Tambah Acara</h3>
  <form method="post" action="?page=acara">
    <input type="hidden" name="action" value="create">
    <div style="margin-bottom:8px;"><label>Judul</label><input type="text" name="judul" required style="width:100%; padding:8px;"></div>
    <div style="margin-bottom:8px;"><label>Isi</label><textarea name="deskripsi" rows="8" style="width:100%; padding:8px;"></textarea></div>
    <button class="btn" type="submit">Simpan</button>
  </form>
  <div style="margin-top:8px;"><a href="#" onclick="this.closest('#acara-create').style.display='none'">Tutup</a></div>
</div>

<?php if (isset($_GET['edit'])):
  $editId = (int) $_GET['edit'];
  $detail = null;
  if ($editId > 0) {
      $st = $mysqli->prepare('SELECT * FROM acara_new WHERE id=? LIMIT 1');
      $st->bind_param('i', $editId);
      $st->execute();
      $detail = $st->get_result()->fetch_assoc();
      $st->close();
  }
?>
<div class="card" style="margin-top:12px;">
  <h3>Edit Acara</h3>
  <?php if ($detail): ?>
  <form method="post" action="?page=acara">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo e((string)$detail['id']); ?>">
    <!-- Visible fields (match requested simple layout) -->
    <div style="margin-bottom:8px;"><label>Judul</label><input type="text" name="judul" required style="width:100%; padding:8px;" value="<?php echo e($detail['judul']); ?>"></div>
    <div style="margin-bottom:8px;"><label>Deskripsi</label><textarea name="deskripsi" rows="6" style="width:100%; padding:8px;"><?php echo e($detail['deskripsi'] ?? ''); ?></textarea></div>

    <!-- Preserve these values without showing -->
    <input type="hidden" name="tanggal" value="<?php echo e((string)($detail['tanggal'] ?? '')); ?>">
    <input type="hidden" name="lokasi" value="<?php echo e((string)($detail['lokasi'] ?? '')); ?>">
    <input type="hidden" name="status" value="<?php echo e((string)($detail['status'] ?? 'published')); ?>">
    <button class="btn" type="submit">Update</button>
  </form>
  <?php
    $foto = [];
    $res = $mysqli->query("SELECT id, file_path, caption, created_at FROM photos WHERE related_type='acara' ORDER BY id DESC");
    if ($res) {
        while ($p = $res->fetch_assoc()) { $foto[] = $p; }
        $res->close();
    }
  ?>
  <div class="card" style="margin-top:16px;">
    <h4>Tambah Foto</h4>
    <form method="post" action="?page=acara" enctype="multipart/form-data">
      <input type="hidden" name="action" value="upload_foto">
      <input type="hidden" name="acara_id" value="<?php echo e((string)$detail['id']); ?>">
      <div style="margin-bottom:12px;">
        <label>Pilih Foto (Multiple):</label>
        <input type="file" id="foto-acara" name="foto[]" multiple accept="image/*" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:4px;">
      </div>
      <div id="photo-titles-acara" style="margin-bottom:12px;"></div>
      <button class="btn" type="submit">Upload Foto</button>
    </form>
  </div>
  <div class="card" style="margin-top:16px;">
    <h4>Foto Terkait</h4>
    <?php if (empty($foto)): ?>
      <p class="muted">Belum ada foto.</p>
    <?php else: ?>
      <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
        <?php foreach ($foto as $ph): ?>
        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; text-align:center;">
          <?php $base = ((isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:'http')).'://'.($_SERVER['HTTP_HOST']??'localhost'); ?>
          <img src="<?php echo e($base . '/' . ltrim((string)$ph['file'], '/')); ?>" alt="<?php echo e($ph['judul']); ?>" style="width:100%; height:150px; object-fit:cover; border-radius:4px; margin-bottom:8px;" onerror="this.onerror=null; this.src='<?php echo e($base); ?>/images/default-berita.jpg';">
          <h5><?php echo e($ph['judul'] ?: 'Tanpa judul'); ?></h5>
          <p class="muted"><?php echo e($ph['created_at'] ?? '-'); ?></p>
          <form method="post" action="?page=acara" style="display:inline" onsubmit="return confirm('Hapus foto ini?')">
            <input type="hidden" name="action" value="delete_photo">
            <input type="hidden" name="id" value="<?php echo e((string)$ph['id']); ?>">
            <input type="hidden" name="acara_id" value="<?php echo e((string)$detail['id']); ?>">
            <button class="btn" style="background:#ef4444; font-size:12px;" type="submit">Hapus</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <?php else: ?><p>Data tidak ditemukan.</p><?php endif; ?>
</div>
<?php endif; ?>

<script>
document.addEventListener('change', function(e){
  if (e.target && e.target.id === 'foto-acara') {
    const container = document.getElementById('photo-titles-acara');
    container.innerHTML = '';
    const files = e.target.files || [];
    for (let i = 0; i < files.length; i++) {
      const div = document.createElement('div');
      div.style.marginBottom = '8px';
      div.innerHTML = `
        <label>Judul Foto ${i + 1}:</label>
        <input type="text" name="photo_judul[]" placeholder="Masukkan judul foto" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:4px;">
      `;
      container.appendChild(div);
    }
  }
});
</script>

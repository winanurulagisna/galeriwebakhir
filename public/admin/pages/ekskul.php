<?php
// CRUD Ekstrakurikuler: galery with category = 'ekstrakurikuler'

// Handle AJAX get request for edit
if (isset($_GET['ajax']) && $_GET['ajax'] === '1' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        $st = $mysqli->prepare('SELECT id, title as judul, description as deskripsi FROM galery WHERE id=? AND category="ekstrakurikuler" LIMIT 1');
        $st->bind_param('i', $id);
        $st->execute();
        $result = $st->get_result();
        if ($row = $result->fetch_assoc()) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'data' => $row]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Data tidak ditemukan']);
        }
        $st->close();
        exit;
    }
}

if (isPost()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        if ($judul !== '') {
            $st = $mysqli->prepare('INSERT INTO galery (title, description, category, status, created_at, updated_at) VALUES (?, ?, "ekstrakurikuler", "active", NOW(), NOW())');
            $st->bind_param('ss', $judul, $deskripsi);
            $st->execute();
            $newId = $st->insert_id;
            $st->close();
            // For AJAX inline create, return JSON
            if (($_POST['ajax'] ?? '') === '1') {
                $q = $mysqli->prepare('SELECT id, title as judul, LEFT(description,150) AS ringkas, created_at FROM galery WHERE id=?');
                $q->bind_param('i', $newId);
                $q->execute();
                $row = $q->get_result()->fetch_assoc();
                $q->close();
                header('Content-Type: application/json');
                echo json_encode(['ok'=>true,'row'=>$row]);
                exit;
            }
        }
        header('Location: ?page=ekskul'); exit;
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        if ($id > 0 && $judul !== '') {
            $st = $mysqli->prepare('UPDATE galery SET title=?, description=?, updated_at=NOW() WHERE id=? AND category="ekstrakurikuler"');
            $st->bind_param('ssi', $judul, $deskripsi, $id);
            $st->execute();
            $st->close();
            if (($_POST['ajax'] ?? '') === '1') {
                $q = $mysqli->prepare('SELECT id, title as judul, LEFT(description,150) AS ringkas, created_at FROM galery WHERE id=?');
                $q->bind_param('i', $id);
                $q->execute();
                $row = $q->get_result()->fetch_assoc();
                $q->close();
                header('Content-Type: application/json');
                echo json_encode(['ok'=>true,'row'=>$row]);
                exit;
            }
        }
        header('Location: ?page=ekskul'); exit;
    } elseif ($action === 'upload_foto') {
        $galeryId = (int)($_POST['galery_id'] ?? 0);
        if ($galeryId > 0 && isset($_FILES['foto']) && !empty($_FILES['foto']['name'][0])) {
            $uploadDir = '../images/';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
            for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                if ($_FILES['foto']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . '_' . basename($_FILES['foto']['name'][$i]);
                    $destPath = $uploadDir . $fileName;
                    $judulFoto = trim($_POST['photo_judul'][$i] ?? '');
                    if (move_uploaded_file($_FILES['foto']['tmp_name'][$i], $destPath)) {
                        $webPath = '/images/' . $fileName;
                        $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file, judul, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
                        $st->bind_param('iss', $galeryId, $webPath, $judulFoto);
                        $st->execute();
                        $st->close();
                    }
                }
            }
        }
        header('Location: ?page=ekskul&edit=' . $galeryId); exit;
    } elseif ($action === 'delete_photo') {
        $photoId = (int)($_POST['id'] ?? 0);
        $ownerId = (int)($_POST['galery_id'] ?? 0);
        if ($photoId > 0) {
            $st = $mysqli->prepare('SELECT file_path FROM photos WHERE id=? LIMIT 1');
            $st->bind_param('i', $photoId);
            $st->execute();
            $resPhoto = $st->get_result();
            if ($row = $resPhoto->fetch_assoc()) {
                $fileRef = (string)($row['file_path'] ?? '');
                if ($fileRef !== '' && !preg_match('/^https?:\/\//i', $fileRef)) {
                    $candidate = realpath(__DIR__ . '/../' . ltrim($fileRef, '/'));
                    $publicRoot = realpath(__DIR__ . '/..');
                    if ($candidate && $publicRoot && strncmp($candidate, $publicRoot, strlen($publicRoot)) === 0 && file_exists($candidate)) {
                        @unlink($candidate);
                    }
                }
            }
            $st->close();
            $st = $mysqli->prepare('DELETE FROM photos WHERE id=?');
            $st->bind_param('i', $photoId);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=ekskul&edit=' . $ownerId); exit;
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Delete photos first
            $st = $mysqli->prepare('DELETE FROM photos WHERE gallery_id=?');
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
            
            // Delete gallery
            $st = $mysqli->prepare('DELETE FROM galery WHERE id=? AND category="ekstrakurikuler"');
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=ekskul'); exit;
    }
}

// Fetch list
$rows = [];
$st = $mysqli->prepare('SELECT id, title as judul, LEFT(description,150) AS ringkas, created_at FROM galery WHERE category="ekstrakurikuler" ORDER BY id DESC');
$st->execute();
$res = $st->get_result();
while ($r = $res->fetch_assoc()) { $rows[] = $r; }
$st->close();
?>
<style>
.btn { cursor: pointer; }
.btn:hover { opacity: 0.9; }
</style>
<script>
// Define global functions IMMEDIATELY before any HTML that uses them
// Edit button redirects to edit page where user can edit title, description, and add photos
window.ekskulEdit = function(btn) {
  const id = btn.getAttribute('data-id');
  if (!id) {
    alert('ID ekstrakurikuler tidak ditemukan');
    return false;
  }
  // Redirect to edit page with all features (edit judul, deskripsi, tambah foto)
  window.location.href = '?page=ekskul&edit=' + id;
  return false;
};
</script>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h3>Ekstrakurikuler</h3>
    </div>
    <table id="tbl-ekskul">
        <thead><tr><th>ID</th><th>Judul</th><th>Ringkasan</th><th>Dibuat</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr data-row-id="<?php echo e((string)$r['id']); ?>">
                <td class="col-id"><?php echo e((string)$r['id']); ?></td>
                <td class="col-judul"><?php echo e($r['judul']); ?></td>
                <td class="col-ringkas muted"><?php echo e($r['ringkas'] ?? ''); ?></td>
                <td class="col-created muted"><?php echo e($r['created_at'] ?? '-'); ?></td>
                <td class="col-actions">
                    <button type="button" class="btn btn-edit" data-id="<?php echo e((string)$r['id']); ?>" onclick="ekskulEdit(this)">Edit</button>
                    <form method="post" action="?page=ekskul" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
                        <button class="btn" style="background:#ef4444" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Create -->
<style>
#post-create.as-modal{position:fixed; inset:0; background:rgba(15,23,42,.45); backdrop-filter:blur(2px); display:flex !important; align-items:center; justify-content:center; z-index:1000; margin:0 !important;}
#post-create.as-modal .modal-panel{background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 20px 40px rgba(2,56,89,.18); width:560px; max-width:92%; padding:16px;}
#post-create.as-modal h3{margin-top:0}
</style>
<div id="post-create" class="card" style="margin-top:12px; display:none;">
    <div class="modal-panel">
    <h3>Tambah Ekstrakurikuler</h3>
    <form method="post" action="?page=ekskul">
        <input type="hidden" name="action" value="create">
        <div style="margin-bottom:8px;"><label>Judul</label><input type="text" name="judul" required style="width:100%; padding:8px;"></div>
        <div style="margin-bottom:8px;"><label>Deskripsi</label><textarea name="deskripsi" rows="8" style="width:100%; padding:8px;"></textarea></div>
        <button class="btn" type="submit">Simpan</button>
    </form>
    <div style="margin-top:8px;"><a href="#" onclick="(function(el){ el.classList.remove('as-modal'); el.style.display='none'; })(document.getElementById('post-create'))">Tutup</a></div>
    </div>
</div>

<?php if (isset($_GET['edit'])):
    $editId = (int) $_GET['edit'];
    $detail = null;
    if ($editId > 0) {
        $st = $mysqli->prepare('SELECT id, title as judul, description as isi FROM galery WHERE id=? AND category="ekstrakurikuler" LIMIT 1');
        $st->bind_param('i', $editId);
        $st->execute();
        $detail = $st->get_result()->fetch_assoc();
        $st->close();
    }
?>
<div class="card" style="margin-top:12px;">
    <h3>Edit Ekstrakurikuler</h3>
    <?php if ($detail): ?>
    <form method="post" action="?page=ekskul">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo e((string)$detail['id']); ?>">
        <div style="margin-bottom:8px;"><label>Judul</label><input type="text" name="judul" required style="width:100%; padding:8px;" value="<?php echo e($detail['judul']); ?>"></div>
        <div style="margin-bottom:8px;"><label>Deskripsi</label><textarea name="deskripsi" rows="8" style="width:100%; padding:8px;"><?php echo e($detail['isi'] ?? ''); ?></textarea></div>
        <button class="btn" type="submit">Update</button>
    </form>
    <?php
    $foto = [];
    // Get photos from two sources:
    // 1. Direct ekstrakurikuler photos (related_type="ekskul")
    // 2. Photos from galery with category="ekstrakurikuler"
    
    // Get photos from galery with category="ekstrakurikuler"
    $st = $mysqli->prepare('SELECT f.id, f.file, f.judul, f.created_at, "galery" as source FROM foto f 
                           INNER JOIN galery g ON g.id = f.galery_id 
                           WHERE g.category = "ekstrakurikuler" AND g.status = "active" 
                           ORDER BY f.id DESC');
    $st->execute();
    $resP = $st->get_result();
    while ($p = $resP->fetch_assoc()) { $foto[] = $p; }
    $st->close();
    
    // Sort by ID descending
    usort($foto, function($a, $b) { return $b['id'] - $a['id']; });
    ?>
    <div class="card" style="margin-top:16px;">
        <h4>Tambah Foto</h4>
        <form method="post" action="?page=ekskul" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_foto">
            <input type="hidden" name="galery_id" value="<?php echo e((string)$detail['id']); ?>">
            <div style="margin-bottom:12px;">
                <label>Pilih Foto (Multiple):</label>
                <input type="file" id="foto-ekskul" name="foto[]" multiple accept="image/*" required style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:4px;">
            </div>
            <div id="photo-titles-ekskul" style="margin-bottom:12px;"></div>
            <button class="btn" type="submit">Upload Foto</button>
        </form>
    </div>
    <div class="card" style="margin-top:16px;">
        <h4>Foto Terkait</h4>
        <p style="font-size:13px; color:#6b7280; margin-bottom:12px; padding:8px; background:#f8fafc; border-radius:6px;">
            💡 <strong>Info:</strong> Halaman ini menampilkan semua foto ekstrakurikuler dari galeri dengan kategori "ekstrakurikuler".<br>
            Foto dapat diupload langsung di sini atau melalui menu "Kelola Foto" → pilih galeri berkategori "Ekstrakurikuler".
        </p>
        <?php if (empty($foto)): ?>
            <p class="muted">Belum ada foto. Silakan upload foto di sini atau melalui menu "Kelola Foto" → pilih galeri berkategori "Ekstrakurikuler".</p>
        <?php else: ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                <?php foreach ($foto as $ph): ?>
                <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; text-align:center;">
                    <img src="<?php echo e('/' . ltrim($ph['file'], '/')); ?>" alt="<?php echo e($ph['judul']); ?>" style="width:100%; height:150px; object-fit:cover; border-radius:4px; margin-bottom:8px;">
                    <h5><?php echo e($ph['judul'] ?: 'Tanpa judul'); ?></h5>
                    <p class="muted"><?php echo e($ph['created_at'] ?? '-'); ?></p>
                    <p style="font-size:11px; color:#6b7280; margin:4px 0;">
                        <span style="background:#e0f2fe; color:#0277bd; padding:2px 6px; border-radius:10px;">📁 Galeri Ekstrakurikuler</span>
                    </p>
                    <form method="post" action="?page=ekskul" style="display:inline" onsubmit="return confirm('Hapus foto ini?')">
                        <input type="hidden" name="action" value="delete_photo">
                        <input type="hidden" name="id" value="<?php echo e((string)$ph['id']); ?>">
                        <input type="hidden" name="galery_id" value="<?php echo e((string)$detail['id']); ?>">
                        <button class="btn" style="background:#ef4444; font-size:12px;" type="submit">Hapus</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

<script>
// Photo upload title fields generator (for edit page)
document.addEventListener('DOMContentLoaded', function(){
  const photoInput = document.getElementById('foto-ekskul');
  if (photoInput) {
    photoInput.addEventListener('change', function(){
      const container = document.getElementById('photo-titles-ekskul');
      if (!container) return;
      container.innerHTML = '';
      for (let i = 0; i < this.files.length; i++) {
        const div = document.createElement('div');
        div.style.marginBottom = '8px';
        div.innerHTML = `<label>Judul foto ${i+1} (${this.files[i].name}):</label><input type="text" name="photo_judul[]" style="width:100%; padding:6px; border:1px solid #d1d5db; border-radius:4px;" />`;
        container.appendChild(div);
      }
    });
  }
});
</script>
    </div>
    <?php else: ?><p>Data tidak ditemukan.</p><?php endif; ?>
</div>
<?php endif; ?>



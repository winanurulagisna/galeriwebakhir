<?php
// Photos CRUD: photos (id, gallery_id, file, judul, created_at, updated_at)

$message = '';
$messageType = '';

// Handle POST actions
if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $galery_id = (int)($_POST['galery_id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        
        if ($galery_id > 0 && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../images/';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

            $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
            $destPath = $uploadDir . $fileName;
            $fileWebPath = '/images/' . $fileName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
                try {
                    $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file, judul, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
                    $st->bind_param('iss', $galery_id, $fileWebPath, $judul);
                    $st->execute();
                    $st->close();
                    $message = 'Foto berhasil ditambahkan!';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            } else {
                $message = 'Gagal mengunggah file.';
                $messageType = 'error';
            }
        } else {
            $message = 'Pilih galeri dan file terlebih dahulu!';
            $messageType = 'error';
        }
        header('Location: ?page=photos'); exit;

    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $galery_id = (int)($_POST['galery_id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');

        if ($id > 0 && $galery_id > 0) {
            try {
                // Optional file replace
                if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                    // get old file to delete
                    $st = $mysqli->prepare('SELECT file_path FROM photos WHERE id=?');
                    $st->bind_param('i', $id);
                    $st->execute();
                    $result = $st->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $oldFs = '../' . ltrim($row['file_path'], '/');
                        if (!empty($row['file_path']) && file_exists($oldFs)) { unlink($oldFs); }
                    }
                    $st->close();
                    $uploadDir = '../images/';
                    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                    $fileName = uniqid() . '_' . basename($_FILES['file']['name']);
                    $destPath = $uploadDir . $fileName;
                    $fileWebPath = '/images/' . $fileName;
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
                        $st = $mysqli->prepare('UPDATE photos SET gallery_id=?, file=?, judul=?, updated_at=NOW() WHERE id=?');
                        $st->bind_param('issi', $galery_id, $fileWebPath, $judul, $id);
                        $st->execute();
                        $st->close();
                    }
                } else {
                    $st = $mysqli->prepare('UPDATE photos SET gallery_id=?, judul=?, updated_at=NOW() WHERE id=?');
                    $st->bind_param('isi', $galery_id, $judul, $id);
                    $st->execute();
                    $st->close();
                }

                $message = 'Foto berhasil diperbarui!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        header('Location: ?page=photos'); exit;

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                // delete file
                $st = $mysqli->prepare('SELECT file_path FROM photos WHERE id=?');
                $st->bind_param('i', $id);
                $st->execute();
                $result = $st->get_result();
                if ($row = $result->fetch_assoc()) {
                    $fileRef = (string)($row['file_path'] ?? '');
                    // Only delete if it's a local file under public directory
                    if ($fileRef !== '' && !preg_match('/^https?:\/\//i', $fileRef)) {
                        $candidate = realpath(__DIR__ . '/../' . ltrim($fileRef, '/'));
                        $publicRoot = realpath(__DIR__ . '/..');
                        if ($candidate && $publicRoot && strncmp($candidate, $publicRoot, strlen($publicRoot)) === 0 && file_exists($candidate)) {
                            @unlink($candidate);
                        }
                    }
                }
                $st->close();

                // delete db row
                $st = $mysqli->prepare('DELETE FROM photos WHERE id=?');
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();

                $message = 'Foto berhasil dihapus!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        header('Location: ?page=photos'); exit;
    }
}

// Fetch galery for select options
$galery = [];
$res = $mysqli->query('SELECT id, title as judul FROM galleries ORDER BY title ASC');
if ($res) { while ($r = $res->fetch_assoc()) { $galery[] = $r; } $res->close(); }

// Fetch foto list
$rows = [];
$res = $mysqli->query('SELECT p.id, p.gallery_id, p.file_path, p.judul, p.created_at, g.title as galleries FROM photos p LEFT JOIN galleries g ON g.id=p.gallery_id ORDER BY p.id DESC');
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->close(); }

// Get current photo for editing
$current = ['id' => '', 'gallery_id' => '', 'judul' => ''];
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $res = $mysqli->query("SELECT id, gallery_id, file_path, judul FROM photos WHERE id = $editId LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) { $current = $row; }
    if ($res) { $res->close(); }
}
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h3>Kelola Foto</h3>
        <a href="#" class="btn" onclick="(function(){ const el=document.getElementById('photo-create'); if(!el) return false; el.style.display='block'; setTimeout(function(){ try{ el.scrollIntoView({behavior:'smooth', block:'start'}); const first=el.querySelector('#galery_id'); if(first) first.focus(); }catch(e){} }, 0); return false; })();">Tambah Foto</a>
    </div>

    <?php if ($message): ?>
        <div style="padding: 12px; margin-bottom: 16px; border-radius: 8px; <?php echo $messageType === 'success' ? 'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;'; ?>">
            <?php echo e($message); ?>
        </div>
    <?php endif; ?>

    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <thead>
            <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">ID</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Galeri</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Preview</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Judul</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Dibuat</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Aksi
                    <button id="btn-add-photo" type="button" class="btn" style="margin-left:8px; padding:6px 10px; font-size:12px;">Tambah</button>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr style="border-bottom:1px solid #e5e7eb; transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                <td style="padding:12px; color:#6b7280; font-weight:500;"><?php echo e((string)$r['id']); ?></td>
                <td style="padding:12px;">
                    <span style="background:#e0f2fe; color:#0277bd; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:500;"><?php echo e($r['galleries'] ?? ('#' . (string)$r['gallery_id'])); ?></span>
                </td>
                <td style="padding:12px;">
                    <?php if (!empty($r['file_path'])): ?>
                        <img src="http://localhost:8000<?php echo e($r['file_path']); ?>" alt="<?php echo e($r['judul']); ?>" style="width:80px; height:60px; object-fit:cover; border-radius:8px; border:2px solid #e5e7eb;">
                    <?php else: ?>
                        <div style="width:80px; height:60px; background:#f3f4f6; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:12px;">No Image</div>
                    <?php endif; ?>
                </td>
                <td style="padding:12px; font-weight:500; color:#111827; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo e($r['judul'] ?? ''); ?>"><?php echo e($r['judul'] ?? ''); ?></td>
                <td style="padding:12px; color:#6b7280; font-size:14px;"><?php echo e($r['created_at'] ?? '-'); ?></td>
                <td style="padding:12px;">
                    <div style="display:flex; gap:8px;">
                        <a href="?page=photos&edit=<?php echo e((string)$r['id']); ?>" class="btn" style="padding:6px 12px; font-size:12px;">Edit</a>
                        <form method="post" action="?page=photos" style="display:inline" onsubmit="return confirm('Hapus foto ini?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
                            <button class="btn" style="background:#ef4444; padding:6px 12px; font-size:12px;" type="submit">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Create/Edit Form -->
<style>
/* modal overlay for create form */
#photo-create.as-modal{position:fixed; inset:0; background:rgba(15,23,42,.45); backdrop-filter:blur(2px); display:flex !important; align-items:center; justify-content:center; z-index:1000; margin:0 !important;}
#photo-create.as-modal .modal-panel{background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 20px 40px rgba(2,56,89,.18); width: 560px; max-width: 92%; padding:16px;}
#photo-create.as-modal h3{margin-top:0}
</style>
<div id="photo-create" class="card" style="margin-top:12px; <?php echo isset($_GET['edit']) ? 'display:block;' : 'display:none'; ?>">
    <div class="modal-panel">
    <h3><?php echo isset($_GET['edit']) ? 'Edit Foto' : 'Tambah Foto'; ?></h3>
    <form method="post" action="?page=photos" enctype="multipart/form-data" onsubmit="return validatePhotoForm()">
        <input type="hidden" name="action" value="<?php echo isset($_GET['edit']) ? 'update' : 'create'; ?>">
        <?php if (isset($_GET['edit'])): ?>
            <input type="hidden" name="id" value="<?php echo e($current['id']); ?>">
        <?php endif; ?>

        <div style="margin-bottom:16px;">
            <label for="galery_id" style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Galeri *</label>
            <select id="galery_id" name="galery_id" required style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px; background:white; transition:border-color 0.2s;">
                <option value="">-- Pilih Galeri --</option>
                <?php foreach ($galery as $g): ?>
                <option value="<?php echo e($g['id']); ?>" <?php echo ((string)$current['galery_id'] === (string)$g['id']) ? 'selected' : ''; ?>><?php echo e($g['judul']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:16px;">
            <label for="judul" style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Judul</label>
            <input type="text" id="judul" name="judul" value="<?php echo e($current['judul']); ?>" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px; transition:border-color 0.2s;">
        </div>

        <div style="margin-bottom:16px;">
            <label for="file" style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">File <?php echo isset($_GET['edit']) ? '(kosongkan jika tidak diganti)' : '*'; ?></label>
            <input type="file" id="file" name="file" <?php echo isset($_GET['edit']) ? '' : 'required'; ?> accept="image/*" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px; transition:border-color 0.2s;">
            
            <?php if (isset($_GET['edit']) && !empty($current['file'])): ?>
                <div style="margin-top:12px;">
                    <p style="margin-bottom:8px; color:#6b7280; font-size:14px;">Preview saat ini:</p>
                    <img src="http://localhost:8000<?php echo e($current['file']); ?>" alt="preview" style="width:150px; height:112px; object-fit:cover; border-radius:8px; border:2px solid #e5e7eb;">
                </div>
            <?php endif; ?>
        </div>

        <div style="display:flex; gap:12px; margin-top:20px;">
            <button class="btn" type="submit" style="padding:12px 24px; font-size:14px; font-weight:600;">Simpan</button>
            <button type="button" class="btn" onclick="resetForm()" style="background:#6b7280; padding:12px 24px; font-size:14px; font-weight:600;">Reset</button>
            <button type="button" class="btn" onclick="(function(){const el=document.getElementById('photo-create'); if(!el) return; el.classList.remove('as-modal'); el.style.display='none';})();" style="background:#9ca3af; padding:12px 24px; font-size:14px; font-weight:600;">Tutup</button>
        </div>
    </form>
    </div>
</div>

<script>
function validatePhotoForm() {
    const galleryId = document.getElementById('galery_id').value;
    const fileInput = document.getElementById('file');
    
    if (!galleryId) {
        alert('Pilih galeri terlebih dahulu!');
        return false;
    }
    
    // Check if file is required (for create mode)
    const isEditMode = <?php echo isset($_GET['edit']) ? 'true' : 'false'; ?>;
    if (!isEditMode && !fileInput.files.length) {
        alert('Pilih file foto terlebih dahulu!');
        return false;
    }
    
    return true;
}

function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form?')) {
        document.getElementById('galery_id').value = '';
        document.getElementById('judul').value = '';
        document.getElementById('file').value = '';
    }
}

// Add hover effects to form inputs
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#3b82f6';
        });
        input.addEventListener('blur', function() {
            this.style.borderColor = '#d1d5db';
        });
    });

    // Open create form as modal if visible
    const openCreateBtn = document.querySelector('a.btn[href="#"]');
    function openCreateModal(e){
        if (e) e.preventDefault();
        const el = document.getElementById('photo-create');
        if (!el) return;
        el.style.display = 'block';
        el.classList.add('as-modal');
        const first = el.querySelector('#galery_id');
        if (first) setTimeout(()=> first.focus(), 50);
    }
    if (openCreateBtn) openCreateBtn.addEventListener('click', openCreateModal);
    const btnAddHeader = document.getElementById('btn-add-photo');
    if (btnAddHeader) btnAddHeader.addEventListener('click', openCreateModal);
});
</script>

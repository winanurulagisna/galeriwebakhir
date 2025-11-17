<?php
// CRUD Berita Terkini: lock kategori to "Berita Terkini" so items tidak hilang dari daftar setelah di-update

// Pastikan ID kategori untuk "Berita Terkini" selalu konsisten
$beritaKategoriId = ensureKategoriId($mysqli, 'Berita Terkini', 1);

// Pastikan galeri default untuk "Berita Terkini" selalu ada
function ensureBeritaGallery($mysqli) {
    // Cek apakah galeri "Berita Terkini" sudah ada
    $st = $mysqli->prepare('SELECT id FROM galleries WHERE title = ? AND category = ? LIMIT 1');
    $title = 'Berita Terkini';
    $category = 'berita';
    $st->bind_param('ss', $title, $category);
    $st->execute();
    $result = $st->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $st->close();
        return (int)$row['id'];
    }
    $st->close();
    
    // Jika belum ada, buat galeri baru
    $st = $mysqli->prepare('INSERT INTO galleries (title, caption, category, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
    $caption = 'Galeri foto untuk berita terkini sekolah';
    $status = 'published';
    $st->bind_param('ssss', $title, $caption, $category, $status);
    $st->execute();
    $newId = $mysqli->insert_id;
    $st->close();
    
    return $newId;
}

$defaultBeritaGalleryId = ensureBeritaGallery($mysqli);

// Cek apakah kolom `url` tersedia pada tabel posts_new
$hasUrlColumn = false;
if ($q = $mysqli->query("SHOW COLUMNS FROM posts_new LIKE 'url'")) {
    $hasUrlColumn = $q->num_rows > 0; $q->close();
}

if (isPost()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        // Paksa kategori ke Berita Terkini agar tidak keluar dari halaman ini
        $kategoriId = (int)$beritaKategoriId;
        $sumber = trim($_POST['sumber_url'] ?? '');
        if ($sumber !== '' && !preg_match('~^https?://~i', $sumber)) {
            // Auto-prepend scheme if missing
            $sumber = 'http://' . $sumber;
        }
        // Jangan masukkan URL ke kolom isi; simpan ke kolom url saja
        
        if ($judul !== '' && $kategoriId > 0) {
            if ($hasUrlColumn) {
                // Simpan ke kolom url jika tersedia
                $st = $mysqli->prepare('INSERT INTO posts_new (judul, isi, url, kategori_id, status, views, created_at, updated_at) VALUES (?, ?, ?, ?, "published", 0, NOW(), NOW())');
                $st->bind_param('sssi', $judul, $isi, $sumber, $kategoriId);
            } else {
                // Fallback tanpa kolom url
                $st = $mysqli->prepare('INSERT INTO posts_new (judul, isi, kategori_id, status, views, created_at, updated_at) VALUES (?, ?, ?, "published", 0, NOW(), NOW())');
                $st->bind_param('ssi', $judul, $isi, $kategoriId);
            }
            $st->execute();
            $newPostId = $mysqli->insert_id;
            $st->close();
            
            // Upload foto jika ada - gunakan galeri default untuk berita terkini
            if ($newPostId > 0 && isset($_FILES['foto_create']) && !empty($_FILES['foto_create']['name'][0])) {
                $uploadDir = '../images/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                
                for ($i = 0; $i < count($_FILES['foto_create']['name']); $i++) {
                    if ($_FILES['foto_create']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileName = uniqid() . '_' . basename($_FILES['foto_create']['name'][$i]);
                        $destPath = $uploadDir . $fileName;
                        $judulFoto = trim($_POST['photo_judul_create'][$i] ?? '');
                        
                        if (move_uploaded_file($_FILES['foto_create']['tmp_name'][$i], $destPath)) {
                            $webPath = '/images/' . $fileName;
                            $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file_path, caption, related_type, related_id, created_at, updated_at) VALUES (?, ?, ?, "berita", ?, NOW(), NOW())');
                            $st->bind_param('issi', $defaultBeritaGalleryId, $webPath, $judulFoto, $newPostId);
                            $st->execute();
                            $st->close();
                        }
                    }
                }
            }
        }
        header('Location: ?page=berita'); exit;
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $kategoriId = (int)($_POST['kategori_id'] ?? 0);
        $sumber = trim($_POST['sumber_url'] ?? '');
        if ($sumber !== '' && !preg_match('~^https?://~i', $sumber)) {
            $sumber = 'http://' . $sumber;
        }
        // Jangan menyisipkan URL ke kolom isi; hanya perbarui kolom url
        
        if ($id > 0 && $judul !== '' && $kategoriId > 0) {
            if ($hasUrlColumn) {
                $st = $mysqli->prepare('UPDATE posts_new SET judul=?, isi=?, url=?, kategori_id=?, updated_at=NOW() WHERE id=?');
                $st->bind_param('sssii', $judul, $isi, $sumber, $kategoriId, $id);
            } else {
                $st = $mysqli->prepare('UPDATE posts_new SET judul=?, isi=?, kategori_id=?, updated_at=NOW() WHERE id=?');
                $st->bind_param('ssii', $judul, $isi, $kategoriId, $id);
            }
            $st->execute();
            $st->close();
        }
        header('Location: ?page=berita'); exit;
    } elseif ($action === 'upload_foto') {
        $postId = (int)($_POST['post_id'] ?? 0);
        
        if ($postId > 0 && isset($_FILES['foto']) && !empty($_FILES['foto']['name'][0])) {
            $uploadDir = '../images/';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
            for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                if ($_FILES['foto']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . '_' . basename($_FILES['foto']['name'][$i]);
                    $destPath = $uploadDir . $fileName;
                    $judulFoto = trim($_POST['photo_judul'][$i] ?? '');
                    if (move_uploaded_file($_FILES['foto']['tmp_name'][$i], $destPath)) {
                        $webPath = '/images/' . $fileName;
                        $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file_path, caption, related_type, related_id, created_at, updated_at) VALUES (?, ?, ?, "berita", ?, NOW(), NOW())');
                        $st->bind_param('issi', $defaultBeritaGalleryId, $webPath, $judulFoto, $postId);
                        $st->execute();
                        $st->close();
                    }
                }
            }
        }
        header('Location: ?page=berita&edit=' . $postId); exit;
    } elseif ($action === 'delete_photo') {
        $photoId = (int)($_POST['id'] ?? 0);
        $ownerId = (int)($_POST['post_id'] ?? 0);
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
        header('Location: ?page=berita&edit=' . $ownerId); exit;
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $st = $mysqli->prepare('DELETE FROM posts_new WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=berita'); exit;
    }
}

// Fetch categories for dropdown (tidak lagi dipakai untuk memilih, tapi tetap disiapkan jika diperlukan di tempat lain)
$categories = [];
$st = $mysqli->prepare('SELECT id, judul FROM kategori_new ORDER BY judul ASC');
$st->execute();
$res = $st->get_result();
while ($r = $res->fetch_assoc()) { $categories[] = $r; }
$st->close();

// Fetch list with category info (only Berita Terkini category)
$rows = [];
$st = $mysqli->prepare('SELECT p.id, p.judul, LEFT(p.isi,150) AS ringkas, p.created_at, p.kategori_id, k.judul as kategori_nama FROM posts_new p LEFT JOIN kategori_new k ON p.kategori_id = k.id WHERE p.kategori_id = ? ORDER BY p.id DESC');
$st->bind_param('i', $beritaKategoriId);
$st->execute();
$res = $st->get_result();
while ($r = $res->fetch_assoc()) { $rows[] = $r; }
$st->close();
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h3 style="font-size:16px; margin:0;">Berita Sekolah</h3>
        <a href="#" class="btn" style="padding:5px 10px; font-size:11px;" onclick="document.getElementById('post-create').style.display='block'">Tambah</a>
    </div>
    <style>
    /* Focus highlight for deep-linked activity */
    .focus-glow { background:#ecfdf5 !important; box-shadow: 0 0 0 3px #86efac inset, 0 10px 24px rgba(16,185,129,.15); transition: background .3s ease; }
    </style>
    <table style="width:100%; border-collapse:collapse; margin-top:12px; font-size:12px;">
        <thead>
            <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                <th style="padding:9px 8px; text-align:left; font-weight:600; color:#374151;">ID</th>
                <th style="padding:9px 8px; text-align:left; font-weight:600; color:#374151;">Judul</th>
                <th style="padding:9px 8px; text-align:left; font-weight:600; color:#374151;">Kategori</th>
                <th style="padding:9px 8px; text-align:left; font-weight:600; color:#374151;">Ringkasan</th>
                <th style="padding:9px 8px; text-align:left; font-weight:600; color:#374151;">Dibuat</th>
                <th style="padding:9px 8px; text-align:left; font-weight:600; color:#374151;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr class="berita-row" data-title="<?php echo e($r['judul']); ?>" style="border-bottom:1px solid #e5e7eb; transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                <td style="padding:9px 8px; color:#6b7280; font-weight:500;"><?php echo e((string)$r['id']); ?></td>
                <td style="padding:9px 8px; font-weight:500; color:#111827;"><?php echo e($r['judul']); ?></td>
                <td style="padding:9px 8px;">
                    <span style="background:#e0f2fe; color:#0277bd; padding:4px 10px; border-radius:16px; font-size:11px; font-weight:500; display:inline-block; min-width:88px; text-align:center;"><?php echo e($r['kategori_nama'] ?? 'Tidak ada'); ?></span>
                </td>
                <td style="padding:9px 8px; color:#6b7280; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo e($r['ringkas'] ?? ''); ?></td>
                <td style="padding:9px 8px; color:#6b7280; font-size:12px;"><?php echo e($r['created_at'] ?? '-'); ?></td>
                <td style="padding:9px 8px;">
                    <div style="display:flex; gap:6px;">
                        <a href="?page=berita&edit=<?php echo e((string)$r['id']); ?>" class="btn" style="padding:5px 10px; font-size:11px;">Edit</a>
                    <form method="post" action="?page=berita" style="display:inline" onsubmit="return confirm('Hapus berita ini?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
                            <button class="btn" style="background:#ef4444; padding:5px 10px; font-size:11px;" type="submit">Hapus</button>
                    </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Create -->
<div id="post-create" class="card" style="margin-top:10px; display:none;">
    <h3 style="font-size:16px; margin:0 0 8px;">Tambah Berita</h3>
    <form method="post" action="?page=berita" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Kategori</label>
            <input type="hidden" name="kategori_id" value="<?php echo e((string)$beritaKategoriId); ?>">
            <div style="background:#e0f2fe; color:#0277bd; padding:4px 10px; border-radius:16px; display:inline-block; font-size:11px; font-weight:600;">Berita Terkini</div>
        </div>
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Judul *</label>
            <input type="text" name="judul" required style="width:100%; padding:9px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; transition:border-color 0.2s;">
        </div>
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Isi</label>
            <textarea name="isi" rows="6" style="width:100%; padding:9px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; resize:vertical; transition:border-color 0.2s;"></textarea>
        </div>
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Sumber URL (opsional)</label>
            <input type="text" name="sumber_url" placeholder="https://contoh.com/artikel" style="width:100%; padding:9px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; transition:border-color 0.2s;">
            <p class="muted" style="margin-top:4px; color:#6b7280; font-size:11px;">Jika diisi, tombol di beranda akan menuju ke link ini.</p>
        </div>
        
        <!-- Upload Foto Section -->
        <div style="margin-bottom:12px; padding:12px; background:#f9fafb; border:1px dashed #d1d5db; border-radius:6px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Upload Foto (opsional)</label>
            <p class="muted" style="margin-bottom:8px; color:#6b7280; font-size:11px;">Upload foto untuk berita ini. Foto akan otomatis disimpan ke galeri "Berita Terkini".</p>
            
            <input type="file" id="foto-create" name="foto_create[]" multiple accept="image/*" style="width:100%; padding:6px; border:1px solid #d1d5db; border-radius:4px; font-size:12px;">
            <div id="photo-titles-create" style="margin-top:8px;"></div>
        </div>
        
        <button class="btn" type="submit" style="padding:5px 10px; font-size:11px;">Simpan</button>
    </form>
    <div style="margin-top:6px; font-size:12px;"><a href="#" onclick="this.closest('#post-create').style.display='none'">Tutup</a></div>
</div>

<script>
// Dynamic photo title inputs for create form
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto-create');
    if (fotoInput) {
        fotoInput.addEventListener('change', function() {
            const container = document.getElementById('photo-titles-create');
            container.innerHTML = '';
            
            for (let i = 0; i < this.files.length; i++) {
                const div = document.createElement('div');
                div.style.marginBottom = '6px';
                div.innerHTML = `
                    <label style="display:block; margin-bottom:3px; font-weight:600; color:#374151; font-size:12px;">Judul Foto ${i + 1}:</label>
                    <input type="text" name="photo_judul_create[]" placeholder="Masukkan judul foto" style="width: 100%; padding: 5px; border: 1px solid #d1d5db; border-radius: 4px; font-size:12px;">
                `;
                container.appendChild(div);
            }
        });
    }
});
</script>

<script>
// Highlight targeted news row when navigated from dashboard
(function(){
  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('act_focus') === '1') {
      const needle = (params.get('act_title')||'').toLowerCase().trim();
      let target = null;
      if (needle) {
        document.querySelectorAll('.berita-row').forEach(function(row){
          const t = (row.getAttribute('data-title')||'').toLowerCase();
          if (!target && t.includes(needle)) target = row;
        });
      }
      target = target || document.querySelector('.berita-row');
      if (target) {
        target.classList.add('focus-glow');
        setTimeout(function(){ target.classList.remove('focus-glow'); }, 2500);
        target.scrollIntoView({behavior:'smooth', block:'center'});
      }
    }
  } catch(_){}
})();
</script>

<?php if (isset($_GET['edit'])):
    $editId = (int) $_GET['edit'];
    $detail = null;
    if ($editId > 0) {
        if ($hasUrlColumn) {
            $st = $mysqli->prepare('SELECT id, judul, isi, url, kategori_id FROM posts_new WHERE id=? LIMIT 1');
        } else {
            $st = $mysqli->prepare('SELECT id, judul, isi, kategori_id FROM posts_new WHERE id=? LIMIT 1');
        }
        $st->bind_param('i', $editId);
        $st->execute();
        $detail = $st->get_result()->fetch_assoc();
        $st->close();
    }
?>
<div class="card" style="margin-top:10px;">
    <h3 style="font-size:16px; margin:0 0 8px;">Edit Berita</h3>
    <?php if ($detail): ?>
    <form method="post" action="?page=berita">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo e((string)$detail['id']); ?>">
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Kategori</label>
            <input type="hidden" name="kategori_id" value="<?php echo e((string)$beritaKategoriId); ?>">
            <div style="background:#e0f2fe; color:#0277bd; padding:4px 10px; border-radius:16px; display:inline-block; font-size:11px; font-weight:600;">Berita Terkini</div>
        </div>
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Judul *</label>
            <input type="text" name="judul" required style="width:100%; padding:9px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; transition:border-color 0.2s;" value="<?php echo e($detail['judul']); ?>">
        </div>
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Isi</label>
            <textarea name="isi" rows="6" style="width:100%; padding:9px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; resize:vertical; transition:border-color 0.2s;"><?php echo e($detail['isi'] ?? ''); ?></textarea>
        </div>
        <?php
            // Prefill dari kolom url (jika ada); fallback ke parsing isi bila kosong atau kolom belum ada
            $prefillSource = $hasUrlColumn ? (string)($detail['url'] ?? '') : '';
            if ($prefillSource === '' && !empty($detail['isi']) && preg_match('~https?://[^\s"\']+~i', $detail['isi'], $m)) {
                $prefillSource = $m[0];
            }
        ?>
        <div style="margin-bottom:12px;">
            <label style="display:block; margin-bottom:4px; font-weight:600; color:#374151; font-size:12px;">Sumber URL (opsional)</label>
            <input type="text" name="sumber_url" value="<?php echo e($prefillSource); ?>" placeholder="https://contoh.com/artikel" style="width:100%; padding:9px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; transition:border-color 0.2s;">
            <p class="muted" style="margin-top:4px; color:#6b7280; font-size:11px;">Jika diisi dan belum ada di isi, link akan ditambahkan.</p>
        </div>
        <button class="btn" type="submit" style="padding:5px 10px; font-size:11px;">Update</button>
    </form>
    
    <?php
    // Photos for this berita - filter berdasarkan related_id
    $foto = [];
    $beritaGalleryId = ensureBeritaGallery($mysqli);
    $st = $mysqli->prepare('SELECT id, file_path, caption, created_at FROM photos WHERE related_type="berita" AND related_id=? ORDER BY id DESC');
    $st->bind_param('i', $editId);
    $st->execute();
    $resP = $st->get_result();
    while ($p = $resP->fetch_assoc()) { $foto[] = $p; }
    $st->close();
    ?>
    
    <div class="card" style="margin-top:12px; padding:14px;">
        <h4 style="margin:0 0 6px; font-size:15px;">Tambah Foto</h4>
        <p class="muted" style="margin-bottom:10px; color:#6b7280; font-size:11px;">Foto akan otomatis disimpan ke galeri "Berita Terkini".</p>
        
        <form method="post" action="?page=berita" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_foto">
            <input type="hidden" name="post_id" value="<?php echo e((string)$detail['id']); ?>">
            
            <div style="margin-bottom:10px;">
                <label style="display:block; font-weight:600; font-size:12px; margin-bottom:4px;">Pilih Foto (Multiple):</label>
                <input type="file" id="foto-berita" name="foto[]" multiple accept="image/*" required style="width:100%; padding:6px; border:1px solid #d1d5db; border-radius:4px; font-size:12px;">
            </div>
            <div id="photo-titles-berita" style="margin-bottom:10px;"></div>
            <button class="btn" type="submit" style="padding:5px 10px; font-size:11px;">Upload Foto</button>
        </form>
    </div>

    <div class="card" style="margin-top:12px; padding:14px;">
        <h4 style="margin:0 0 6px; font-size:15px;">Foto Terkait</h4>
        <?php if (empty($foto)): ?>
            <p class="muted" style="font-size:12px;">Belum ada foto.</p>
        <?php else: ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 14px;">
                <?php foreach ($foto as $ph): ?>
                <div style="border:1px solid #e5e7eb; border-radius:6px; padding:10px; text-align:center;">
                    <img src="<?php echo e('/' . ltrim($ph['file_path'], '/')); ?>" alt="<?php echo e($ph['caption'] ?? ''); ?>" style="width:100%; height:140px; object-fit:cover; border-radius:4px; margin-bottom:6px;">
                    <h5 style="margin:0 0 4px; font-size:13px;"><?php echo e($ph['caption'] ?: 'Tanpa caption'); ?></h5>
                    <p class="muted" style="font-size:11px;"><?php echo e($ph['created_at'] ?? '-'); ?></p>
                    <form method="post" action="?page=berita" style="display:inline" onsubmit="return confirm('Hapus foto ini?')">
                        <input type="hidden" name="action" value="delete_photo">
                        <input type="hidden" name="id" value="<?php echo e((string)$ph['id']); ?>">
                        <input type="hidden" name="post_id" value="<?php echo e((string)$detail['id']); ?>">
                        <button class="btn" style="background:#ef4444; font-size:11px; padding:5px 10px;" type="submit">Hapus</button>
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
// Dynamic photo title inputs for berita
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto-berita');
    if (fotoInput) {
        fotoInput.addEventListener('change', function() {
            const container = document.getElementById('photo-titles-berita');
            container.innerHTML = '';
            
            for (let i = 0; i < this.files.length; i++) {
                const div = document.createElement('div');
                div.style.marginBottom = '6px';
                div.innerHTML = `
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:3px;">Judul Foto ${i + 1}:</label>
                    <input type="text" name="photo_judul[]" placeholder="Masukkan judul foto" style="width: 100%; padding: 5px; border: 1px solid #d1d5db; border-radius: 4px; font-size:12px;">
                `;
                container.appendChild(div);
            }
        });
    }
});
</script>


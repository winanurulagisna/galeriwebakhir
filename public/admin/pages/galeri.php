<?php
// Galeri CRUD: galery (id, judul, deskripsi, status) + foto (id, galery_id, file, judul)

$message = '';
$messageType = '';

if (isPost()) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_gallery') {
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $category = trim($_POST['category'] ?? 'Kegiatan Sekolah');
        
        if ($judul !== '') {
            try {
                // Schema: title, caption, status, category
                $st = $mysqli->prepare('INSERT INTO galleries (title, caption, status, category, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
                $st->bind_param('ssss', $judul, $deskripsi, $status, $category);
                $st->execute();
                $st->close();
                $message = 'Galeri berhasil ditambahkan!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message = 'Judul galeri tidak boleh kosong!';
            $messageType = 'error';
        }
        header('Location: ?page=galeri');
        exit;
        
    } elseif ($action === 'update_gallery') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        $category = trim($_POST['category'] ?? 'Kegiatan Sekolah');
        
        if ($id > 0 && $judul !== '') {
            try {
                // Schema: title, caption, status, category
                $st = $mysqli->prepare('UPDATE galleries SET title=?, caption=?, status=?, category=?, updated_at=NOW() WHERE id=?');
                $st->bind_param('ssssi', $judul, $deskripsi, $status, $category, $id);
                $st->execute();
                $st->close();
                $message = 'Galeri berhasil diperbarui!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        header('Location: ?page=galeri');
        exit;
        
    } elseif ($action === 'delete_gallery') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                // Hapus foto terlebih dahulu
                $st = $mysqli->prepare('DELETE FROM photos WHERE gallery_id=?');
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();
                
                // Hapus galeri
                $st = $mysqli->prepare('DELETE FROM galleries WHERE id=?');
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();
                
                $message = 'Galeri dan semua fotonya berhasil dihapus!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        header('Location: ?page=galeri');
        exit;
        
    } elseif ($action === 'upload_foto') {
        $galery_id = (int)($_POST['galery_id'] ?? 0);
        
        // Validasi galery_id tidak boleh kosong
        if ($galery_id <= 0) {
            $message = 'Pilih galeri dulu!';
            $messageType = 'error';
            header('Location: ?page=galeri&view=' . $galery_id . '&error=' . urlencode($message));
            exit;
        }
        
        if ($galery_id > 0 && isset($_FILES['foto']) && !empty($_FILES['foto']['name'][0])) {
            // Cek kategori album untuk auto-flag sebagai 'acara'
            $isAcara = false;
            $stCat = $mysqli->prepare('SELECT category, title FROM galleries WHERE id=? LIMIT 1');
            $stCat->bind_param('i', $galery_id);
            $stCat->execute();
            $resCat = $stCat->get_result();
            if ($rowCat = $resCat->fetch_assoc()) {
                $cat = strtolower(trim((string)($rowCat['category'] ?? '')));
                $title = trim((string)($rowCat['title'] ?? ''));
                if ($cat === 'acara sekolah' || strcasecmp($title, 'Acara Sekolah') === 0) {
                    $isAcara = true;
                }
            }
            $stCat->close();
            $uploadDir = '../images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadedCount = 0;
            $errors = [];

            // Cek apakah kolom created_by ada pada tabel photos (defensif)
            $hasCreatedBy = false;
            if ($chk = $mysqli->query("SHOW COLUMNS FROM photos LIKE 'created_by'")) { $hasCreatedBy = $chk->num_rows > 0; $chk->close(); }
            $petugasId = 0;
            if (isset($_SESSION['petugas_id'])) { $petugasId = (int)$_SESSION['petugas_id']; }
            else if (isset($_SESSION['admin_id'])) { $petugasId = (int)$_SESSION['admin_id']; }
            else if (isset($_SESSION['user_id'])) { $petugasId = (int)$_SESSION['user_id']; }
            
            for ($i = 0; $i < count($_FILES['foto']['name']); $i++) {
                if ($_FILES['foto']['error'][$i] === UPLOAD_ERR_OK) {
                    $originalFileName = basename($_FILES['foto']['name'][$i]);
                    $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
                    
                    // Validasi format file yang didukung
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif', 'avif', 'bmp', 'tiff', 'tif'];
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        $errors[] = "Format file $originalFileName tidak didukung. Format yang didukung: " . implode(', ', array_unique($allowedExtensions));
                        continue;
                    }
                    
                    $fileName = uniqid() . '_' . $originalFileName;
                    $destPath = $uploadDir . $fileName;
                    $judulFoto = trim($_POST['photo_judul'][$i] ?? '');
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'][$i], $destPath)) {
                        try {
                            // Konversi HEIC ke JPEG jika diperlukan
                            $processedPath = $destPath;
                            $processedFileName = $fileName;
                            
                            if (in_array($fileExtension, ['heic', 'heif'])) {
                                // Coba konversi HEIC ke JPEG jika Imagick tersedia
                                if (extension_loaded('imagick')) {
                                    try {
                                        $jpegFileName = uniqid() . '_' . pathinfo($originalFileName, PATHINFO_FILENAME) . '.jpg';
                                        $jpegPath = $uploadDir . $jpegFileName;
                                        
                                        $imagick = new Imagick($destPath);
                                        $imagick->setImageFormat('jpeg');
                                        $imagick->setImageCompressionQuality(85);
                                        $imagick->writeImage($jpegPath);
                                        $imagick->clear();
                                        $imagick->destroy();
                                        
                                        // Hapus file HEIC asli dan gunakan JPEG
                                        unlink($destPath);
                                        $processedPath = $jpegPath;
                                        $processedFileName = $jpegFileName;
                                    } catch (Exception $e) {
                                        // Jika konversi gagal, tetap gunakan file asli
                                        error_log('HEIC conversion failed: ' . $e->getMessage());
                                        // File HEIC asli tetap digunakan
                                    }
                                } else {
                                    // Imagick tidak tersedia, simpan file HEIC asli
                                    // File akan tetap bisa di-upload tapi tidak dikonversi
                                    error_log('HEIC file uploaded but not converted (Imagick not available): ' . $originalFileName);
                                }
                            }
                            
                            // Schema foto: file, judul
                            $webPath = '/images/' . $processedFileName;
                            if ($isAcara) {
                                // Tandai foto sebagai konten 'acara' agar halaman publik Acara Sekolah menampilkannya
                                if ($hasCreatedBy && $petugasId > 0) {
                                    $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file_path, caption, related_type, created_by, created_at, updated_at) VALUES (?, ?, ?, "acara", ?, NOW(), NOW())');
                                    $st->bind_param('issi', $galery_id, $webPath, $judulFoto, $petugasId);
                                } else {
                                    $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file_path, caption, related_type, created_at, updated_at) VALUES (?, ?, ?, "acara", NOW(), NOW())');
                                    $st->bind_param('iss', $galery_id, $webPath, $judulFoto);
                                }
                            } else {
                                if ($hasCreatedBy && $petugasId > 0) {
                                    $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file_path, caption, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
                                    $st->bind_param('issi', $galery_id, $webPath, $judulFoto, $petugasId);
                                } else {
                                    $st = $mysqli->prepare('INSERT INTO photos (gallery_id, file_path, caption, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
                                    $st->bind_param('iss', $galery_id, $webPath, $judulFoto);
                                }
                            }
                            $st->execute();
                            $st->close();
                            $uploadedCount++;
                        } catch (Exception $e) {
                            $errors[] = 'Error menyimpan foto: ' . $e->getMessage();
                        }
                    } else {
                        $errors[] = 'Gagal upload: ' . $_FILES['foto']['name'][$i];
                    }
                }
            }
            
            if ($uploadedCount > 0) {
                $message = "Berhasil upload $uploadedCount foto!";
                $messageType = 'success';
            }
            if (!empty($errors)) {
                $message .= ' ' . implode(', ', $errors);
                $messageType = 'error';
            }
        }
        header('Location: ?page=galeri&view=' . $galery_id);
        exit;
        
    } elseif ($action === 'delete_photo') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                // Ambil path file untuk dihapus
                $st = $mysqli->prepare('SELECT file_path FROM photos WHERE id=?');
                $st->bind_param('i', $id);
                $st->execute();
                $result = $st->get_result();
                if ($row = $result->fetch_assoc()) {
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
                
                // Hapus dari database
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
        header('Location: ?page=galeri&view=' . ($_POST['galery_id'] ?? ''));
        exit;
    } elseif ($action === 'update_photo_caption') {
        $id = (int)($_POST['id'] ?? 0);
        $judulBaru = trim($_POST['caption'] ?? '');
        if ($id > 0) {
            try {
                $st = $mysqli->prepare('UPDATE photos SET caption=?, updated_at=NOW() WHERE id=?');
                $st->bind_param('si', $judulBaru, $id);
                $st->execute();
                $st->close();
                $message = 'Caption foto diperbarui!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        header('Location: ?page=galeri&view=' . ($_POST['galery_id'] ?? ''));
        exit;
    }
}

// Fetch galery (exclude berita category)
$galery = [];
$res = $mysqli->query("SELECT id, title as judul, caption as deskripsi, status, category, created_at, updated_at FROM galleries WHERE category NOT IN ('berita', 'Berita Sekolah') ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $galery[] = $row;
    }
    $res->close();
}

// Get gallery for editing
$currentGallery = ['id' => '', 'judul' => '', 'deskripsi' => '', 'status' => 'draft', 'category' => 'Kegiatan Sekolah'];
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $res = $mysqli->query("SELECT id, title as judul, caption as deskripsi, status, category FROM galleries WHERE id = $editId LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $currentGallery = $row;
    }
    if ($res) { $res->close(); }
}

// Get foto for specific gallery
$foto = [];
$viewGallery = null;
if (isset($_GET['view'])) {
    $viewId = (int) $_GET['view'];
    // Get gallery details
    $res = $mysqli->query("SELECT id, title as judul, caption, status FROM galleries WHERE id = $viewId LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $viewGallery = $row;
    }
    if ($res) { $res->close(); }

    // Get all photos in the gallery
    $res = $mysqli->query("SELECT id, file_path, caption as judul, created_at FROM photos WHERE gallery_id = $viewId ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $foto[] = $row;
        }
        $res->close();
    }
}
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <h3>Album Galeri</h3>
            <p class="muted" style="margin-top:4px; font-size:13px;">Kelola album foto untuk mengelompokkan foto berdasarkan kategori/tema</p>
        </div>
        <a href="#" class="btn" onclick="document.getElementById('gallery-create').style.display='block'">Tambah Album</a>
    </div>

    <?php if ($message): ?>
        <div style="padding: 12px; margin-bottom: 16px; border-radius: 8px; <?php echo $messageType === 'success' ? 'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;' : 'background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;'; ?>">
            <?php echo e($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div style="padding: 12px; margin-bottom: 16px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 8px;">
            <?php echo e($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <?php if ($viewGallery): ?>
        <!-- View Photos -->
        <div style="margin-bottom: 12px;">
            <a href="?page=galeri" class="btn" style="background: #6b7280; padding:5px 11px; font-size:11px;">← Kembali ke Daftar Album</a>
            <h4 style="margin:10px 0 4px; font-size:16px;">Album: <?php echo e($viewGallery['judul'] ?? ''); ?></h4>
            <p class="muted" style="margin-top:2px; font-size:12px;"><?php echo e($viewGallery['caption'] ?? 'Tidak ada deskripsi'); ?></p>
            <p style="font-size:12px;"><strong>Status:</strong> <span style="display:inline-block; padding:2px 8px; border-radius:12px; font-size:11px; <?php echo ($viewGallery['status'] ?? '') === 'published' ? 'background:#dcfce7; color:#166534;' : 'background:#fef3c7; color:#92400e;'; ?>"><?php echo e($viewGallery['status'] ?? '-'); ?></span></p>
        </div>

        <!-- Upload Photos Form -->
        <div class="card" style="margin-bottom: 12px; padding:14px;">
            <h4 style="margin:0 0 6px; font-size:15px;">Tambah Foto</h4>
            <div style="background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 6px; padding: 10px; margin-bottom: 12px;">
                <p style="margin: 0; font-size: 12px; color: #0369a1;">
                    <strong>💡 Info:</strong> Sekarang mendukung format foto modern seperti HEIC (iPhone), WebP, AVIF, dan format lainnya selain JPG/PNG tradisional.
                </p>
            </div>
            <form method="post" action="?page=galeri" enctype="multipart/form-data" onsubmit="return validatePhotoForm()">
                <input type="hidden" name="action" value="upload_foto">
                <input type="hidden" name="galery_id" value="<?php echo e($viewGallery['id']); ?>">
                
                <div style="margin-bottom: 10px;">
                    <label style="display:block; font-weight:600; font-size:12px; margin-bottom:4px;">Pilih Foto (Multiple):</label>
                    <input type="file" id="foto" name="foto[]" multiple accept="image/*,.heic,.heif,.webp,.avif" required style="width: 100%; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; font-size:12px;">
                    <p class="muted" style="margin-top:4px; font-size:11px;">Format yang didukung: JPG, PNG, GIF, WebP, HEIC, HEIF, AVIF, BMP, TIFF</p>
                </div>
                
                <div id="photo-titles" style="margin-bottom: 10px;">
                    <!-- Dynamic photo title inputs will be added here -->
                </div>
                
                <button class="btn" type="submit" style="padding:5px 10px; font-size:11px;">Upload Foto</button>
            </form>
        </div>

        <!-- Photos List -->
        <div class="card" style="padding:14px;">
            <h4 style="margin:0 0 8px; font-size:15px;">Daftar Foto (<?php echo count($foto); ?> foto)</h4>
            <?php if (empty($foto)): ?>
                <p class="muted" style="font-size:12px;">Belum ada foto di galeri ini.</p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; margin-top: 12px;">
                    <?php foreach ($foto as $photo): ?>
                        <div class="photo-card" data-photo-id="<?php echo e((string)$photo['id']); ?>" style="background: linear-gradient(135deg, #a855f7 0%, #7c3aed 50%, #6366f1 100%); border-radius: 12px; padding: 0; overflow: hidden; box-shadow: 0 8px 18px rgba(168, 85, 247, 0.28); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px rgba(168, 85, 247, 0.35)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 18px rgba(168, 85, 247, 0.28)';">
                            <!-- Image Container with highlight overlay -->
                            <div class="img-box" style="position: relative; width: 100%; padding-top: 100%; overflow: hidden;">
                                <img src="<?php echo e('/' . ltrim($photo['file_path'], '/')); ?>" alt="<?php echo e($photo['judul']); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; display:block;">
                                <div class="hl-overlay" style="position:absolute; inset:0; background:rgba(34,197,94,0.35); outline:3px solid #22c55e; box-shadow: 0 0 0 4px rgba(34,197,94,0.5) inset; opacity:0;"></div>
                            </div>
                            
                            <!-- Instagram-style Footer -->
                            <div style="padding: 12px; background: rgba(255, 255, 255, 0.98);">
                                <!-- Like and Actions Icons -->
                                <div style="display: flex; gap: 12px; margin-bottom: 10px; align-items: center;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="cursor: pointer; color: #ef4444;">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="cursor: pointer;">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="cursor: pointer;">
                                        <circle cx="18" cy="5" r="3"></circle>
                                        <circle cx="6" cy="12" r="3"></circle>
                                        <circle cx="18" cy="19" r="3"></circle>
                                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                    </svg>
                                </div>
                                
                                <!-- Caption -->
                                <div style="margin-bottom: 10px;">
                                    <p style="margin: 0; font-weight: 600; color: #1f2937; font-size: 13px; line-height: 1.45;">
                                        <span style="font-weight: 700;"><?php echo e($viewGallery['judul'] ?? 'Gallery'); ?></span>
                                        <?php if ($photo['judul']): ?>
                                            <?php echo e($photo['judul']); ?>
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">Tanpa caption</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                
                                <!-- Date -->
                                <p style="margin: 0 0 12px 0; font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?php echo e($photo['created_at'] ?? '-'); ?>
                                </p>
                                
                                <!-- Edit Caption Form -->
                                <form method="post" action="?page=galeri" style="margin-bottom:10px;">
                                    <input type="hidden" name="action" value="update_photo_caption">
                                    <input type="hidden" name="id" value="<?php echo e($photo['id']); ?>">
                                    <input type="hidden" name="galery_id" value="<?php echo e($viewGallery['id']); ?>">
                                    <div style="display: flex; gap: 6px;">
                                        <input type="text" name="caption" value="<?php echo e($photo['judul']); ?>" placeholder="Ubah caption" style="flex: 1; padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size:12px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#a855f7';" onblur="this.style.borderColor='#e5e7eb';">
                                        <button class="btn" type="submit" style="padding: 6px 12px; font-size:11px; background: #a855f7; border: none; border-radius: 6px; color: white; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#9333ea';" onmouseout="this.style.background='#a855f7';">Simpan</button>
                                    </div>
                                </form>
                                
                                <!-- Delete Button -->
                                <form method="post" action="?page=galeri" onsubmit="return confirm('Hapus foto ini?')">
                                    <input type="hidden" name="action" value="delete_photo">
                                    <input type="hidden" name="id" value="<?php echo e($photo['id']); ?>">
                                    <input type="hidden" name="galery_id" value="<?php echo e($viewGallery['id']); ?>">
                                    <button class="btn" style="width: 100%; padding: 6px; background: #ef4444; border: none; border-radius: 6px; color: white; font-size:12px; font-weight: 600; cursor: pointer; transition: background 0.2s;" type="submit" onmouseover="this.style.background='#dc2626';" onmouseout="this.style.background='#ef4444';">Hapus Foto</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Galleries List -->
        <table>
        <thead>
            <tr style="background:#1d4ed8;color:#fff;">
                <th>ID</th>
                <th>Nama Album</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
            <tbody>
                <?php foreach ($galery as $gallery): ?>
                <tr>
                    <td><?php echo e((string)$gallery['id']); ?></td>
                    <td><?php echo e($gallery['judul']); ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; background: #e0e7ff; color: #3730a3;">
                            <?php echo e($gallery['category'] ?? 'Kegiatan Sekolah'); ?>
                        </span>
                    </td>
                    <td class="muted"><?php $desc = $gallery['deskripsi'] ?? ''; echo e(substr($desc, 0, 50) . (strlen($desc) > 50 ? '...' : '')); ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; <?php echo $gallery['status'] === 'published' ? 'background: #d1fae5; color: #065f46;' : 'background: #fef3c7; color: #92400e;'; ?>">
                            <?php echo e($gallery['status']); ?>
                        </span>
                    </td>
                    <td class="muted"><?php echo e($gallery['created_at'] ?? '-'); ?></td>
                    <td>
                        <div class="actions">
                            <a href="?page=galeri&view=<?php echo e($gallery['id']); ?>" class="btn" style="font-size: 12px;">Lihat Foto</a>
                            <a href="?page=galeri&edit=<?php echo e($gallery['id']); ?>" class="btn" style="font-size: 12px;">Edit</a>
                            <form method="post" action="?page=galeri" onsubmit="return confirm('Hapus galeri dan semua fotonya?')">
                                <input type="hidden" name="action" value="delete_gallery">
                                <input type="hidden" name="id" value="<?php echo e($gallery['id']); ?>">
                                <button class="btn" style="background: #ef4444; font-size: 12px;" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Create/Edit Gallery Form -->
<div id="gallery-create" class="card" style="margin-top:10px; <?php echo isset($_GET['edit']) ? 'display:block;' : 'display:none'; ?>">
    <h3 style="font-size:16px; margin:0 0 8px;"><?php echo isset($_GET['edit']) ? 'Edit Album' : 'Tambah Album Baru'; ?></h3>
    <p class="muted" style="margin-bottom:12px; font-size:12px;">Album digunakan untuk mengelompokkan foto berdasarkan tema/kategori (misal: Basket, PMR, Acara Sekolah, dll)</p>
    <div style="background: #f0fdf4; border: 1px solid #22c55e; border-radius: 6px; padding: 10px; margin-bottom: 12px;">
        <p style="margin: 0; font-size: 12px; color: #15803d;">
            <strong>📱 Format Foto Didukung:</strong> JPG, PNG, GIF, WebP, HEIC (iPhone), HEIF, AVIF, BMP, TIFF
        </p>
    </div>
    <form method="post" action="?page=galeri" onsubmit="return validateGalleryForm()">
        <input type="hidden" name="action" value="<?php echo isset($_GET['edit']) ? 'update_gallery' : 'create_gallery'; ?>">
        <?php if (isset($_GET['edit'])): ?>
            <input type="hidden" name="id" value="<?php echo e($currentGallery['id']); ?>">
        <?php endif; ?>
        
        <div style="margin-bottom: 10px;">
            <label for="judul" style="display:block; margin-bottom:4px; font-weight:600; font-size:12px;">Nama Album *</label>
            <input type="text" id="judul" name="judul" required placeholder="Contoh: Basket, PMR, Acara Sekolah" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 5px; font-size:13px;" value="<?php echo e($currentGallery['judul'] ?? ''); ?>">
            <p class="muted" style="margin-top:4px; font-size:11px;">Nama album akan muncul di halaman galeri publik</p>
        </div>
        
        <div style="margin-bottom: 10px;">
            <label for="deskripsi" style="display:block; margin-bottom:4px; font-weight:600; font-size:12px;">Deskripsi Album</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan tentang album ini (opsional)" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 5px; font-size:13px;"><?php echo e($currentGallery['deskripsi'] ?? ''); ?></textarea>
            <p class="muted" style="margin-top:4px; font-size:11px;">Deskripsi akan ditampilkan di halaman detail album</p>
        </div>
        
        <div style="margin-bottom: 10px;">
            <label for="category" style="display:block; margin-bottom:4px; font-weight:600; font-size:12px;">Kategori Album</label>
            <select id="category" name="category" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 5px; font-size:13px;">
                <?php
                // Fetch categories from database
                $categories = [];
                $catRes = $mysqli->query('SELECT id, judul FROM kategori_new ORDER BY judul ASC');
                if ($catRes) {
                    while ($cat = $catRes->fetch_assoc()) {
                        $categories[] = $cat;
                    }
                    $catRes->close();
                }
                
                // Get current category
                $currentCat = trim($currentGallery['category'] ?? '');
                
                // Display categories from database
                foreach ($categories as $cat) {
                    $catName = $cat['judul'];
                    
                    // Simple exact match (case-insensitive)
                    $selected = (strcasecmp($currentCat, $catName) === 0) ? 'selected' : '';
                    
                    echo '<option value="' . e($catName) . '" ' . $selected . '>' . e($catName) . '</option>';
                }
                ?>
            </select>
            <p class="muted" style="margin-top:4px; font-size:11px;">Kategori dikelola di menu <a href="?page=kategori" style="color:#2563eb; text-decoration:underline;">Kategori</a></p>
        </div>
        
        <div style="margin-bottom: 10px;">
            <label for="status" style="display:block; margin-bottom:4px; font-weight:600; font-size:12px;">Status Publikasi</label>
            <select id="status" name="status" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 5px; font-size:13px;">
                <option value="draft" <?php echo ($currentGallery['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft (Tidak ditampilkan)</option>
                <option value="published" <?php echo ($currentGallery['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published (Ditampilkan di publik)</option>
            </select>
            <p class="muted" style="margin-top:4px; font-size:11px;">Hanya album dengan status "Published" yang akan muncul di halaman publik</p>
        </div>
        
        <button class="btn" type="submit" id="saveBtn" style="padding:5px 11px; font-size:11px;"><?php echo isset($_GET['edit']) ? 'Perbarui' : 'Simpan'; ?></button>
        <button type="button" class="btn" onclick="resetGalleryForm()" style="background: #6b7280; margin-left: 6px; padding:5px 11px; font-size:11px;">Reset</button>
    </form>
    <div style="margin-top: 6px; font-size:12px;">
        <a href="#" onclick="document.getElementById('gallery-create').style.display='none'; window.location.href='?page=galeri'">Tutup</a>
    </div>
</div>

<style>
/* Focus highlight for deep-linked activity */
.focus-glow { background:#ecfdf5 !important; box-shadow: 0 0 0 3px #86efac inset, 0 10px 24px rgba(16,185,129,.15); transition: background .3s ease; }
/* Full-photo 3s overlay */
.img-box.highlight .hl-overlay{ animation: hlflash 3s ease-out forwards; }
@keyframes hlflash{ 0%{opacity:1} 80%{opacity:.2} 100%{opacity:0} }
</style>
<script>
// Highlight targeted gallery row or photo when navigated from dashboard
(function(){
  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('act_focus') === '1') {
      const needle = (params.get('act_title')||'').toLowerCase().trim();
      const photoId = params.get('act_photo');
      let target = null;
      // Highest priority: specific photo card
      if (photoId) {
        target = document.querySelector('.photo-card[data-photo-id="'+photoId+'"]');
        if (target) {
          const box = target.querySelector('.img-box');
          if (box){ box.classList.add('highlight'); setTimeout(function(){ box.classList.remove('highlight'); }, 3000); }
          target.scrollIntoView({behavior:'smooth', block:'center'});
          return;
        }
      }
      // Try gallery list rows
      document.querySelectorAll('table tbody tr').forEach(function(row){
        if (target) return;
        const cells = row.querySelectorAll('td');
        const titleCell = cells && cells[1] ? (cells[1].textContent||'').toLowerCase() : '';
        if (needle && titleCell.includes(needle)) target = row;
      });
      // If in photo view, try match photo caption container
      if (!target) {
        // Already prioritized specific photo above. No-op here.
      }
      target = target || document.querySelector('table tbody tr');
      if (target) {
        target.classList.add('focus-glow');
        setTimeout(function(){ target.classList.remove('focus-glow'); }, 2500);
        target.scrollIntoView({behavior:'smooth', block:'center'});
      }
    }
  } catch(_){}
})();
</script>

<script>
function validateGalleryForm() {
    const judul = document.getElementById('judul').value.trim();
    const saveBtn = document.getElementById('saveBtn');

    if (judul === '') {
        alert('Judul galeri tidak boleh kosong!');
        return false;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = 'Menyimpan...';
    return true;
}

function validatePhotoForm() {
    const foto = document.getElementById('foto');
    if (!foto.files || foto.files.length === 0) {
        alert('Pilih minimal satu foto!');
        return false;
    }
    
    // Validate file types
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/heic', 'image/heif', 'image/avif', 'image/bmp', 'image/tiff'];
    const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.heic', '.heif', '.avif', '.bmp', '.tiff', '.tif'];
    
    for (let i = 0; i < foto.files.length; i++) {
        const file = foto.files[i];
        const fileName = file.name.toLowerCase();
        const fileType = file.type.toLowerCase();
        
        // Check by file extension first (more reliable for HEIC)
        const isValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
        
        // For HEIC/HEIF, browser might not set correct MIME type, so extension check is enough
        if (fileName.endsWith('.heic') || fileName.endsWith('.heif')) {
            continue; // HEIC/HEIF is valid, skip MIME type check
        }
        
        // For other formats, check MIME type or extension
        const isValidType = allowedTypes.includes(fileType);
        
        if (!isValidType && !isValidExtension) {
            alert(`File "${file.name}" tidak didukung. Format yang didukung: JPG, PNG, GIF, WebP, HEIC, HEIF, AVIF, BMP, TIFF`);
            return false;
        }
    }
    
    return true;
}

function resetGalleryForm() {
    if (confirm('Apakah Anda yakin ingin mereset form?')) {
        document.getElementById('judul').value = '';
        document.getElementById('deskripsi').value = '';
        document.getElementById('status').value = 'draft';
    }
}

// Dynamic photo title inputs
document.getElementById('foto').addEventListener('change', function() {
    const container = document.getElementById('photo-titles');
    container.innerHTML = '';
    
    for (let i = 0; i < this.files.length; i++) {
        const div = document.createElement('div');
        div.style.marginBottom = '6px';
        div.innerHTML = `
            <label style="display:block; font-weight:600; font-size:12px; margin-bottom:3px;">Judul Foto ${i + 1} (${this.files[i].name}):</label>
            <input type="text" name="photo_judul[]" placeholder="Masukkan judul foto" style="width: 100%; padding: 5px; border: 1px solid #d1d5db; border-radius: 4px; font-size:12px;">
        `;
        container.appendChild(div);
    }
});
</script>
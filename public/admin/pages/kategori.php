<?php
// Kategori CRUD: kategori_new (id, judul) — menyesuaikan model Category.php (judul)

$error = '';
$success = '';

if (isPost()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $judul = trim($_POST['judul'] ?? '');
        
        // Validasi: judul tidak boleh kosong
        if ($judul === '') {
            $error = 'Nama kategori tidak boleh kosong!';
        } else {
            // Validasi: judul tidak boleh duplikat
            $checkSt = $mysqli->prepare('SELECT id FROM kategori_new WHERE judul = ? LIMIT 1');
            $checkSt->bind_param('s', $judul);
            $checkSt->execute();
            $checkResult = $checkSt->get_result();
            $exists = $checkResult->fetch_assoc();
            $checkSt->close();
            
            if ($exists) {
                $error = 'Kategori "' . htmlspecialchars($judul) . '" sudah ada!';
            } else {
                $st = $mysqli->prepare('INSERT INTO kategori_new (judul, created_at, updated_at) VALUES (?, NOW(), NOW())');
                $st->bind_param('s', $judul);
                $st->execute();
                $st->close();
                $success = 'Kategori berhasil ditambahkan!';
                header('Location: ?page=kategori&success=create'); exit;
            }
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        
        // Validasi: judul tidak boleh kosong
        if ($judul === '') {
            $error = 'Nama kategori tidak boleh kosong!';
        } elseif ($id > 0) {
            // Validasi: judul tidak boleh duplikat (kecuali untuk kategori yang sama)
            $checkSt = $mysqli->prepare('SELECT id FROM kategori_new WHERE judul = ? AND id != ? LIMIT 1');
            $checkSt->bind_param('si', $judul, $id);
            $checkSt->execute();
            $checkResult = $checkSt->get_result();
            $exists = $checkResult->fetch_assoc();
            $checkSt->close();
            
            if ($exists) {
                $error = 'Kategori "' . htmlspecialchars($judul) . '" sudah ada!';
            } else {
                $st = $mysqli->prepare('UPDATE kategori_new SET judul=?, updated_at=NOW() WHERE id=?');
                $st->bind_param('si', $judul, $id);
                $st->execute();
                $st->close();
                $success = 'Kategori berhasil diperbarui!';
                header('Location: ?page=kategori&success=update'); exit;
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Cek apakah kategori digunakan oleh post
            $checkSt = $mysqli->prepare('SELECT COUNT(*) as count FROM posts_new WHERE kategori_id = ?');
            $checkSt->bind_param('i', $id);
            $checkSt->execute();
            $checkResult = $checkSt->get_result();
            $countData = $checkResult->fetch_assoc();
            $checkSt->close();
            
            if ($countData['count'] > 0) {
                $error = 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $countData['count'] . ' berita!';
            } else {
                $st = $mysqli->prepare('DELETE FROM kategori_new WHERE id=?');
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();
                $success = 'Kategori berhasil dihapus!';
                header('Location: ?page=kategori&success=delete'); exit;
            }
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success'])) {
    $successType = $_GET['success'];
    if ($successType === 'create') {
        $success = 'Kategori berhasil ditambahkan!';
    } elseif ($successType === 'update') {
        $success = 'Kategori berhasil diperbarui!';
    } elseif ($successType === 'delete') {
        $success = 'Kategori berhasil dihapus!';
    }
}

// Fetch list
$rows = [];
$res = $mysqli->query('SELECT id, judul, COALESCE(created_at, "Tidak ada") as created_at FROM kategori_new ORDER BY id DESC');
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->close(); }
?>

<!-- Success Alert -->
<?php if ($success): ?>
<div class="card" style="background:#d1fae5; border-left:4px solid #10b981; margin-bottom:16px;">
    <p style="color:#065f46; margin:0; font-weight:500;">✓ <?php echo e($success); ?></p>
</div>
<?php endif; ?>

<!-- Error Alert -->
<?php if ($error): ?>
<div class="card" style="background:#fee2e2; border-left:4px solid #ef4444; margin-bottom:16px;">
    <p style="color:#991b1b; margin:0; font-weight:500;">✗ <?php echo e($error); ?></p>
</div>
<?php endif; ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h3>Kategori</h3>
        <!-- Tombol Tambah Kategori -->
        <a href="?page=kategori&create=1" class="btn" style="background:#10b981; padding:8px 16px; font-size:14px;">
            Tambah Kategori
        </a>
    </div>
    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <thead>
            <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">ID</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Nama</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Dibuat</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr style="border-bottom:1px solid #e5e7eb; transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                <td style="padding:12px; color:#6b7280; font-weight:500;"><?php echo e((string)$r['id']); ?></td>
                <td style="padding:12px; font-weight:500; color:#111827;"><?php echo e($r['judul']); ?></td>
                <td style="padding:12px; color:#6b7280; font-size:14px;"><?php echo e($r['created_at'] ?? '-'); ?></td>
                <td style="padding:12px;">
                    <div style="display:flex; gap:8px;">
                        <a href="?page=kategori&edit=<?php echo e((string)$r['id']); ?>" class="btn" style="padding:6px 12px; font-size:12px;">Edit</a>
                        <form method="post" action="?page=kategori" style="display:inline" onsubmit="return confirm('Hapus halaman ini?')">
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

<!-- Form Tambah Kategori -->
<?php if (isset($_GET['create'])): ?>
<div class="card" style="margin-top:12px;">
    <h3>Tambah Kategori Baru</h3>
    <form method="post" action="?page=kategori">
        <input type="hidden" name="action" value="create">
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Nama Kategori *</label>
            <input type="text" name="judul" required maxlength="100" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px; transition:border-color 0.2s;" placeholder="Masukkan nama kategori">
            <small style="color:#6b7280; font-size:12px;">Nama kategori tidak boleh duplikat</small>
        </div>
        <div style="display:flex; gap:8px;">
            <button class="btn" type="submit" style="background:#10b981;">Simpan</button>
            <a href="?page=kategori" class="btn" style="background:#6b7280;">Batal</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php if (isset($_GET['edit'])): 
    $editId = (int) $_GET['edit'];
    $detail = null;
    if ($editId > 0) {
        $st = $mysqli->prepare('SELECT id, judul FROM kategori_new WHERE id=? LIMIT 1');
        $st->bind_param('i', $editId);
        $st->execute();
        $detail = $st->get_result()->fetch_assoc();
        $st->close();
    }
?>
<div class="card" style="margin-top:12px;">
    <h3>Edit Kategori</h3>
    <?php if ($detail): ?>
    <form method="post" action="?page=kategori">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo e((string)$detail['id']); ?>">
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Nama Kategori *</label>
            <input type="text" name="judul" required maxlength="100" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px; transition:border-color 0.2s;" value="<?php echo e($detail['judul']); ?>">
            <small style="color:#6b7280; font-size:12px;">Nama kategori tidak boleh duplikat</small>
        </div>
        <div style="display:flex; gap:8px;">
            <button class="btn" type="submit">Update</button>
            <a href="?page=kategori" class="btn" style="background:#6b7280;">Batal</a>
        </div>
    </form>
    <?php else: ?>
        <p>Data tidak ditemukan.</p>
    <?php endif; ?>
</div>
<?php endif; ?>
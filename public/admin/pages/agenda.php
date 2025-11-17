<?php
// CRUD Agenda: tabel agenda (id, judul, deskripsi, tanggal, lokasi, status)

$success = '';
$error = '';

if (isPost()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $status = trim($_POST['status'] ?? 'Akan Datang');
        
        if ($judul !== '' && $tanggal !== '') {
            $st = $mysqli->prepare('INSERT INTO agenda (judul, deskripsi, tanggal, lokasi, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $st->bind_param('sssss', $judul, $deskripsi, $tanggal, $lokasi, $status);
            $st->execute();
            $st->close();
            $success = 'Agenda berhasil ditambahkan!';
        } else {
            $error = 'Judul dan tanggal wajib diisi!';
        }
        if (!$error) {
            header('Location: ?page=agenda&success=create'); exit;
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $lokasi = trim($_POST['lokasi'] ?? '');
        $status = trim($_POST['status'] ?? 'Akan Datang');
        
        if ($id > 0 && $judul !== '' && $tanggal !== '') {
            $st = $mysqli->prepare('UPDATE agenda SET judul=?, deskripsi=?, tanggal=?, lokasi=?, status=?, updated_at=NOW() WHERE id=?');
            $st->bind_param('sssssi', $judul, $deskripsi, $tanggal, $lokasi, $status, $id);
            $st->execute();
            $st->close();
            $success = 'Agenda berhasil diperbarui!';
        } else {
            $error = 'Judul dan tanggal wajib diisi!';
        }
        if (!$error) {
            header('Location: ?page=agenda&success=update'); exit;
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $st = $mysqli->prepare('DELETE FROM agenda WHERE id=?');
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
        header('Location: ?page=agenda&success=delete'); exit;
    }
}

// Check for success message from redirect
if (isset($_GET['success'])) {
    $successType = $_GET['success'];
    if ($successType === 'create') {
        $success = 'Agenda berhasil ditambahkan!';
    } elseif ($successType === 'update') {
        $success = 'Agenda berhasil diperbarui!';
    } elseif ($successType === 'delete') {
        $success = 'Agenda berhasil dihapus!';
    }
}

// Fetch list
$rows = [];
$res = $mysqli->query('SELECT id, judul, tanggal, lokasi, status, created_at FROM agenda ORDER BY tanggal DESC, id DESC');
if ($res) {
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $res->close();
}
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
        <h3>Agenda Sekolah</h3>
        <a href="#" class="btn" onclick="document.getElementById('agenda-create').style.display='block'; return false;">Tambah Agenda</a>
    </div>
    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <thead>
            <tr style="background:#f8fafc; border-bottom:2px solid #e5e7eb;">
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Judul</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Tanggal</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Lokasi</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Status</th>
                <th style="padding:12px; text-align:left; font-weight:600; color:#374151;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
            <tr>
                <td colspan="5" style="padding:24px; text-align:center; color:#6b7280;">Belum ada agenda</td>
            </tr>
            <?php else: ?>
            <?php foreach ($rows as $r): 
                // Status badge color
                $statusColor = '#6b7280';
                if ($r['status'] === 'Selesai') $statusColor = '#10b981';
                elseif ($r['status'] === 'Berlangsung') $statusColor = '#3b82f6';
                elseif ($r['status'] === 'Akan Datang') $statusColor = '#f59e0b';
            ?>
            <tr style="border-bottom:1px solid #e5e7eb; transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                <td style="padding:12px; font-weight:500; color:#111827;"><?php echo e($r['judul']); ?></td>
                <td style="padding:12px; color:#6b7280; font-size:14px;"><?php echo e(date('d M Y', strtotime($r['tanggal']))); ?></td>
                <td style="padding:12px; color:#6b7280; font-size:14px;"><?php echo e($r['lokasi'] ?: '-'); ?></td>
                <td style="padding:12px;">
                    <span style="background:<?php echo $statusColor; ?>; color:#fff; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:500;">
                        <?php echo e($r['status']); ?>
                    </span>
                </td>
                <td style="padding:12px;">
                    <div style="display:flex; gap:8px;">
                        <a href="?page=agenda&edit=<?php echo e((string)$r['id']); ?>" class="btn" style="padding:6px 12px; font-size:12px;">Edit</a>
                        <form method="post" action="?page=agenda" style="display:inline" onsubmit="return confirm('Hapus agenda ini?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
                            <button class="btn" style="background:#ef4444; padding:6px 12px; font-size:12px;" type="submit">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Create -->
<div id="agenda-create" class="card" style="margin-top:12px; display:none;">
    <h3>Tambah Agenda Baru</h3>
    <form method="post" action="?page=agenda">
        <input type="hidden" name="action" value="create">
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Judul Agenda *</label>
            <input type="text" name="judul" required placeholder="Contoh: Rapat Guru Semester 1" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Deskripsi</label>
            <textarea name="deskripsi" rows="6" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;" placeholder="Masukkan detail agenda, tujuan, dan informasi lainnya..."></textarea>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Tanggal *</label>
                <input type="date" name="tanggal" required style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Lokasi</label>
                <input type="text" name="lokasi" placeholder="Contoh: Ruang Guru" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;">
            </div>
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Status</label>
            <select name="status" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;">
                <option value="Akan Datang">Akan Datang</option>
                <option value="Berlangsung">Berlangsung</option>
                <option value="Selesai">Selesai</option>
            </select>
        </div>
        <div style="display:flex; gap:8px;">
            <button class="btn" type="submit">Simpan</button>
            <a href="#" class="btn" style="background:#6b7280;" onclick="document.getElementById('agenda-create').style.display='none'; return false;">Batal</a>
        </div>
    </form>
</div>

<?php if (isset($_GET['edit'])):
    $editId = (int) $_GET['edit'];
    $detail = null;
    if ($editId > 0) {
        $st = $mysqli->prepare('SELECT id, judul, deskripsi, tanggal, lokasi, status FROM agenda WHERE id=? LIMIT 1');
        $st->bind_param('i', $editId);
        $st->execute();
        $detail = $st->get_result()->fetch_assoc();
        $st->close();
    }
?>
<div class="card" style="margin-top:12px;">
    <h3>Edit Agenda</h3>
    <?php if ($detail): ?>
    <form method="post" action="?page=agenda">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?php echo e((string)$detail['id']); ?>">
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Judul Agenda *</label>
            <input type="text" name="judul" required style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;" value="<?php echo e($detail['judul']); ?>">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Deskripsi</label>
            <textarea name="deskripsi" rows="6" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;" placeholder="Masukkan detail agenda, tujuan, dan informasi lainnya..."><?php echo e($detail['deskripsi'] ?? ''); ?></textarea>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
            <div>
                <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Tanggal *</label>
                <input type="date" name="tanggal" required style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;" value="<?php echo e($detail['tanggal']); ?>">
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Lokasi</label>
                <input type="text" name="lokasi" placeholder="Contoh: Ruang Guru" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;" value="<?php echo e($detail['lokasi'] ?? ''); ?>">
            </div>
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; margin-bottom:6px; font-weight:600; color:#374151;">Status</label>
            <select name="status" style="width:100%; padding:12px; border:2px solid #d1d5db; border-radius:8px; font-size:14px;">
                <option value="Akan Datang" <?php echo ($detail['status'] === 'Akan Datang') ? 'selected' : ''; ?>>Akan Datang</option>
                <option value="Berlangsung" <?php echo ($detail['status'] === 'Berlangsung') ? 'selected' : ''; ?>>Berlangsung</option>
                <option value="Selesai" <?php echo ($detail['status'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
            </select>
        </div>
        <div style="display:flex; gap:8px;">
            <button class="btn" type="submit">Update</button>
            <a href="?page=agenda" class="btn" style="background:#6b7280;">Kembali</a>
        </div>
    </form>
    <?php else: ?><p>Data tidak ditemukan.</p><?php endif; ?>
</div>
<?php endif; ?>


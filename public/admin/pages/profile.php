<?php
// Profile Management: profile (id, judul, isi)

$message = '';
$messageType = '';

// Handle POST actions
if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        
        if ($judul !== '' && $isi !== '') {
            try {
                $st = $mysqli->prepare('INSERT INTO profile (judul, isi, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
                $st->bind_param('ss', $judul, $isi);
                $st->execute();
                $st->close();
                $message = 'Profile berhasil ditambahkan!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message = 'Judul dan isi harus diisi!';
            $messageType = 'error';
        }
        header('Location: ?page=profile'); exit;

    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        
        if ($id > 0 && $judul !== '' && $isi !== '') {
            try {
                $st = $mysqli->prepare('UPDATE profile SET judul = ?, isi = ?, updated_at = NOW() WHERE id = ?');
                $st->bind_param('ssi', $judul, $isi, $id);
                $st->execute();
                $st->close();
                $message = 'Profile berhasil diperbarui!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message = 'Data tidak valid!';
            $messageType = 'error';
        }
        header('Location: ?page=profile'); exit;

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        
        if ($id > 0) {
            try {
                $st = $mysqli->prepare('DELETE FROM profile WHERE id = ?');
                $st->bind_param('i', $id);
                $st->execute();
                $st->close();
                $message = 'Profile berhasil dihapus!';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        header('Location: ?page=profile'); exit;
    }
}

// Get all profiles
$profiles = [];
$st = $mysqli->prepare('SELECT id, judul, isi, created_at, updated_at FROM profile ORDER BY id DESC');
$st->execute();
$res = $st->get_result();
while ($row = $res->fetch_assoc()) {
    $profiles[] = $row;
}
$st->close();

// No need to get single profile for editing since we use modal
?>

<div class="container-fluid profile-container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Kelola Profile</h3>
                    <button class="btn btn-primary" onclick="showAddForm()">
                        <i class="fas fa-plus"></i> Tambah Profile
                    </button>
                </div>
                
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                            <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Isi (Preview)</th>
                                    <th>Dibuat</th>
                                    <th>Diperbarui</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($profiles)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada data profile</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($profiles as $profile): ?>
                                    <tr>
                                        <td><?= $profile['id'] ?></td>
                                        <td><?= htmlspecialchars($profile['judul']) ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;">
                                                <?= strip_tags($profile['isi']) ?>
                                            </div>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($profile['created_at'])) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($profile['updated_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="showEditForm(<?= $profile['id'] ?>, '<?= htmlspecialchars($profile['judul'], ENT_QUOTES) ?>', '<?= htmlspecialchars($profile['isi'], ENT_QUOTES) ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus profile ini?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $profile['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Tambah/Edit Profile -->
<div class="card mt-4" id="profileFormCard" style="display: none;">
    <div class="card-header">
        <h5 class="card-title mb-0" id="formTitle">Tambah Profile Baru</h5>
    </div>
    <div class="card-body">
        <form method="POST" id="profileForm">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="formId">
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="judul" class="form-label fw-bold mb-2">Judul Profile</label>
                        <input type="text" class="form-control form-control-lg" id="judul" name="judul" 
                               placeholder="Masukkan judul profile..." required>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="isi" class="form-label fw-bold mb-2">Isi Profile</label>
                        <textarea id="isi" name="isi" class="form-control" rows="20" 
                                  placeholder="Masukkan isi profile..." style="resize: vertical;"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="button" class="btn btn-secondary px-4" onclick="hideForm()">
                            <i class="fas fa-times me-2"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Simpan Profile
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap CSS (scoped to avoid conflicts) -->
<style>
/* Scoped Bootstrap styles for profile page only */
.profile-container * {
    box-sizing: border-box;
}

.profile-container .container-fluid {
    padding: 0;
    margin: 0;
}

.profile-container .row {
    margin: 0;
}

.profile-container .col-12 {
    padding: 0;
}

.profile-container .card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.profile-container .card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
}

.profile-container .card-body {
    padding: 20px;
}

.profile-container .card-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.profile-container .btn {
    background: #2563eb;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.profile-container .btn:hover {
    background: #1d4ed8;
}

.profile-container .btn-primary {
    background: #2563eb;
}

.profile-container .btn-warning {
    background: #f59e0b;
}

.profile-container .btn-danger {
    background: #ef4444;
}

.profile-container .btn-secondary {
    background: #6b7280;
}

.profile-container .btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.profile-container .table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.profile-container .table th,
.profile-container .table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #f1f5f9;
}

.profile-container .table th {
    background: #f8fafc;
    font-weight: 600;
    color: #374151;
}

.profile-container .table-striped tbody tr:nth-of-type(odd) {
    background-color: #f9fafb;
}

.profile-container .table-responsive {
    overflow-x: auto;
}

.profile-container .alert {
    padding: 12px 16px;
    margin-bottom: 16px;
    border: 1px solid transparent;
    border-radius: 8px;
}

.profile-container .alert-success {
    background-color: #d1fae5;
    border-color: #a7f3d0;
    color: #065f46;
}

.profile-container .alert-danger {
    background-color: #fee2e2;
    border-color: #fecaca;
    color: #991b1b;
}

.profile-container .alert-dismissible .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 12px 16px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
}

.profile-container .modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1055;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
}

.profile-container .modal.show {
    display: block;
}

.profile-container .modal-dialog {
    position: relative;
    width: auto;
    margin: 0.5rem;
    pointer-events: none;
}

.profile-container .modal-xl {
    max-width: 1140px;
}

.profile-container .modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    outline: 0;
}

.profile-container .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    border-radius: 12px 12px 0 0;
}

.profile-container .modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.profile-container .modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 20px;
}

.profile-container .modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    border-radius: 0 0 12px 12px;
    gap: 8px;
}

.profile-container .btn-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
}

.profile-container .form-label {
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
}

.profile-container .form-control {
    display: block;
    width: 100%;
    padding: 8px 12px;
    font-size: 14px;
    line-height: 1.5;
    color: #111827;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.profile-container .form-control:focus {
    outline: 0;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.profile-container .mb-3 {
    margin-bottom: 16px;
}

.profile-container .text-center {
    text-align: center;
}

.profile-container .text-muted {
    color: #6b7280;
}

.profile-container .text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.profile-container .d-flex {
    display: flex;
}

.profile-container .justify-content-between {
    justify-content: space-between;
}

.profile-container .align-items-center {
    align-items: center;
}

.profile-container .fas {
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    margin-right: 6px;
}

/* Form styling improvements */
.profile-container .form-label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.profile-container .form-control-lg {
    padding: 12px 16px;
    font-size: 16px;
    border-radius: 8px;
    border: 2px solid #d1d5db;
    transition: all 0.2s ease;
}

.profile-container .form-control-lg:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

.profile-container textarea.form-control {
    padding: 12px 16px;
    font-size: 14px;
    border-radius: 8px;
    border: 2px solid #d1d5db;
    transition: all 0.2s ease;
    font-family: inherit;
    line-height: 1.5;
}

.profile-container textarea.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

.profile-container .btn {
    padding: 10px 20px;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.profile-container .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.profile-container .btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
}

.profile-container .btn-secondary {
    background: #6b7280;
    color: white;
}

.profile-container .btn-warning {
    background: #f59e0b;
    color: white;
}

.profile-container .btn-danger {
    background: #ef4444;
    color: white;
}

.profile-container .btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.profile-container .gap-2 {
    gap: 8px;
}

.profile-container .fw-bold {
    font-weight: 600;
}

.profile-container .card {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
}

.profile-container .card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 2px solid #e5e7eb;
}

.profile-container .card-title {
    color: #111827;
    font-weight: 600;
}

/* Form group styling */
.profile-container .form-group {
    margin-bottom: 0;
}

.profile-container .form-group .form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.profile-container .form-group .form-control {
    width: 100%;
    margin-bottom: 0;
}

/* Improved spacing */
.profile-container .mb-4 {
    margin-bottom: 1.5rem !important;
}

.profile-container .mt-4 {
    margin-top: 1.5rem !important;
}

.profile-container .gap-3 {
    gap: 1rem !important;
}

.profile-container .px-4 {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
}

.profile-container .me-2 {
    margin-right: 0.5rem !important;
}
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap JS (scoped) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to show add form
    window.showAddForm = function() {
        document.getElementById('formTitle').textContent = 'Tambah Profile Baru';
        document.getElementById('formAction').value = 'create';
        document.getElementById('formId').value = '';
        document.getElementById('judul').value = '';
        document.getElementById('isi').value = '';
        document.getElementById('profileFormCard').style.display = 'block';
        
        // Scroll to form
        document.getElementById('profileFormCard').scrollIntoView({ behavior: 'smooth' });
    };
    
    // Function to show edit form
    window.showEditForm = function(id, judul, isi) {
        document.getElementById('formTitle').textContent = 'Edit Profile';
        document.getElementById('formAction').value = 'update';
        document.getElementById('formId').value = id;
        document.getElementById('judul').value = judul;
        document.getElementById('isi').value = isi;
        document.getElementById('profileFormCard').style.display = 'block';
        
        // Scroll to form
        document.getElementById('profileFormCard').scrollIntoView({ behavior: 'smooth' });
    };
    
    // Function to hide form
    window.hideForm = function() {
        document.getElementById('profileFormCard').style.display = 'none';
        document.getElementById('judul').value = '';
        document.getElementById('isi').value = '';
    };
});
</script>
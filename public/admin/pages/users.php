<?php
// Users list from users table

// Handle delete action
if (isPost() && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        // Prevent deleting the current admin user
        if (!isset($_SESSION['petugas_id']) || $_SESSION['petugas_id'] != $id) {
            $st = $mysqli->prepare('DELETE FROM users WHERE id = ?');
            $st->bind_param('i', $id);
            $st->execute();
            $st->close();
        }
    }
    header('Location: ?page=users');
    exit;
}

// Fetch list
$rows = [];
$res = $mysqli->query('SELECT id, name, username, email, gender, phone, created_at FROM users ORDER BY id DESC');
if ($res) { 
    while ($r = $res->fetch_assoc()) { 
        $rows[] = $r; 
    } 
    $res->close(); 
}
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h3>Users</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Registered</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
                <td><?php echo e((string)$r['id']); ?></td>
                <td><?php echo e($r['name']); ?></td>
                <td><?php echo e($r['username'] ?? '-'); ?></td>
                <td><?php echo e($r['email']); ?></td>
                <td><?php echo e($r['gender'] ?? '-'); ?></td>
                <td><?php echo e($r['phone'] ?? '-'); ?></td>
                <td><?php echo e($r['created_at'] ?? '-'); ?></td>
                <td>
                    <form method="post" action="?page=users" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
                        <button class="btn" style="background:#ef4444; padding:5px 10px; font-size:11px;" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
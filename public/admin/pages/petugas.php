<?php
// Petugas CRUD (id, username, password)

if (isPost()) {
	$action = $_POST['action'] ?? '';
	if ($action === 'create') {
		$username = trim($_POST['username'] ?? '');
		$password = (string)($_POST['password'] ?? '');
		if ($username !== '' && $password !== '') {
			$hashed = password_hash($password, PASSWORD_BCRYPT);
			$st = $mysqli->prepare('INSERT INTO petugas (username, password) VALUES (?, ?)');
			$st->bind_param('ss', $username, $hashed);
			$st->execute();
			$st->close();
		}
		header('Location: ?page=petugas'); exit;
	} elseif ($action === 'update') {
		$id = (int)($_POST['id'] ?? 0);
		$username = trim($_POST['username'] ?? '');
		$newPassword = (string)($_POST['password'] ?? '');
		if ($id > 0 && $username !== '') {
			if ($newPassword !== '') {
				$hashed = password_hash($newPassword, PASSWORD_BCRYPT);
				$st = $mysqli->prepare('UPDATE petugas SET username=?, password=? WHERE id=?');
				$st->bind_param('ssi', $username, $hashed, $id);
				$st->execute();
				$st->close();
			} else {
				$st = $mysqli->prepare('UPDATE petugas SET username=? WHERE id=?');
				$st->bind_param('si', $username, $id);
				$st->execute();
				$st->close();
			}
		}
		header('Location: ?page=petugas'); exit;
	} elseif ($action === 'delete') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$st = $mysqli->prepare('DELETE FROM petugas WHERE id=?');
			$st->bind_param('i', $id);
			$st->execute();
			$st->close();
		}
		header('Location: ?page=petugas'); exit;
	}
}

// Fetch list
$rows = [];
$res = $mysqli->query('SELECT id, username, password FROM petugas ORDER BY id DESC');
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->close(); }
?>
<div class="card">
	<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
		<h3>Petugas</h3>
		<a href="#" class="btn" onclick="document.getElementById('petugas-create').style.display='block'">Tambah</a>
	</div>
	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Username</th>
				<th>Password Hash</th>
				<th>Aksi</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($rows as $r): ?>
			<tr>
				<td><?php echo e((string)$r['id']); ?></td>
				<td><?php echo e($r['username']); ?></td>
				<td class="muted" style="max-width:480px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($r['password']); ?>"><?php echo e($r['password']); ?></td>
				<td>
					<a href="?page=petugas&edit=<?php echo e((string)$r['id']); ?>" class="btn">Edit</a>
					<form method="post" action="?page=petugas" style="display:inline" onsubmit="return confirm('Hapus petugas ini?')">
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
<div id="petugas-create" class="card" style="margin-top:12px; display:none;">
	<h3>Tambah Petugas</h3>
	<form method="post" action="?page=petugas">
		<input type="hidden" name="action" value="create">
		<div style="margin-bottom:8px;">
			<label>Username</label>
			<input type="text" name="username" required style="width:100%; padding:8px;">
		</div>
		<div style="margin-bottom:8px;">
			<label>Password</label>
			<input type="password" name="password" required style="width:100%; padding:8px;">
		</div>
		<button class="btn" type="submit">Simpan</button>
	</form>
	<div style="margin-top:8px;"><a href="#" onclick="this.closest('#petugas-create').style.display='none'">Tutup</a></div>
</div>

<?php if (isset($_GET['edit'])): 
	$editId = (int) $_GET['edit'];
	$detail = null;
	if ($editId > 0) {
		$st = $mysqli->prepare('SELECT id, username FROM petugas WHERE id=? LIMIT 1');
		$st->bind_param('i', $editId);
		$st->execute();
		$detail = $st->get_result()->fetch_assoc();
		$st->close();
	}
?>
<div class="card" style="margin-top:12px;">
	<h3>Edit Petugas</h3>
	<?php if ($detail): ?>
	<form method="post" action="?page=petugas">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="id" value="<?php echo e((string)$detail['id']); ?>">
		<div style="margin-bottom:8px;">
			<label>Username</label>
			<input type="text" name="username" required style="width:100%; padding:8px;" value="<?php echo e($detail['username']); ?>">
		</div>
		<div style="margin-bottom:8px;">
			<label>Password (opsional - kosongkan jika tidak diubah)</label>
			<input type="password" name="password" style="width:100%; padding:8px;" placeholder="Biarkan kosong jika tidak diubah">
		</div>
		<button class="btn" type="submit">Update</button>
	</form>
	<?php else: ?>
		<p>Data tidak ditemukan.</p>
	<?php endif; ?>
</div>
<?php endif; ?>



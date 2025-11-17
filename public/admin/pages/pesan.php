<?php
// Pesan Masuk (messages): id, name, email, message, status, created_at, updated_at

// Optional: mark all unread as read when triggered from notifications
if (($_GET['mark_all'] ?? '') === '1') {
    if ($mysqli) {
        @$mysqli->query("UPDATE messages_new SET status='read', updated_at=NOW() WHERE status='unread'");
    }
    header('Location: ?page=pesan&act_focus=1');
    exit;
}

if (isPost()) {
	$action = $_POST['action'] ?? '';
	if ($action === 'mark_read') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$st = $mysqli->prepare('UPDATE messages_new SET status="read", updated_at=NOW() WHERE id=?');
			$st->bind_param('i', $id);
			$st->execute();
			$st->close();
		}
		header('Location: ?page=pesan'); exit;
	} elseif ($action === 'mark_unread') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$st = $mysqli->prepare('UPDATE messages_new SET status="unread", updated_at=NOW() WHERE id=?');
			$st->bind_param('i', $id);
			$st->execute();
			$st->close();
		}
		header('Location: ?page=pesan'); exit;
	} elseif ($action === 'approve') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$st = $mysqli->prepare('UPDATE messages_new SET approval_status="approved", updated_at=NOW() WHERE id=?');
			$st->bind_param('i', $id);
			if ($st->execute()) {
				// Success - message approved
			}
			$st->close();
		}
		header('Location: ?page=pesan'); exit;
	} elseif ($action === 'reject') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$st = $mysqli->prepare('UPDATE messages_new SET approval_status="rejected", updated_at=NOW() WHERE id=?');
			$st->bind_param('i', $id);
			if ($st->execute()) {
				// Success - message rejected
			}
			$st->close();
		}
		header('Location: ?page=pesan'); exit;
	} elseif ($action === 'delete') {
		$id = (int)($_POST['id'] ?? 0);
		if ($id > 0) {
			$st = $mysqli->prepare('DELETE FROM messages_new WHERE id=?');
			$st->bind_param('i', $id);
			$st->execute();
			$st->close();
		}
		header('Location: ?page=pesan'); exit;
	}
}

// Check if approval_status column exists
$columnExists = false;
if ($q = $mysqli->query("SHOW COLUMNS FROM messages_new LIKE 'approval_status'")) {
    $columnExists = $q->num_rows > 0;
    $q->close();
}

// Fetch list messages
$rows = [];
$selectColumns = $columnExists ? 
    'SELECT id, name, email, message, status, approval_status, rating, created_at FROM messages_new ORDER BY id DESC' :
    'SELECT id, name, email, message, status, rating, created_at FROM messages_new ORDER BY id DESC';
    
$res = $mysqli->query($selectColumns);
if ($res) { 
    while ($r = $res->fetch_assoc()) { 
        // Set default approval_status if column doesn't exist
        if (!$columnExists) {
            $r['approval_status'] = 'pending';
        }
        $rows[] = $r; 
    } 
    $res->close(); 
}
?>
<div class="card">
	<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
		<h3>Pesan Masuk</h3>
		<span class="muted">Total: <?php echo count($rows); ?> pesan</span>
	</div>
	<style>
	/* Focus highlight for deep-linked activity */
	.focus-glow { background:#ecfdf5 !important; box-shadow: 0 0 0 3px #86efac inset, 0 10px 24px rgba(16,185,129,.15); transition: background .3s ease; }
	</style>
	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Nama</th>
				<th>Email</th>
				<th>Pesan</th>
				<th>Penilaian</th>
				<th>Status</th>
				<th>Persetujuan</th>
				<th>Dibuat</th>
				<th>Aksi</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($rows as $r): ?>
			<tr class="msg-row" data-title="<?php echo e(($r['name'] ?? '').' - '.substr($r['message'] ?? '', 0, 60)); ?>">
				<td><?php echo e((string)$r['id']); ?></td>
				<td><?php echo e($r['name']); ?></td>
				<td><a href="mailto:<?php echo e($r['email']); ?>"><?php echo e($r['email']); ?></a></td>
				<td style="max-width:420px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo e($r['message']); ?>"><?php echo e($r['message']); ?></td>
				<td>
					<?php 
					$rating = $r['rating'] ?? null;
					if ($rating == 5) {
						echo '<span style="padding:4px 8px; border-radius:4px; font-size:12px; background:#fef3c7; color:#92400e;">⭐ Keren Sekali</span>';
					} elseif ($rating == 4) {
						echo '<span style="padding:4px 8px; border-radius:4px; font-size:12px; background:#dbeafe; color:#1e40af;">👍 Bagus</span>';
					} else {
						echo '<span style="padding:4px 8px; border-radius:4px; font-size:12px; background:#f3f4f6; color:#6b7280;">-</span>';
					}
					?>
				</td>
				<td>
					<span style="padding:4px 8px; border-radius:4px; font-size:12px; <?php echo ($r['status'] === 'read') ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;'; ?>">
						<?php echo e($r['status']); ?>
					</span>
				</td>
				<td>
					<?php 
					$approval_status = $r['approval_status'] ?? 'pending';
					$approval_colors = [
						'pending' => 'background:#fef3c7; color:#92400e;',
						'approved' => 'background:#d1fae5; color:#065f46;',
						'rejected' => 'background:#fee2e2; color:#991b1b;'
					];
					?>
					<span style="padding:4px 8px; border-radius:4px; font-size:12px; <?php echo $approval_colors[$approval_status]; ?>">
						<?php echo e(ucfirst($approval_status)); ?>
					</span>
				</td>
				<td class="muted"><?php echo e($r['created_at'] ?? '-'); ?></td>
				<td>
					<div style="display:flex; flex-wrap:wrap; gap:4px; align-items:center;">
						<?php if (($r['status'] ?? 'unread') === 'unread'): ?>
						<form method="post" action="?page=pesan" style="display:inline">
							<input type="hidden" name="action" value="mark_read">
							<input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
							<button class="btn" type="submit" style="font-size:11px; padding:4px 8px;">Baca</button>
						</form>
						<?php else: ?>
						<form method="post" action="?page=pesan" style="display:inline">
							<input type="hidden" name="action" value="mark_unread">
							<input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
							<button class="btn" type="submit" style="background:#6b7280; font-size:11px; padding:4px 8px;">Belum Baca</button>
						</form>
						<?php endif; ?>
						
						
						<form method="post" action="?page=pesan" style="display:inline" onsubmit="return confirm('Hapus pesan ini?')">
							<input type="hidden" name="action" value="delete">
							<input type="hidden" name="id" value="<?php echo e((string)$r['id']); ?>">
							<button class="btn" style="background:#ef4444; font-size:11px; padding:4px 8px;" type="submit">Hapus</button>
						</form>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<script>
// Highlight targeted message row when navigated from dashboard
(function(){
  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('act_focus') === '1') {
      const needle = (params.get('act_title')||'').toLowerCase().trim();
      let target = null;
      if (needle) {
        document.querySelectorAll('.msg-row').forEach(function(row){
          const t = (row.getAttribute('data-title')||'').toLowerCase();
          if (!target && t.includes(needle)) target = row;
        });
      }
      target = target || document.querySelector('.msg-row');
      if (target) {
        target.classList.add('focus-glow');
        setTimeout(function(){ target.classList.remove('focus-glow'); }, 2500);
        target.scrollIntoView({behavior:'smooth', block:'center'});
      }
    }
  } catch(_){}
})();
</script>



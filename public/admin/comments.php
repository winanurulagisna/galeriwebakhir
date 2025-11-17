<?php
// Simple admin page for managing comments
require_once '../../vendor/autoload.php';

$app = require_once '../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PhotoComment;
use App\Models\Photo;

// Handle actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    
    if ($action && $id) {
        $comment = PhotoComment::find($id);
        if ($comment) {
            switch ($action) {
                case 'approve':
                    $comment->update(['status' => 'approved']);
                    $message = "Komentar ID {$id} berhasil disetujui!";
                    break;
                case 'reject':
                    $comment->update(['status' => 'rejected']);
                    $message = "Komentar ID {$id} ditolak!";
                    break;
                case 'delete':
                    $comment->delete();
                    $message = "Komentar ID {$id} berhasil dihapus!";
                    break;
            }
        }
    }
}

// Get all comments
$comments = PhotoComment::with(['photo', 'user'])
    ->orderBy('created_at', 'desc')
    ->get();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Komentar - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007cba; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status.approved { background: #d4edda; color: #155724; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.rejected { background: #f8d7da; color: #721c24; }
        .actions { display: flex; gap: 5px; }
        .btn { padding: 4px 8px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-delete { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.8; }
        .comment-text { max-width: 300px; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Kelola Komentar Foto</h1>
        
        <?php if (isset($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <p><strong>Total Komentar:</strong> <?= $comments->count() ?></p>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>User</th>
                    <th>Komentar</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?= $comment->id ?></td>
                    <td>
                        <?php if ($comment->photo): ?>
                            Foto ID: <?= $comment->photo->id ?><br>
                            <small><?= htmlspecialchars($comment->photo->judul ?: 'Tanpa judul') ?></small>
                        <?php else: ?>
                            <em>Foto dihapus</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($comment->user): ?>
                            <?= htmlspecialchars($comment->user->name) ?><br>
                            <small><?= htmlspecialchars($comment->user->email) ?></small>
                        <?php else: ?>
                            <?= htmlspecialchars($comment->first_name . ' ' . $comment->last_name) ?><br>
                            <small><?= htmlspecialchars($comment->email) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="comment-text"><?= htmlspecialchars($comment->body) ?></td>
                    <td>
                        <span class="status <?= $comment->status ?>">
                            <?= ucfirst($comment->status) ?>
                        </span>
                    </td>
                    <td><?= $comment->created_at->format('d/m/Y H:i') ?></td>
                    <td>
                        <div class="actions">
                            <?php if ($comment->status !== 'approved'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="id" value="<?= $comment->id ?>">
                                <button type="submit" class="btn btn-approve">✓ Setujui</button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($comment->status !== 'rejected'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="id" value="<?= $comment->id ?>">
                                <button type="submit" class="btn btn-reject">✗ Tolak</button>
                            </form>
                            <?php endif; ?>
                            
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus komentar ini?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $comment->id ?>">
                                <button type="submit" class="btn btn-delete">🗑 Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 4px;">
            <h3>📋 Petunjuk:</h3>
            <ul>
                <li><strong>Setujui:</strong> Komentar akan muncul di halaman publik</li>
                <li><strong>Tolak:</strong> Komentar tidak akan muncul di halaman publik</li>
                <li><strong>Hapus:</strong> Komentar akan dihapus permanen dari database</li>
            </ul>
        </div>
        
        <p style="margin-top: 20px;">
            <a href="index.php" style="color: #007cba;">← Kembali ke Dashboard Admin</a>
        </p>
    </div>
</body>
</html>

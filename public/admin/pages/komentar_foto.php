<?php
// Komentar Foto & Berita - Moderasi dari database
require_once __DIR__ . '/../../db.php';

// AJAX actions
if (($_GET['ajax'] ?? '') === '1') {
    header('Content-Type: application/json');
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        // List - ambil dari database
        $stmt = $mysqli->prepare("
            SELECT pc.id, pc.photo_id, pc.user_id, pc.body, pc.status, pc.created_at,
                   u.name as user_name, u.email as user_email
            FROM photo_comments pc
            LEFT JOIN users u ON pc.user_id = u.id
            ORDER BY pc.created_at DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        
        while ($row = $result->fetch_assoc()) {
            // Cek apakah ini komentar foto atau berita
            $photoStmt = $mysqli->prepare("SELECT caption as judul, created_at FROM photos WHERE id = ?");
            $photoStmt->bind_param('i', $row['photo_id']);
            $photoStmt->execute();
            $photoResult = $photoStmt->get_result();
            $photo = $photoResult->fetch_assoc();
            $photoStmt->close();
            
            $postStmt = $mysqli->prepare("SELECT judul, created_at FROM posts_new WHERE id = ?");
            $postStmt->bind_param('i', $row['photo_id']);
            $postStmt->execute();
            $postResult = $postStmt->get_result();
            $post = $postResult->fetch_assoc();
            $postStmt->close();
            
            // Tentukan tipe dan judul
            if ($photo && $post) {
                // Jika keduanya ada, pilih yang lebih dekat waktunya
                $photoDiff = abs(strtotime($row['created_at']) - strtotime($photo['created_at']));
                $postDiff = abs(strtotime($row['created_at']) - strtotime($post['created_at']));
                if ($postDiff < $photoDiff) {
                    $type = 'Berita';
                    $title = $post['judul'];
                } else {
                    $type = 'Foto';
                    $title = $photo['judul'] ?: 'Foto #' . $row['photo_id'];
                }
            } else {
                $type = $photo ? 'Foto' : ($post ? 'Berita' : 'Unknown');
                $title = $photo ? ($photo['judul'] ?: 'Foto #' . $row['photo_id']) : ($post ? $post['judul'] : 'Item #' . $row['photo_id']);
            }
            
            $statusText = $row['status'] === 'approved' ? 'Disetujui' : ($row['status'] === 'rejected' ? 'Tidak Disetujui' : 'Menunggu');
            
            $items[] = [
                'id' => $row['id'],
                'name' => $row['user_name'] ?: 'Anonymous',
                'email' => $row['user_email'] ?: '-',
                'body' => $row['body'],
                'type' => $type,
                'title' => $title,
                'created_at' => $row['created_at'],
                'status' => $statusText,
            ];
        }
        $stmt->close();
        
        echo json_encode(['data' => $items]);
        exit;
    }

    $id = $_GET['id'] ?? '';

    if ($method === 'POST' && ($action = ($_GET['action'] ?? '')) !== '') {
        if ($action === 'approve') {
            $stmt = $mysqli->prepare("UPDATE photo_comments SET status = 'approved' WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['ok' => true]);
            exit;
        } elseif ($action === 'reject') {
            $stmt = $mysqli->prepare("UPDATE photo_comments SET status = 'rejected' WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['ok' => true]);
            exit;
        }
    }
    
    if ($method === 'DELETE') {
        $stmt = $mysqli->prepare("DELETE FROM photo_comments WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['ok' => false, 'error' => 'Unsupported']);
    exit;
}
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h3>Moderasi Komentar Foto & Berita</h3>
        <span class="muted" id="totalInfo"></span>
    </div>
    <style>
    /* Focus highlight for deep-linked activity */
    .focus-glow { background:#ecfdf5 !important; box-shadow: 0 0 0 3px #86efac inset, 0 10px 24px rgba(16,185,129,.15); transition: background .3s ease; }
    </style>
    <div style="overflow-x:auto">
        <table id="kfTable" style="width:100%; border-collapse:collapse; display:none;">
            <thead>
                <tr>
                    <th style="background:#1d4ed8;color:#fff;">No</th>
                    <th style="background:#1d4ed8;color:#fff;">Judul</th>
                    <th style="background:#1d4ed8;color:#fff;">Nama pengirim</th>
                    <th style="background:#1d4ed8;color:#fff;">Pesan komentar</th>
                    <th style="background:#1d4ed8;color:#fff;">Tanggal kirim</th>
                    <th style="background:#1d4ed8;color:#fff;">Status</th>
                    <th style="background:#1d4ed8;color:#fff;">Aksi</th>
                </tr>
            </thead>
            <tbody id="kfBody"></tbody>
        </table>
        <div id="emptyState" class="muted" style="text-align:center; padding:24px; display:none;">Belum ada komentar yang masuk.</div>
    </div>
</div>
<script>
(function(){
    const body = document.getElementById('kfBody');
    const table = document.getElementById('kfTable');
    const empty = document.getElementById('emptyState');
    const totalInfo = document.getElementById('totalInfo');

    function fmt(dt){ try{ return new Date(dt).toLocaleString('id-ID',{hour12:false}); }catch(_){ return dt||'-'; } }
    function pill(status){
        const s = (status||'').toLowerCase();
        if (s === 'disetujui') return '<span style="background:#dcfce7;color:#166534;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:700;">Disetujui</span>';
        if (s === 'tidak disetujui') return '<span style="background:#fee2e2;color:#991b1b;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:700;">Tidak Disetujui</span>';
        return '<span style="background:#fef9c3;color:#92400e;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:700;">Menunggu</span>';
    }
    function actions(id, status){
        const disApp = status==='Disetujui' ? 'disabled' : '';
        const disRej = status==='Tidak Disetujui' ? 'disabled' : '';
        return `<div class="actions">
            <button onclick="KF.approve('${id}')" class="btn" ${disApp}>Setujui</button>
            <button onclick="KF.reject('${id}')" class="btn" style="background:#ef4444" ${disRej}>Tidak Disetujui</button>
            <button onclick="KF.remove('${id}')" class="btn" style="background:#6b7280">Hapus</button>
        </div>`;
    }

    async function list(){
        try{
            const res = await fetch('pages/komentar_foto.php?ajax=1', { headers: { 'Accept': 'application/json' } });
            if(!res.ok) throw new Error('HTTP '+res.status);
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : [];
            render(items);
        }catch(e){
            console.error(e);
            totalInfo.textContent = '';
            table.style.display = 'none';
            empty.style.display = 'block';
        }
    }

    function render(items){
        body.innerHTML = '';
        totalInfo.textContent = `Total: ${items.length} komentar`;
        if (!items.length) {
            table.style.display = 'none';
            empty.style.display = 'block';
            return;
        }
        table.style.display = '';
        empty.style.display = 'none';
        items.forEach((it, i)=>{
            const tr = document.createElement('tr');
            tr.classList.add('kf-row');
            tr.dataset.title = (it.title||'').toString();
            tr.innerHTML = `
                <td>${i+1}</td>
                <td style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${(it.title||'').toString().replace(/\"/g,'&quot;')}">${it.title||''}</td>
                <td>${(it.name||'').toString().substring(0,100)}</td>
                <td style="max-width:400px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${(it.body||'').toString().replace(/\"/g,'&quot;')}">${it.body||''}</td>
                <td class="muted">${fmt(it.created_at)}</td>
                <td>${pill(it.status)}</td>
                <td>${actions(it.id, it.status)}</td>
            `;
            body.appendChild(tr);
        });

        // If navigated from Dashboard with focus params, highlight the best match
        try {
            const params = new URLSearchParams(window.location.search);
            if (params.get('act_focus') === '1') {
                const targetTitle = (params.get('act_title')||'').toLowerCase().trim();
                let targetEl = null;
                if (targetTitle) {
                    document.querySelectorAll('.kf-row').forEach(el => {
                        const t = (el.dataset.title||'').toLowerCase();
                        if (!targetEl && t.includes(targetTitle)) targetEl = el;
                    });
                }
                // fallback: highlight first row if no match
                targetEl = targetEl || document.querySelector('.kf-row');
                if (targetEl) {
                    targetEl.classList.add('focus-glow');
                    setTimeout(()=> targetEl.classList.remove('focus-glow'), 2500);
                    targetEl.scrollIntoView({behavior:'smooth', block:'center'});
                }
            }
        } catch(_) {}
    }

    async function post(action, id, method){
        const url = `pages/komentar_foto.php?ajax=1&action=${encodeURIComponent(action)}&id=${encodeURIComponent(id)}`;
        const res = await fetch(url, { method: method||'POST', headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Request failed');
        return res.json();
    }

    window.KF = {
        approve: async function(id){ try{ await post('approve', id, 'POST'); await list(); }catch(e){ alert('Gagal menyetujui'); } },
        reject : async function(id){ try{ await post('reject', id, 'POST'); await list(); }catch(e){ alert('Gagal menolak'); } },
        remove : async function(id){ if(!confirm('Hapus komentar ini?')) return; try{ await post('delete', id, 'DELETE'); await list(); }catch(e){ alert('Gagal menghapus'); } },
    };

    list();
})();
</script>

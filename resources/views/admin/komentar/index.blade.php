@extends('admin.layouts.app')

@section('title', 'Komentar Foto & Berita - Admin')
@section('page-title', 'Komentar Foto & Berita')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Moderasi Komentar Foto & Berita</h2>
    <p class="text-gray-600 mb-4">Total: <span id="totalCount" class="font-semibold">0</span> komentar</p>

    <div id="emptyState" class="hidden text-center py-12">
        <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
        <p class="text-gray-600">Belum ada komentar yang masuk.</p>
    </div>

    <div class="overflow-x-auto">
        <table id="commentsTable" class="min-w-full border rounded-lg hidden">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tipe</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Judul</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Nama pengirim</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Pesan komentar</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal kirim</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody id="commentsBody" class="divide-y"></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const table = document.getElementById('commentsTable');
    const body = document.getElementById('commentsBody');
    const empty = document.getElementById('emptyState');

    async function fetchList(){
        try{
            const res = await fetch("{{ route('admin.komentar.list') }}", { headers: { 'Accept': 'application/json' } });
            if(!res.ok) throw new Error('Gagal memuat');
            const json = await res.json();
            const items = Array.isArray(json.data) ? json.data : [];
            render(items);
        }catch(e){
            console.error(e);
            render([]);
        }
    }

    function fmt(dt){
        try{ return new Date(dt).toLocaleString('id-ID', { hour12: false }); }catch(_){ return dt || '-'; }
    }

    function pill(status){
        const map = {
            'Disetujui': 'bg-green-100 text-green-800',
            'Tidak Disetujui': 'bg-red-100 text-red-800',
            'Menunggu': 'bg-yellow-100 text-yellow-800',
        };
        const cls = map[status] || 'bg-gray-100 text-gray-800';
        return `<span class="px-2 py-1 rounded text-xs font-medium ${cls}">${status || '-'}</span>`;
    }

    function actionButtons(id, status){
        return `
            <div class="flex items-center gap-2">
                <button data-act="approve" data-id="${id}" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs" ${status==='Disetujui'?'disabled':''}>Setujui</button>
                <button data-act="reject" data-id="${id}" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs" ${status==='Tidak Disetujui'?'disabled':''}>Tidak Disetujui</button>
                <button data-act="delete" data-id="${id}" class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white rounded text-xs">Hapus</button>
            </div>
        `;
    }

    function render(items){
        body.innerHTML = '';
        document.getElementById('totalCount').textContent = items.length;
        if(!items.length){
            table.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
        }
        empty.classList.add('hidden');
        table.classList.remove('hidden');
        items.forEach((it, idx)=>{
            const tr = document.createElement('tr');
            const typeBadge = it.type === 'Foto' ? '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">📷 Foto</span>' : '<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">📰 Berita</span>';
            tr.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-700">${idx+1}</td>
                <td class="px-4 py-3 text-sm">${typeBadge}</td>
                <td class="px-4 py-3 text-sm text-gray-700">${(it.title||'').toString().substring(0,50)}</td>
                <td class="px-4 py-3 text-sm text-gray-800">${(it.name||'').toString().substring(0,100)}</td>
                <td class="px-4 py-3 text-sm text-gray-700">${(it.body||'')}</td>
                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">${fmt(it.created_at)}</td>
                <td class="px-4 py-3">${pill(it.status)}</td>
                <td class="px-4 py-3">${actionButtons(it.id, it.status)}</td>
            `;
            body.appendChild(tr);
        });
    }

    async function post(url, method){
        const res = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: method==='DELETE' ? null : JSON.stringify({})
        });
        if(!res.ok) throw new Error('Request failed');
        return res.json();
    }

    body.addEventListener('click', async (e)=>{
        const btn = e.target.closest('button[data-act]');
        if(!btn) return;
        const id = btn.getAttribute('data-id');
        const act = btn.getAttribute('data-act');
        try{
            if(act==='approve'){
                await post(`{{ url('admin/komentar-foto') }}/${id}/approve`, 'POST');
            }else if(act==='reject'){
                await post(`{{ url('admin/komentar-foto') }}/${id}/reject`, 'POST');
            }else if(act==='delete'){
                if(!confirm('Yakin ingin menghapus komentar ini?')) return;
                await post(`{{ url('admin/komentar-foto') }}/${id}`, 'DELETE');
            }
            await fetchList();
        }catch(err){
            console.error(err);
            alert('Terjadi kesalahan. Coba lagi.');
        }
    });

    fetchList();
})();
</script>
@endsection

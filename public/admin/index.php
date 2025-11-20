<?php
require_once __DIR__ . '/_auth.php';

// Start output buffering so included pages can safely call header() for redirects
ob_start();

$username = $_SESSION['petugas_username'] ?? 'Petugas';

// Simple router via ?page=
$page = $_GET['page'] ?? 'home';

// Calculate pending messages count for sidebar notification
$pesanPendingCount = 0;
if (isset($mysqli) && $mysqli) {
    // Check if approval_status column exists first
    $columnExists = false;
    if ($q = $mysqli->query("SHOW COLUMNS FROM messages_new LIKE 'approval_status'")) {
        $columnExists = $q->num_rows > 0;
        $q->close();
    }
    
    // Count unread messages for notification badge
    if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM messages_new WHERE status='unread'")) { 
        $row = $q->fetch_assoc(); 
        $pesanPendingCount = (int)($row['c'] ?? 0); 
        $q->close(); 
    }
}

?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root{ --brand:#023859; --brand-dark:#012a44; --ring: rgba(2,56,89,.18); --muted:#94a3b8; }
        body { margin: 0; font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:
            radial-gradient(800px 400px at 0% 0%, rgba(2,56,89,.05), transparent 60%),
            radial-gradient(800px 400px at 100% 0%, rgba(2,56,89,.04), transparent 60%),
            #f6f8fb; color:#0f172a; }
        .layout { display: grid; grid-template-columns: 210px 1fr; min-height: 100vh; align-items: start; }
        .sidebar { position: sticky; top: 0; height: 100vh; overflow-y: auto; background: var(--brand); color: #e5e7eb; padding: 12px; box-shadow: 4px 0 18px rgba(2,56,89,.15); }
        .sidebar h2 { color: #fff; font-size: 16px; margin: 0 0 10px; font-weight:700; letter-spacing:.15px; }
        .nav a { display: block; color: #e5e7eb; text-decoration: none; padding: 8px 10px; border-radius: 9px; margin-bottom: 5px; transition: background .2s, transform .05s; font-size: 12px; line-height: 1.3; font-weight: 600; width: 100%; box-sizing: border-box; }
        .nav a:hover, .nav a.active { background: rgba(255,255,255,.12); }
        .topbar { background: linear-gradient(180deg,#ffffff,#fbfdff); border-bottom: 1px solid rgba(2,56,89,.12); padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; position: sticky; top:0; z-index:5; backdrop-filter: blur(6px); flex: 0 0 auto; }
        /* keep main as-is; no margin-left to preserve existing layout */
        .content { padding: 14px; }
        .btn { background: var(--brand); color: white; padding: 5px 9px; border: none; border-radius: 7px; text-decoration: none; display: inline-flex; align-items:center; gap:6px; font-weight:700; font-size:11px; box-shadow: 0 4px 10px rgba(2,56,89,.14); }
        .btn:hover { filter: brightness(1.03); }
        .btn.danger { background:#ef4444; box-shadow: 0 4px 10px rgba(239,68,68,.15); }
        .card { background: #ffffff; border: 1px solid rgba(2,56,89,.08); border-radius: 14px; padding: 16px; box-shadow: 0 8px 18px rgba(2,56,89,.08); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #eef2f7; text-align: left; font-size: 13px; }
        th { background: #f8fbff; font-weight: 600; color:#023859; }
        .muted { color: #6b7280; }
        /* Summary stat cards */
        .summary-grid{display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:8px}
        @media(min-width:900px){ .summary-grid{grid-template-columns: repeat(4, minmax(0,1fr));} }
        .summary-card{background:#ffffff; border:1px solid #e6eef5; border-radius:10px; padding:12px; text-align:center}
        .summary-card .num{font-weight:700; color:#023859; font-size:20px}
        .summary-card .lbl{color:#64748b; font-weight:600; font-size:11px}
        .nav-section { color: #e2e8f0; padding: 8px 10px 7px; margin-top: 16px; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; border-bottom: 1px solid rgba(255,255,255,.28); width: 100%; box-sizing: border-box; }
        .hello { color:#334155; }
        .actions { display:flex; align-items:center; gap:8px; white-space:nowrap; }
        .actions form { display:inline; }
        /* Activity items */
        .activity-list .act-item{border:1px solid #eef2f7; border-radius:10px; padding:9px 10px; margin-bottom:9px; background:#fff; display:flex; align-items:center; justify-content:space-between}
        
        .quick-grid{display:grid; grid-template-columns: repeat(1,minmax(0,1fr)); gap:12px}
        @media(min-width:900px){ .quick-grid{grid-template-columns: repeat(2, minmax(0,1fr));} }
        .quick-link{display:flex; align-items:center; gap:10px; background:#ffffff; border:2px solid #e6eef5; border-radius:11px; padding:14px; text-decoration:none; color:#023859; font-weight:600; min-height:62px; transition:all 0.3s ease; position:relative; overflow:hidden}
        .quick-link:hover{background:#f8fbff; border-color:#3b82f6; transform:translateY(-2px); box-shadow:0 6px 14px rgba(59,130,246,0.12)}
        .quick-link::before{content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:linear-gradient(180deg, #3b82f6, #1e40af); opacity:0; transition:opacity 0.3s}
        .quick-link:hover::before{opacity:1}
        .quick-icon{width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0}
        .quick-icon.icon-berita{background:#f5f3ff; color:#8b5cf6}
        .quick-icon.icon-agenda{background:#eff6ff; color:#3b82f6}
        .quick-icon.icon-galeri{background:#ecfdf5; color:#10b981}
        .quick-icon.icon-foto{background:#fffbeb; color:#f59e0b}
        .quick-content{flex:1}
        .quick-title{font-size:14px; font-weight:600; color:#023859; margin-bottom:2px}
        .quick-desc{font-size:11px; color:#64748b; font-weight:500}
        .quick-arrow{font-size:16px; color:#3b82f6; transition:transform 0.3s}
        .quick-link:hover .quick-arrow{transform:translateX(4px)}
        /* Dashboard layout */
        .dash-grid{display:grid; grid-template-columns: 1fr; gap:14px}
        @media(min-width:1100px){ .dash-grid{grid-template-columns: 1fr 320px;} }
        .col-main{display:flex; flex-direction:column; gap:14px}
        .col-side{display:flex; flex-direction:column; gap:14px}
        /* Mini calendar & notifications */
        .mini-cal h4, .notif-card h4{margin:0 0 8px; color:#023859; font-size:14px}
        .mini-cal-list{list-style:none; padding:0; margin:0}
        .mini-item{display:flex; align-items:center; justify-content:space-between; padding:10px 12px; border:1px solid #e6eef5; border-radius:10px; background:#ffffff; margin-bottom:9px; min-height:52px}
        .mini-item .when{display:flex; align-items:center; gap:8px; color:#023859; font-weight:600; font-size:13px}
        .mini-item .when .date{background:#e6f4ff; color:#1e40af; border:1px solid #cfe3ff; padding:3px 9px; border-radius:9999px; font-size:11px}
        .notif-list{display:flex; flex-direction:column; gap:9px}
        .notif{display:flex; align-items:center; justify-content:space-between; border:1px solid #e6eef5; border-radius:10px; padding:10px 12px; background:#ffffff; min-height:52px}
        .notif .label{display:flex; align-items:center; gap:10px}
        .badge{padding:2px 7px; border-radius:9999px; font-size:10px; font-weight:600}
        .badge-blue{background:#e6f0ff; color:#1e3a8a; border:1px solid #cbd5ff}
        .badge-green{background:#ecfdf5; color:#065f46; border:1px solid #bbf7d0}
        .badge-amber{background:#fffbeb; color:#92400e; border:1px solid #fde68a}
        /* Activity list */
        /* Activity vertical list with internal scroll */
        .activity-list{display:flex; flex-direction:column; gap:10px}
        .activity{display:flex; align-items:center; justify-content:space-between; border:1px solid #e6eef5; border-radius:10px; padding:10px 12px; background:#ffffff}
        .activity .info{display:flex; align-items:center; gap:10px}
        .activity .icon{width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(2,56,89,.08); font-size:14px}
        .activity .title{font-weight:700; color:#0f172a; font-size:13px}
        .activity .meta{font-size:11px; color:#64748b}
        .icon-blue{background:#e8f1ff; color:#1e3a8a; border:1px solid #cbd5ff}
        .icon-green{background:#e8fff5; color:#065f46; border:1px solid #bbf7d0}
        .icon-amber{background:#fff7e6; color:#92400e; border:1px solid #fde68a}
        /* Activity filters and scroll */
        .act-toolbar{display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:8px}
        .tabs{display:flex; gap:6px; flex-wrap:wrap}
        .tab-btn{background:#f1f5f9; color:#0f172a; border:1px solid #e2e8f0; padding:5px 9px; border-radius:9999px; font-size:11px; font-weight:600; cursor:pointer}
        .tab-btn.active{background:#e6f0ff; color:#1e3a8a; border-color:#cbd5ff}
        .activity-scroll{max-height:240px; overflow-y:auto; padding-right:4px}
        .hidden{display:none !important}
        
        /* Mobile toggle button (hidden on desktop) */
        .sidebar-toggle {
            display: none;
        }
        
        /* Mobile-specific styles */
        
        @media (max-width: 768px) {
            .layout {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }
            
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                max-height: 100vh;
                padding: 0;
                box-shadow: none;
                background: rgba(0, 0, 0, 0.5);
                display: none;
                z-index: 1000;
            }
            
            .sidebar.open {
                display: block;
            }
            
            .sidebar-content {
                background: var(--brand);
                padding: 12px;
                box-shadow: 0 4px 18px rgba(2,56,89,.15);
                width: 80%;
                height: 100vh;
                max-height: 100vh;
                overflow-y: auto;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                -webkit-overflow-scrolling: touch;
                display: flex;
                flex-direction: column;
            }
            
            .sidebar.open .sidebar-content {
                transform: translateX(0);
            }
            
            .sidebar-toggle {
                display: block;
                background: none;
                border: none;
                color: var(--brand);
                font-size: 24px;
                cursor: pointer;
                padding: 5px;
                margin-right: 10px;
                z-index: 1001;
                flex-shrink: 0;
            }
            
            .sidebar-close-container {
                display: block !important;
            }
            
            .topbar {
                padding: 10px;
                display: flex;
                align-items: center;
            }
            
            .topbar-left {
                display: flex;
                align-items: center;
                flex: 1;
            }
            
            .content {
                padding: 10px;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-grid {
                grid-template-columns: 1fr;
            }
            
            .dash-grid {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 12px;
                display: block;
                overflow-x: auto;
            }
                    
            .table-container {
                overflow-x: auto;
                margin-bottom: 15px;
            }
            
            th, td {
                padding: 6px;
            }
            
            .card {
                padding: 12px;
            }
            
            /* Ensure nav links are properly styled on mobile */
            .nav a {
                padding: 10px 12px;
                font-size: 14px;
                margin-bottom: 6px;
                width: 100%;
                box-sizing: border-box;
            }
            
            .nav-section {
                padding: 10px 12px 8px;
                font-size: 12px;
                margin-top: 18px;
                width: 100%;
                box-sizing: border-box;
            }
            
            .dashboard-link {
                font-size: 20px;
                margin: 0 0 15px;
                padding: 10px 12px;
            }
        }
        
        /* Desktop styles (override mobile when needed) */
        @media (min-width: 769px) {
            .sidebar-toggle {
                display: none !important;
            }
            
            .sidebar {
                display: block !important;
                position: sticky;
                width: 210px;
                height: 100vh;
                background: var(--brand);
            }
            
            .sidebar-content {
                transform: none !important;
                width: 100%;
                height: 100%;
            }
            
            .sidebar-close-container {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="layout" style="display: flex;">
        <!-- Blue sidebar on the left -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-content" style="display: flex; flex-direction: column;">
            <!-- Close button for mobile -->
            <div style="text-align: right; margin-bottom: 10px;" class="sidebar-close-container">
                <button class="sidebar-close" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px 10px;">&times;</button>
            </div>
            <nav class="nav" style="flex: 1; display: flex; flex-direction: column;">
                <!-- Dashboard menu in sidebar -->
                <a href="?page=home" class="<?php echo $page==='home'?'active':''; ?>" style="display: block; color: #e5e7eb; text-decoration: none; padding: 8px 10px; border-radius: 9px; margin-bottom: 5px; transition: background .2s, transform .05s; font-size: 12px; line-height: 1.3; font-weight: 600; width: 100%; box-sizing: border-box;">Dashboard</a>
                
                <!-- Konten Sekolah -->
                <div class="nav-section">Konten Sekolah</div>
                <a href="?page=galeri" class="<?php echo $page==='galeri'?'active':''; ?>">Kelola Galeri Sekolah</a>
                <a href="?page=berita" class="<?php echo $page==='berita'?'active':''; ?>">Berita Sekolah</a>
                <a href="?page=agenda" class="<?php echo $page==='agenda'?'active':''; ?>">Agenda Sekolah</a>
                <a href="?page=kategori" class="<?php echo $page==='kategori'?'active':''; ?>">Kategori</a>

                <!-- Manajemen & Laporan -->
                <div class="nav-section">Manajemen & Laporan</div>
                <a href="?page=petugas" class="<?php echo $page==='petugas'?'active':''; ?>">Petugas</a>
                <a href="?page=users" class="<?php echo $page==='users'?'active':''; ?>">Users</a>
                <a href="?page=komentar_foto" class="<?php echo $page==='komentar_foto'?'active':''; ?>">Komentar Foto</a>
                <a href="?page=downloads" class="<?php echo $page==='downloads'?'active':''; ?>">Unduhan Foto</a>
                <a href="?page=pesan" class="<?php echo $page==='pesan'?'active':''; ?>">
                    Pesan
                    <?php if (isset($pesanPendingCount) && $pesanPendingCount > 0): ?>
                        <span style="background:#ef4444; color:#fff; font-size:10px; padding:2px 6px; border-radius:9999px; margin-left:6px;"><?php echo $pesanPendingCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="?page=reports" class="<?php echo $page==='reports'?'active':''; ?>">Laporan</a>

                <!-- Sistem -->
                <div class="nav-section">Sistem</div>
                <a href="../logout.php" class="">Logout</a>
            </nav>
            </div>
        </aside>
        
        <!-- Main content area on the right -->
        <main style="flex: 1; display: flex; flex-direction: column;">
            <!-- White header bar full width above content -->
            <header class="topbar" style="background: linear-gradient(180deg,#ffffff,#fbfdff); border-bottom: 1px solid rgba(2,56,89,.12); padding: 10px 16px; display: flex; justify-content: space-between; align-items: center;">
                <div class="topbar-left" style="display: flex; align-items: center; gap: 10px;">
                    <button class="sidebar-toggle" id="sidebarToggle" style="background: none; border: none; color: #023859; font-size: 24px; cursor: pointer; padding: 5px;">☰</button>
                    <div style="font-weight:800;color:#023859;">Panel Admin</div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="hello">Halo, <?php echo e($username); ?></span>
                    <a href="../logout.php" class="btn danger" title="Logout" aria-label="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </a>
                </div>
            </header>
            
            <!-- Dashboard content area -->
            <div class="content" style="flex: 1; background: #f6f8fb; padding: 14px;">
                <?php
                switch ($page) {
                    case 'berita':
                        require __DIR__ . '/pages/berita.php';
                        break;
                    case 'galeri':
                        require __DIR__ . '/pages/galeri.php';
                        break;
                    case 'agenda':
                        require __DIR__ . '/pages/agenda.php';
                        break;
                    case 'kategori':
                        require __DIR__ . '/pages/kategori.php';
                        break;
                    case 'photos':
                        require __DIR__ . '/pages/photos.php';
                        break;
                    case 'downloads':
                        require __DIR__ . '/pages/downloads.php';
                        break;
                    case 'reports':
                        require __DIR__ . '/pages/reports.php';
                        break;
                    case 'pesan':
                        require __DIR__ . '/pages/pesan.php';
                        break;
                    case 'petugas':
                        require __DIR__ . '/pages/petugas.php';
                        break;
                    case 'users':
                        require __DIR__ . '/pages/users.php';
                        break;
                    case 'komentar_foto':
                        require __DIR__ . '/pages/komentar_foto.php';
                        break;
                    case 'home':
                    default:
                        // Compute real-time stats from DB with graceful fallbacks
                        $serverStatus = 'Online';
                        // Visitors (try 'visitors' then 'page_views')
                        $visitorsToday = 0;
                        if (function_exists('tableExists') && tableExists($mysqli, 'visitors')) {
                            if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM visitors WHERE DATE(created_at)=CURDATE()")) { $row=$q->fetch_assoc(); $visitorsToday=(int)($row['c']??0); $q->close(); }
                        } elseif (tableExists($mysqli, 'page_views')) {
                            if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM page_views WHERE DATE(created_at)=CURDATE()")) { $row=$q->fetch_assoc(); $visitorsToday=(int)($row['c']??0); $q->close(); }
                        }
                        // Total photos from photos table (galeri + berita)
                        $photosTotal = 0;
                        if (function_exists('tableExists') && tableExists($mysqli, 'photos')) {
                            if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM photos")) { $row=$q->fetch_assoc(); $photosTotal=(int)($row['c']??0); $q->close(); }
                        }
                        // Admins (petugas)
                        $adminsActive = 0; if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM petugas")) { $row=$q->fetch_assoc(); $adminsActive=(int)($row['c']??0); $q->close(); }
                        // Komentar foto (TOTAL) dari JSON agar selaras dengan publik
                        $commentsTotal = 0;
                        $root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
                        if ($root !== false) {
                            $jsonPath = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'komentar_temp.json';
                            if (file_exists($jsonPath)) {
                                $raw = @file_get_contents($jsonPath);
                                if ($raw !== false && $raw !== '') {
                                    $arr = json_decode($raw, true);
                                    if (is_array($arr)) {
                                        $commentsTotal = count($arr);
                                    }
                                }
                            }
                        }
                        // Total likes for photos (safe if table exists)
                        $likesTotal = 0; if (function_exists('tableExists') && tableExists($mysqli,'photo_likes')) { if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM photo_likes")) { $row=$q->fetch_assoc(); $likesTotal=(int)($row['c']??0); $q->close(); } }
                        // Total photo downloads from JSON counter
                        $downloadsTotal = 0;
                        if (isset($mysqli) && $mysqli && function_exists('tableExists') && tableExists($mysqli, 'photo_downloads')) {
                            if ($res = $mysqli->query('SELECT COUNT(*) AS c FROM photo_downloads')) {
                                $row = $res->fetch_assoc();
                                $downloadsTotal = (int)($row['c'] ?? 0);
                                $res->close();
                            }
                        } elseif ($root !== false) {
                            // Legacy fallback menggunakan JSON lama
                            $subPath = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'photo_download_submissions.json';
                            if (file_exists($subPath)) {
                                $raw = @file_get_contents($subPath);
                                if ($raw) {
                                    $arr = json_decode($raw, true);
                                    if (is_array($arr)) {
                                        foreach ($arr as $row) {
                                            if (($row['status'] ?? '') === 'downloaded') { $downloadsTotal++; }
                                        }
                                    }
                                }
                            }
                        }
                        echo '<div class="card" style="margin-bottom:12px">'
                            .'<div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center">'
                                .'<div style="display:flex; align-items:center; gap:6px; background:#f1f8ff; border:1px solid #d9e8f7; color:#023859; padding:6px 10px; border-radius:9999px; font-weight:600; font-size:12px;"><span>Pengunjung Hari Ini: '.$visitorsToday.'</span></div>'
                                .'<div style="display:flex; align-items:center; gap:6px; background:#e6f4ff; border:1px solid #cfe3ff; color:#1e40af; padding:6px 10px; border-radius:9999px; font-weight:600; font-size:12px;"><span>Total Foto: '.$photosTotal.'</span></div>'
                                .'<div style="display:flex; align-items:center; gap:6px; background:#ecfdf5; border:1px solid #bbf7d0; color:#065f46; padding:6px 10px; border-radius:9999px; font-weight:600; font-size:12px;"><span>Admin Aktif: '.$adminsActive.'</span></div>'
                                .'<div style="display:flex; align-items:center; gap:6px; background:#eef2ff; border:1px solid #c7d2fe; color:#3730a3; padding:6px 10px; border-radius:9999px; font-weight:600; font-size:12px;"><span>Komentar (Total): '.$commentsTotal.'</span></div>'
                                .'<div style="display:flex; align-items:center; gap:6px; background:#ffe4e6; border:1px solid #fecdd3; color:#9f1239; padding:6px 10px; border-radius:9999px; font-weight:600; font-size:12px;"><span>Like Foto: '.$likesTotal.'</span></div>'
                                .'<div style="display:flex; align-items:center; gap:6px; background:#fef3c7; border:1px solid #fde68a; color:#92400e; padding:6px 10px; border-radius:9999px; font-weight:600; font-size:12px;"><span>Unduh Foto: '.$downloadsTotal.'</span></div>'
                                .'<div style="display:flex; align-items:center; gap:6px; background:#ecfeff; border:1px solid #a5f3fc; color:#155e75; padding:6px 10px; border-radius:9999px; font-weight:600; font-size:12px;"><span>Server: '.$serverStatus.'</span></div>'
                            .'</div>'
                        .'</div>';

                        // Content stats cards (aligned with public filters)
                        $beritaCount = 0; $acaraCount = 0; $galeriCount = 0; $pesanCount = 0; $agendaCount = 0;
                        // Berita: published + kategori judul like ('berita','news','terkini')
                        if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM posts_new p INNER JOIN kategori_new k ON k.id=p.kategori_id WHERE p.status='published' AND (k.judul LIKE '%berita%' OR k.judul LIKE '%news%' OR k.judul LIKE '%terkini%')")) { $row=$q->fetch_assoc(); $beritaCount=(int)($row['c']??0); $q->close(); }
                        // Acara Sekolah: jumlah foto bertipe 'acara' pada galeri kategori 'acara sekolah' (selaras dengan halaman publik acara)
                        if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM photos f INNER JOIN galleries g ON g.id=f.gallery_id WHERE f.related_type='acara' AND (g.status IN ('active','published') OR g.status=0) AND g.category='acara sekolah'")) { $row=$q->fetch_assoc(); $acaraCount=(int)($row['c']??0); $q->close(); }
                        // Galeri Sekolah: hanya album kategori non-berita dengan status 'published'
                        if ($q = $mysqli->query("SELECT COUNT(*) AS c FROM galleries WHERE category NOT IN ('berita', 'Berita Sekolah') AND status='published'")) { $row=$q->fetch_assoc(); $galeriCount=(int)($row['c']??0); $q->close(); }
                        // Agenda: total agenda from agenda table
                        if (tableExists($mysqli, 'agenda')) {
                            if ($q = $mysqli->query('SELECT COUNT(*) AS c FROM agenda')) { $row=$q->fetch_assoc(); $agendaCount=(int)($row['c']??0); $q->close(); }
                        }
                        // Pesan: total messages
                        if ($q = $mysqli->query('SELECT COUNT(*) AS c FROM messages_new')) { $row=$q->fetch_assoc(); $pesanCount=(int)($row['c']??0); $q->close(); }

                        // Ringkasan dalam bentuk stat cards (tanpa emoji)
                        $komentarPesan = (int)$commentsTotal + (int)$pesanCount;
                        echo '<div class="card" style="margin-bottom:14px">'
                            .'<h3 style="margin:0 0 12px; font-size:18px; color:#023859">Ringkasan</h3>'
                            .'<div class="summary-grid">'
                                .'<div class="summary-card">'
                                    .'<div class="num">'.$galeriCount.'</div>'
                                    .'<div class="lbl">Galeri Sekolah</div>'
                                .'</div>'
                                .'<div class="summary-card">'
                                    .'<div class="num">'.$beritaCount.'</div>'
                                    .'<div class="lbl">Berita Sekolah</div>'
                                .'</div>'
                                .'<div class="summary-card">'
                                    .'<div class="num">'.$agendaCount.'</div>'
                                    .'<div class="lbl">Agenda Sekolah</div>'
                                .'</div>'
                                .'<div class="summary-card">'
                                    .'<div class="num">'.$komentarPesan.'</div>'
                                    .'<div class="lbl">Komentar & Pesan</div>'
                                .'</div>'
                            .'</div>'
                        .'</div>';

                        // Unified two-column layout for consistent alignment
                        echo '<div class="dash-grid">';
                        echo '<div class="col-main">';
                        

                        // Activity List (dynamic, mixed sources)
                        echo '<div class="card" style="margin-bottom:0">'
                            .'<h3 style="margin:0 0 8px; font-size:18px; color:#023859">Aktivitas Terbaru</h3>';
                        $acts = [];
                        // Fetch per table to avoid UNION collation issues, then merge and sort in PHP
                        if (tableExists($mysqli, 'posts_new')) {
                            // Berita
                            if ($st = $mysqli->prepare('SELECT created_at, judul FROM posts_new WHERE kategori_id=? ORDER BY created_at DESC LIMIT 6')) {
                                $st->bind_param('i', $beritaKategoriId);
                                $st->execute(); $rs = $st->get_result();
                                while ($row = $rs->fetch_assoc()) { $acts[] = ['dt'=>$row['created_at'],'tp'=>'berita','title'=>$row['judul'],'by'=>'']; }
                                $st->close();
                            }
                        }
                        if (tableExists($mysqli, 'galleries')) {
                            if ($q = $mysqli->query('SELECT id, created_at, title FROM galleries ORDER BY created_at DESC LIMIT 6')) {
                                while ($row = $q->fetch_assoc()) { $acts[] = ['id'=>$row['id'],'dt'=>$row['created_at'],'tp'=>'galeri','title'=>$row['title'],'by'=>'']; }
                                $q->close();
                            }
                        }
                        // Recent photo uploads as Galeri activities (include uploader if available)
                        if (tableExists($mysqli, 'photos')) {
                            $hasCreatedByFoto = false; if ($ck = $mysqli->query("SHOW COLUMNS FROM photos LIKE 'created_by'")) { $hasCreatedByFoto = $ck->num_rows > 0; $ck->close(); }
                            $hasPetugas = tableExists($mysqli, 'petugas');
                            $uploaderSelect = ($hasCreatedByFoto && $hasPetugas) ? 'COALESCE(pet.username, "") AS uploader ' : '"" AS uploader ';
                            $joinPetugas = ($hasCreatedByFoto && $hasPetugas) ? 'LEFT JOIN petugas pet ON pet.id = f.created_by ' : '';
                            $sql = 'SELECT f.id AS pid, f.gallery_id AS gid, f.caption AS ptitle, f.created_at AS pcreated, '
                                 . 'g.title AS gtitle, ' . $uploaderSelect
                                 . 'FROM photos f '
                                 . 'LEFT JOIN galleries g ON g.id=f.gallery_id '
                                 . $joinPetugas
                                 . 'ORDER BY f.created_at DESC LIMIT 6';
                            if ($q = $mysqli->query($sql)) {
                                while ($row = $q->fetch_assoc()) {
                                    $ttl = trim((string)($row['ptitle'] ?? '')); if ($ttl==='') { $ttl = 'Foto baru'; }
                                    $gttl = trim((string)($row['gtitle'] ?? ''));
                                    $acts[] = [
                                        'id' => (int)($row['gid'] ?? 0),
                                        'pid'=> (int)($row['pid'] ?? 0),
                                        'dt' => $row['pcreated'] ?? null,
                                        'tp' => 'galeri',
                                        'title' => $ttl . ($gttl? (' • ' . $gttl) : ''),
                                        'by' => trim((string)($row['uploader'] ?? ''))
                                    ];
                                }
                                $q->close();
                            }
                        }
                        if (tableExists($mysqli, 'photo_comments')) {
                           $sql = "SELECT pc.created_at, 
               COALESCE(p.judul, f.caption, CONCAT('Item #', pc.photo_id)) AS ttl, 
               COALESCE(u.username,u.name,'Anonymous') AS actor
        FROM photo_comments pc
        LEFT JOIN posts_new p ON p.id = pc.photo_id
        LEFT JOIN photos f ON f.id = pc.photo_id
        LEFT JOIN users u ON u.id = pc.user_id
        WHERE pc.approved = 1
        ORDER BY pc.created_at DESC
        LIMIT 6";
                            if ($q = $mysqli->query($sql)) {
                                while ($row = $q->fetch_assoc()) { $acts[] = ['dt'=>$row['created_at'],'tp'=>'komentar','title'=>$row['ttl'],'by'=>($row['actor']??'')]; }
                                $q->close();
                            }
                        }
                        if (tableExists($mysqli, 'messages_new')) {
                            if ($q = $mysqli->query("SELECT created_at, name, CONCAT(name, ' - ', LEFT(message,60)) AS ttl FROM messages_new ORDER BY created_at DESC LIMIT 6")) {
                                while ($row = $q->fetch_assoc()) { $acts[] = ['dt'=>$row['created_at'],'tp'=>'pesan','title'=>$row['ttl'],'by'=>($row['name']??'')]; }
                                $q->close();
                            }
                        }
                        if (tableExists($mysqli, 'agenda')) {
                            if ($q = $mysqli->query('SELECT id, created_at, judul FROM agenda ORDER BY created_at DESC LIMIT 6')) {
                                while ($row = $q->fetch_assoc()) { $acts[] = ['id'=>$row['id'],'dt'=>$row['created_at'],'tp'=>'agenda','title'=>$row['judul'],'by'=>'']; }
                                $q->close();
                            }
                        }
                        // Sort desc by datetime and keep top 6
                        usort($acts, function($a,$b){ return strtotime($b['dt'] ?? '1970-01-01') <=> strtotime($a['dt'] ?? '1970-01-01'); });
                        if (count($acts) > 0) {
                            // Pastikan setiap kategori (pesan & komentar) minimal punya placeholder agar tab tidak kosong
                            $hasPesan = array_reduce($acts, function($carry, $item){ return $carry || ($item['tp'] ?? '') === 'pesan'; }, false);
                            $hasKomentar = array_reduce($acts, function($carry, $item){ return $carry || ($item['tp'] ?? '') === 'komentar'; }, false);
                            if (!$hasPesan) {
                                $acts[] = [
                                    'dt' => date('Y-m-d H:i:s'),
                                    'tp' => 'pesan',
                                    'title' => 'Belum ada pesan baru',
                                    'by' => ''
                                ];
                            }
                            if (!$hasKomentar) {
                                $acts[] = [
                                    'dt' => date('Y-m-d H:i:s'),
                                    'tp' => 'komentar',
                                    'title' => 'Belum ada komentar baru',
                                    'by' => ''
                                ];
                            }
                        }
                        if (count($acts) > 6) { $acts = array_slice($acts, 0, 6); }
                        if (!empty($acts)) {
                            // Build tabs and compact list (default show 5)
                            echo '<div class="act-toolbar">'
                                    .'<div class="tabs">'
                                        .'<button class="tab-btn active" data-filter="all">Semua</button>'
                                        .'<button class="tab-btn" data-filter="pesan">Pesan</button>'
                                        .'<button class="tab-btn" data-filter="galeri">Galeri</button>'
                                        .'<button class="tab-btn" data-filter="agenda">Agenda</button>'
                                        .'<button class="tab-btn" data-filter="komentar">Komentar</button>'
                                    .'</div>'
                                    .'<a href="#" id="toggle-acts" class="btn" data-state="collapsed">Lihat Semua Aktivitas</a>'
                                .'</div>';

                            echo '<div id="acts-wrap" class="activity-scroll">';
                            echo '<div class="activity-list">';
                            $idx = 0;
                            foreach ($acts as $a) {
                                $when = $a['dt'] ? date('d M Y H:i', strtotime($a['dt'])) : '-';
                                $title = e($a['title'] ?? '');
                                $tp = $a['tp'] ?? '';
                                $iconCls = ($tp==='berita') ? 'icon-blue' : (($tp==='galeri') ? 'icon-green' : (($tp==='agenda') ? 'icon-purple' : (($tp==='komentar') ? 'icon-amber' : 'icon-blue')));
                                $label = ($tp==='berita') ? 'Informasi' : (($tp==='galeri') ? 'Galeri' : (($tp==='agenda') ? 'Agenda' : (($tp==='komentar') ? 'Komentar' : 'Pesan')));
                                $common = '&act_title='.urlencode($title);
                                if ($tp==='komentar') {
                                    $goto = '?page=komentar_foto&act_focus=1&act_dt='.urlencode($a['dt']).$common;
                                } else if ($tp==='berita') {
                                    $goto = '?page=berita&act_focus=1'.$common;
                                } else if ($tp==='galeri') {
                                    $gid = (int)($a['id'] ?? 0);
                                    $pid = (int)($a['pid'] ?? 0);
                                    $goto = $gid > 0 ? ('?page=galeri&view='.$gid.'&act_focus=1'.($pid>0?('&act_photo='.$pid):'').$common) : ('?page=galeri&act_focus=1'.$common);
                                } else if ($tp==='agenda') {
                                    $goto = '?page=agenda&act_focus=1'.$common;
                                } else {
                                    $goto = '?page=pesan&act_focus=1'.$common;
                                }
                                $hidden = ($idx >= 5) ? ' hidden' : '';
                                $uid = substr(sha1(($a['dt']??'').'|'.$tp.'|'.$title),0,12);
                                $by = trim((string)($a['by'] ?? ''));
                                $byText = '';
                                if ($by !== '') {
                                    $byText = ($tp==='galeri') ? (' • diupload oleh: '.e($by)) : (' • oleh: '.e($by));
                                }
                                echo '<div class="activity act-item'.$hidden.'" data-type="'.$tp.'" data-key="'.$uid.'">'
                                    .'<div class="info">'
                                        .'<div class="icon '.$iconCls.'">📌</div>'
                                        .'<div>'
                                            .'<div class="title">'.$label.'</div>'
                                            .'<div class="meta">'.$when.' • '.$title.$byText.'</div>'
                                        .'</div>'
                                    .'</div>'
                                    .'<a class="btn act-open" data-key="'.$uid.'" href="'.$goto.'">Buka</a>'
                                .'</div>';
                                $idx++;
                            }
                            echo '</div>'; // .activity-list
                            echo '</div>'; // #acts-wrap
                            echo '<script>
                                (function(){
                                  const tabs = document.querySelectorAll(".tab-btn");
                                  const items = document.querySelectorAll(".act-item");
                                  const toggle = document.getElementById("toggle-acts");
                                  let expanded = false;
                                  function applyFilter(type){
                                    tabs.forEach(t=>t.classList.remove("active"));
                                    const active = Array.from(tabs).find(t=>t.getAttribute("data-filter")===(type||"all"));
                                    if(active) active.classList.add("active");
                                    items.forEach((el,i)=>{
                                      const showByFilter = (type==="all" || el.getAttribute("data-type")===type);
                                      const showByLimit = expanded || i < 5;
                                      el.classList.toggle("hidden", !(showByFilter && showByLimit));
                                    });
                                  }
                                  tabs.forEach(t=>t.addEventListener("click", function(e){ e.preventDefault(); applyFilter(this.getAttribute("data-filter")); }));
                                  toggle.addEventListener("click", function(e){ e.preventDefault(); expanded = !expanded; this.textContent = expanded ? "Tampilkan 5 Teratas" : "Lihat Semua Aktivitas"; applyFilter(document.querySelector(".tab-btn.active")?.getAttribute("data-filter")||"all"); });
                                  applyFilter("all");
                                })();
                            </script>';
                        } else {
                            // Fallback table if no data
                            echo '<table>'
                                .'<thead><tr><th>Waktu</th><th>Aktivitas</th><th>Detail</th></tr></thead>'
                                .'<tbody>'
                                    .'<tr><td>Hari ini</td><td>Tambah Berita</td><td>“Upacara 17 Agustus”</td></tr>'
                                    .'<tr><td>Kemarin</td><td>Perbarui Galeri</td><td>“Album Kegiatan OSIS”</td></tr>'
                                    .'<tr><td>2 hari lalu</td><td>Tambah Agenda</td><td>“Rapat Guru”</td></tr>'
                                .'</tbody>'
                            .'</table>';
                        }
                        echo '</div>';

                        
                        echo '</div>'; // end col-main

                        echo '</div>'; // end dash-grid

                        break;
                }
                ?>
                </div>
            </main>
        </div>
    
    <script>
        // Mobile menu toggle functionality
        function initMobileMenu() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarClose = document.querySelector('.sidebar-close');
            
            if (sidebarToggle && sidebar) {
                console.log('Sidebar elements found');
                
                sidebarToggle.addEventListener('click', function() {
                    console.log('Sidebar toggle clicked');
                    sidebar.classList.toggle('open');
                    console.log('Sidebar open class:', sidebar.classList.contains('open'));
                    // Visual feedback
                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
                
                // Close sidebar with close button
                if (sidebarClose) {
                    sidebarClose.addEventListener('click', function() {
                        console.log('Close button clicked');
                        sidebar.classList.remove('open');
                    });
                }
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 768 && 
                        !sidebar.contains(event.target) && 
                        event.target !== sidebarToggle &&
                        sidebar.classList.contains('open')) {
                        console.log('Clicked outside sidebar');
                        sidebar.classList.remove('open');
                    }
                });
                
                // Close sidebar when clicking on nav links (mobile)
                const navLinks = document.querySelectorAll('.nav a');
                navLinks.forEach(function(link) {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            console.log('Nav link clicked, closing sidebar');
                            sidebar.classList.remove('open');
                        }
                    });
                });
            } else {
                console.log('Sidebar elements not found:', !!sidebarToggle, !!sidebar);
            }
        }
        
        // Initialize mobile menu when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMobileMenu);
        } else {
            // DOM is already ready
            setTimeout(initMobileMenu, 100);
        }
    </script>
</body>
</html>
<?php
ob_end_flush();
?>

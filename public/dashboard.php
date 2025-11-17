<?php
session_start();

if (empty($_SESSION['petugas_id'])) {
    header('Location: login.php');
    exit;
}

$petugasUsername = isset($_SESSION['petugas_username']) ? (string) $_SESSION['petugas_username'] : 'Petugas';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{ --brand:#023859; --ring: rgba(2,56,89,.18); --muted:#64748b; }
        *{ box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background:
            radial-gradient(800px 400px at 0% 0%, rgba(2,56,89,.06), transparent 60%),
            radial-gradient(800px 400px at 100% 0%, rgba(2,56,89,.05), transparent 60%),
            #f6f8fb; color:#0f172a; }
        .nav { background: linear-gradient(180deg, var(--brand), #012a44); color: white; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 6px 20px rgba(2,56,89,.2); position: sticky; top:0; z-index:10; }
        .brand { font-weight: 800; letter-spacing:.2px; }
        .right { display:flex; align-items:center; gap:12px; }
        .hello { opacity:.9; font-size:14px; }
        a.btn { display:inline-flex; align-items:center; gap:8px; background: #ffffff; color: var(--brand); padding: 8px 12px; border-radius: 10px; text-decoration: none; font-weight:700; border:1px solid rgba(2,56,89,.15); box-shadow: 0 6px 14px rgba(2,56,89,.12); transition: filter .2s, transform .05s; }
        a.btn:hover { filter: brightness(1.02); }
        a.btn:active { transform: translateY(1px); }
        .container { max-width: 1000px; margin: 28px auto; background: #fff; padding: 28px; border-radius: 16px; box-shadow: 0 20px 50px rgba(2,56,89,.08); border:1px solid rgba(2,56,89,.08); }
        h1 { margin-top:0; font-size: 24px; font-weight: 800; color: var(--brand); }
        p { color:#334155; }
    </style>
</head>
<body>
    <div class="nav">
        <div class="brand">Dashboard Admin</div>
        <div class="right">
            <span class="hello">Halo, <?php echo htmlspecialchars($petugasUsername, ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="logout.php" class="btn" title="Keluar" aria-label="Keluar">
                <!-- Logout icon -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </a>
        </div>
    </div>
    <div class="container">
        <h1>Selamat datang, <?php echo htmlspecialchars($petugasUsername, ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p>Anda berhasil login sebagai petugas.</p>
        <p>Silakan lanjutkan ke fitur admin yang Anda perlukan.</p>
    </div>
</body>
</html>

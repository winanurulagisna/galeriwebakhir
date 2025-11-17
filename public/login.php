<?php
session_start();

// Redirect to dashboard if already logged in
if (!empty($_SESSION['petugas_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Bring in mysqli connection
require_once __DIR__ . '/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi';
    } else {
        // Prepare statement to avoid SQL injection
        $stmt = $mysqli->prepare('SELECT id, username, password FROM petugas WHERE username = ? LIMIT 1');
        if (!$stmt) {
            $error = 'Kesalahan server';
        } else {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$user) {
                $error = 'Username tidak ditemukan';
            } else {
                $storedHashOrPlain = (string) $user['password'];
                $isBcrypt = str_starts_with($storedHashOrPlain, '$2y$') || str_starts_with($storedHashOrPlain, '$2a$');

                $passwordOk = false;
                if ($isBcrypt) {
                    $passwordOk = password_verify($password, $storedHashOrPlain);
                } else {
                    // fallback plain text comparison
                    $passwordOk = hash_equals($storedHashOrPlain, $password);
                }

                if ($passwordOk) {
                    // Regenerate session id to prevent fixation
                    session_regenerate_id(true);
                    $_SESSION['petugas_id'] = (int) $user['id'];
                    $_SESSION['petugas_username'] = (string) $user['username'];

                    header('Location: admin/index.php');
                    exit;
                } else {
                    $error = 'Password salah';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#023859">
    <title>Login Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --brand: #023859;
            --brand-600: #034a76;
            --brand-700: #01304b;
            --muted: #6b7280;
            --ring: rgba(2,56,89,.18);
            --bg: radial-gradient(1200px 600px at 10% 10%, rgba(2,56,89,.08), transparent 60%),
                  radial-gradient(1200px 600px at 90% 20%, rgba(2,56,89,.06), transparent 60%),
                  #f6f8fb;
        }
        *{ box-sizing: border-box; }
        html, body { height: 100%; }
        body { margin: 0; font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: #0f172a; }

        .page { min-height: 100%; display: grid; place-items: center; padding: 24px; }
        .card {
            width: 100%; max-width: 440px; background: rgba(255,255,255,.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(2,56,89,.08);
            border-radius: 18px; padding: 28px; box-shadow: 0 30px 60px rgba(2,56,89,.12);
        }

        .brand { display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom: 8px; }
        .brand-badge { width: 44px; height: 44px; border-radius: 12px; background: #fff; display:grid; place-items:center; border:1px solid rgba(2,56,89,.12); box-shadow: 0 6px 18px rgba(2,56,89,.12); }
        .brand-badge img { width:30px; height:30px; object-fit:contain; }
        .title { margin: 6px 0 18px; text-align:center; font-size: 22px; font-weight: 800; color: var(--brand); letter-spacing: .2px; }

        .field { margin-bottom: 14px; }
        .label { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; font-size: 13px; font-weight: 600; color: #334155; }
        .control { position: relative; }
        .input { width: 100%; padding: 12px 44px 12px 44px; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 14px; background:#fff; transition: box-shadow .2s, border-color .2s, background .2s; }
        .input:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 4px var(--ring); background:#fff; }
        .icon { position:absolute; left:12px; top:50%; transform: translateY(-50%); color: #94a3b8; }
        .toggle { position:absolute; right:10px; top:50%; transform: translateY(-50%); cursor:pointer; color:#64748b; background:transparent; border:none; padding:6px; border-radius:8px; }
        .toggle:hover{ color: var(--brand); background: rgba(2,56,89,.06); }

        .btn { width: 100%; margin-top: 6px; background: var(--brand); color: white; padding: 12px 14px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; font-size: 14px; letter-spacing:.2px; box-shadow: 0 10px 20px rgba(2,56,89,.18), 0 4px 10px rgba(2,56,89,.12); transition: transform .05s ease, filter .2s ease, background .2s ease; }
        .btn:hover { filter: brightness(1.03); }
        .btn:active { transform: translateY(1px); }

        .error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 12px 14px; border-radius: 12px; margin-bottom: 14px; font-size: 14px; display:flex; align-items:flex-start; gap:10px; }
        .error svg{ flex:0 0 auto; margin-top:2px; }

        .footer { text-align: center; margin-top: 12px; color: var(--muted); font-size: 12px; }
        .helper { display:flex; align-items:center; justify-content:space-between; margin: 10px 2px 0; }
        .link { color: var(--brand); text-decoration:none; font-weight:600; font-size:12px; }
        .link:hover{ text-decoration:underline; }

        @media (max-width: 420px){ .card{ padding:22px; border-radius:16px; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="card" role="dialog" aria-labelledby="title" aria-describedby="desc">
            <div class="brand">
                <div class="brand-badge">
                    <img src="/images/LOGO.png" alt="Logo SMKN 4" onerror="this.style.display='none'">
                </div>
            </div>
            <h1 id="title" class="title">Login Admin</h1>
            <?php if ($error): ?>
                <div class="error" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            <?php endif; ?>
            <form method="post" action="" novalidate>
                <div class="field">
                    <label class="label" for="username">Username</label>
                    <div class="control">
                        <span class="icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input class="input" type="text" id="username" name="username" required autocomplete="username" placeholder="Masukkan username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="password">Password</label>
                    <div class="control">
                        <span class="icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input class="input" type="password" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                        <button type="button" class="toggle" aria-label="Tampilkan/Sembunyikan password" onclick="togglePass()">
                            <svg id="eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn">Masuk</button>
                <div class="helper">
                    <span id="desc" class="footer">Gunakan akun petugas untuk masuk</span>
                    <a class="link" href="/">Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePass(){
            const input = document.getElementById('password');
            const eye = document.getElementById('eye');
            const isPwd = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPwd ? 'text' : 'password');
            eye.innerHTML = isPwd
                ? '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.06-6.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.8 21.8 0 0 1-3.2 4.9"/><line x1="1" y1="1" x2="23" y2="23"></line>'
                : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/>';
        }
    </script>
</body>
</html>

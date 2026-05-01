<?php
// login.php
session_start();
// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; SiKeMas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-page">
    <div class="login-card">

        <!-- Logo & Brand -->
        <div class="login-logo">
            <div class="icon-wrap">
                <i class="bi bi-bank2"></i>
            </div>
            <h2>SiKeMas</h2>
            <p>Sistem Informasi Keuangan Masjid</p>
        </div>

        <!-- Error Alert -->
        <?php if ($error): ?>
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form action="process/login_proses.php" method="POST">
            <div style="margin-bottom:18px">
                <label class="form-label" style="font-weight:600;font-size:13px;display:block;margin-bottom:6px">
                    <i class="bi bi-envelope" style="color:#1a6b3c;margin-right:5px"></i>Email
                </label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="admin@gmail.com"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                    style="border:2px solid #e2e8f0;border-radius:8px;padding:11px 14px;font-size:14px;width:100%;outline:none;font-family:inherit;transition:border-color .2s"
                    onfocus="this.style.borderColor='#1a6b3c'"
                    onblur="this.style.borderColor='#e2e8f0'"
                >
            </div>

            <div style="margin-bottom:28px">
                <label class="form-label" style="font-weight:600;font-size:13px;display:block;margin-bottom:6px">
                    <i class="bi bi-lock" style="color:#1a6b3c;margin-right:5px"></i>Password
                </label>
                <div style="position:relative">
                    <input
                        type="password"
                        name="password"
                        id="passInput"
                        class="form-control"
                        placeholder="••••••••"
                        required
                        style="border:2px solid #e2e8f0;border-radius:8px;padding:11px 44px 11px 14px;font-size:14px;width:100%;outline:none;font-family:inherit;transition:border-color .2s"
                        onfocus="this.style.borderColor='#1a6b3c'"
                        onblur="this.style.borderColor='#e2e8f0'"
                    >
                    <button type="button" onclick="togglePass()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7a8d;font-size:15px" tabindex="-1">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" style="margin-bottom: 15px;">
                <i class="bi bi-box-arrow-in-right" style="margin-right:6px"></i>Masuk Sekarang
            </button>
            
            <p style="text-align:center;font-size:13px;color:#6b7a8d;margin:0">
                Belum punya akun? <a href="register.php" style="color:#1a6b3c;font-weight:600;text-decoration:none">Daftar di sini</a>
            </p>
        </form>

        <!-- Footer -->
        <p style="text-align:center;margin-top:24px;font-size:12px;color:#9ca3af">
            &copy; <?= date('Y') ?> SiKeMas &mdash; Tugas Akhir Mahasiswa
        </p>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('passInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>

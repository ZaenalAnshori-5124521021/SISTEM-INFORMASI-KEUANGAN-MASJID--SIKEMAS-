<?php
// process/login_proses.php
session_start();
require_once '../config/koneksi.php';

// Tolak akses langsung
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi input kosong
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Email dan password wajib diisi.';
    header('Location: ../login.php');
    exit;
}

// Validasi format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = 'Format email tidak valid.';
    header('Location: ../login.php');
    exit;
}

// Cari user di database (gunakan prepared statement)
$stmt = mysqli_prepare($koneksi,
    "SELECT id, nama, email, password, role FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);

// Verifikasi password
// password_verify() mengecek hash bcrypt
if ($user && password_verify($password, $user['password'])) {
    // Login berhasil
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_nama']  = $user['nama'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];
    header('Location: ../dashboard.php');
    exit;
} else {
    // Login gagal
    $_SESSION['login_error'] = 'Email atau password salah. Silakan coba lagi.';
    header('Location: ../login.php');
    exit;
}
?>

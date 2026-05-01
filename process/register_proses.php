<?php
// process/register_proses.php
session_start();
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = trim($_POST['nama']);
    $email    = trim($_POST['email']);
    $role     = trim($_POST['role']);
    $password = $_POST['password'];

    // Validasi input kosong
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['register_error'] = "Semua field harus diisi.";
        header('Location: ../register.php');
        exit;
    }

    // Validasi email exist
    $stmt = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email sudah terdaftar. Silakan gunakan email lain.";
        $stmt->close();
        header('Location: ../register.php');
        exit;
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert ke database
    $stmt = $koneksi->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Pendaftaran berhasil! Silakan login.";
        header('Location: ../register.php');
    } else {
        $_SESSION['register_error'] = "Terjadi kesalahan sistem. Pendaftaran gagal.";
        header('Location: ../register.php');
    }
    
    $stmt->close();
} else {
    header('Location: ../register.php');
    exit;
}
?>

<?php
// process/tambah_pengeluaran.php
session_start();
require_once '../config/koneksi.php';
cekLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pengeluaran.php');
    exit;
}

$tanggal    = trim($_POST['tanggal']    ?? '');
$jumlah     = trim($_POST['jumlah']     ?? '');
$keterangan = trim($_POST['keterangan'] ?? '');

// Validasi
if (empty($tanggal) || empty($jumlah) || empty($keterangan)) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header('Location: ../pengeluaran.php');
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $_SESSION['error'] = 'Format tanggal tidak valid.';
    header('Location: ../pengeluaran.php');
    exit;
}

$jumlah = (float)$jumlah;
if ($jumlah <= 0) {
    $_SESSION['error'] = 'Jumlah harus lebih dari 0.';
    header('Location: ../pengeluaran.php');
    exit;
}

// Insert
$stmt = mysqli_prepare($koneksi,
    "INSERT INTO pengeluaran (tanggal, jumlah, keterangan) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sds', $tanggal, $jumlah, $keterangan);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['sukses'] = 'Data pengeluaran berhasil ditambahkan.';
} else {
    $_SESSION['error'] = 'Gagal menyimpan data. Silakan coba lagi.';
}

header('Location: ../pengeluaran.php');
exit;
?>

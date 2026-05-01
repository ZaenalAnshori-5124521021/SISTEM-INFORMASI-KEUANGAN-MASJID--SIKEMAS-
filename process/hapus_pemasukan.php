<?php
// process/hapus_pemasukan.php
session_start();
require_once '../config/koneksi.php';
cekLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'ID tidak valid.';
    header('Location: ../pemasukan.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, "DELETE FROM pemasukan WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
    $_SESSION['sukses'] = 'Data pemasukan berhasil dihapus.';
} else {
    $_SESSION['error'] = 'Data tidak ditemukan atau gagal dihapus.';
}

header('Location: ../pemasukan.php');
exit;
?>

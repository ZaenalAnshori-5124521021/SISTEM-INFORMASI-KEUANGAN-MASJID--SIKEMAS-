<?php
// ============================================
// config/koneksi.php
// Konfigurasi koneksi database MySQL
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Sesuaikan jika berbeda
define('DB_PASS', '');            // Sesuaikan jika ada password
define('DB_NAME', 'masjid_keuangan');
define('APP_NAME', 'SiKeMas');
define('APP_FULL', 'Sistem Informasi Keuangan Masjid');

// Membuat koneksi menggunakan MySQLi
$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (!$koneksi) {
    die('<div style="font-family:sans-serif;padding:40px;background:#fff3cd;border:1px solid #ffc107;border-radius:8px;margin:20px;">
        <h3 style="color:#856404;">⚠️ Gagal Terhubung ke Database</h3>
        <p>Error: ' . mysqli_connect_error() . '</p>
        <p>Pastikan:</p>
        <ul>
            <li>XAMPP sudah berjalan (Apache & MySQL)</li>
            <li>Database <strong>masjid_keuangan</strong> sudah dibuat</li>
            <li>Konfigurasi di <code>config/koneksi.php</code> sudah benar</li>
        </ul>
    </div>');
}

// Set charset UTF-8
mysqli_set_charset($koneksi, 'utf8mb4');

// Fungsi helper: format rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi helper: format tanggal Indonesia
function tgl_indo($tanggal) {
    $bulan = [
        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember'
    ];
    $d = explode('-', $tanggal);
    return $d[2] . ' ' . $bulan[$d[1]] . ' ' . $d[0];
}

// Fungsi helper: cek login
function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>

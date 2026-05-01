<?php
// dashboard.php
session_start();
require_once 'config/koneksi.php';
cekLogin();

$halaman_aktif = 'dashboard';

// ── Hitung Total ─────────────────────────
$q_masuk   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah),0) AS total FROM pemasukan"));
$q_keluar  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(jumlah),0) AS total FROM pengeluaran"));
$total_masuk  = $q_masuk['total'];
$total_keluar = $q_keluar['total'];
$saldo        = $total_masuk - $total_keluar;

// ── Data Grafik 6 Bulan Terakhir ──────────
$labels     = [];
$data_masuk = [];
$data_keluar = [];

for ($i = 5; $i >= 0; $i--) {
    $bln   = date('Y-m', strtotime("-$i months"));
    $nama  = date('M Y', strtotime("-$i months"));
    $labels[] = $nama;

    $rm = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total FROM pemasukan
         WHERE DATE_FORMAT(tanggal,'%Y-%m') = '$bln'"));
    $rk = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT COALESCE(SUM(jumlah),0) AS total FROM pengeluaran
         WHERE DATE_FORMAT(tanggal,'%Y-%m') = '$bln'"));
    $data_masuk[]  = (int)$rm['total'];
    $data_keluar[] = (int)$rk['total'];
}

// ── 5 Transaksi Terakhir ─────────────────
$q_trx = mysqli_query($koneksi, "
    (SELECT tanggal, jumlah, keterangan, 'masuk' AS jenis FROM pemasukan)
    UNION ALL
    (SELECT tanggal, jumlah, keterangan, 'keluar' AS jenis FROM pengeluaran)
    ORDER BY tanggal DESC, jenis ASC
    LIMIT 8
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard &mdash; SiKeMas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include '_sidebar.php'; ?>

<div class="main-wrapper">

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
            <div>
                <div class="page-title">Dashboard</div>
                <div class="page-bread">Selamat datang, <?= htmlspecialchars($_SESSION['user_nama']) ?></div>
            </div>
        </div>
        <div class="topbar-right">
            <span style="font-size:12px;color:#6b7a8d">
                <i class="bi bi-calendar3" style="margin-right:5px"></i>
                <?= date('d F Y') ?>
            </span>
        </div>
    </header>

    <!-- Content -->
    <main class="page-content">

        <!-- ── Stat Cards ─────────────────── -->
        <div class="stats-grid">

            <div class="stat-card green">
                <div class="stat-icon green"><i class="bi bi-wallet2"></i></div>
                <div class="stat-value"><?= rupiah($saldo) ?></div>
                <div class="stat-label">Total Saldo Kas</div>
                <div class="stat-trend <?= $saldo >= 0 ? 'up' : 'down' ?>">
                    <i class="bi bi-<?= $saldo >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= $saldo >= 0 ? 'Saldo Positif' : 'Defisit' ?>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-icon blue"><i class="bi bi-arrow-down-circle"></i></div>
                <div class="stat-value"><?= rupiah($total_masuk) ?></div>
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-trend up">
                    <i class="bi bi-arrow-up"></i> Total Masuk
                </div>
            </div>

            <div class="stat-card red">
                <div class="stat-icon red"><i class="bi bi-arrow-up-circle"></i></div>
                <div class="stat-value"><?= rupiah($total_keluar) ?></div>
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-trend down">
                    <i class="bi bi-arrow-down"></i> Total Keluar
                </div>
            </div>

        </div>

        <!-- ── Grafik & Transaksi ──────────── -->
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px" class="dash-grid">

            <!-- Chart -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon"><i class="bi bi-bar-chart-line"></i></div>
                        Grafik Keuangan 6 Bulan Terakhir
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="chart-container">
                        <canvas id="chartKeuangan"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon"><i class="bi bi-lightning"></i></div>
                        Aksi Cepat
                    </div>
                </div>
                <div class="card-body-custom" style="display:flex;flex-direction:column;gap:12px">
                    <a href="pemasukan.php" style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#e8f5ee;border-radius:10px;border:1.5px solid rgba(26,107,60,0.15);transition:all .2s;text-decoration:none"
                       onmouseover="this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.transform=''">
                        <div style="width:40px;height:40px;background:#1a6b3c;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px;flex-shrink:0">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13.5px;color:#1a2332">Tambah Pemasukan</div>
                            <div style="font-size:11.5px;color:#6b7a8d">Catat infaq, donasi, zakat</div>
                        </div>
                    </a>

                    <a href="pengeluaran.php" style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#fef2f2;border-radius:10px;border:1.5px solid rgba(239,68,68,0.15);transition:all .2s;text-decoration:none"
                       onmouseover="this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.transform=''">
                        <div style="width:40px;height:40px;background:#ef4444;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px;flex-shrink:0">
                            <i class="bi bi-dash-circle"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13.5px;color:#1a2332">Tambah Pengeluaran</div>
                            <div style="font-size:11.5px;color:#6b7a8d">Catat listrik, honor, dll</div>
                        </div>
                    </a>

                    <a href="laporan.php" style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#eff6ff;border-radius:10px;border:1.5px solid rgba(59,130,246,0.15);transition:all .2s;text-decoration:none"
                       onmouseover="this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.transform=''">
                        <div style="width:40px;height:40px;background:#3b82f6;border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px;flex-shrink:0">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13.5px;color:#1a2332">Lihat Laporan</div>
                            <div style="font-size:11.5px;color:#6b7a8d">Rekap semua transaksi</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- ── Transaksi Terakhir ─────────── -->
        <div class="content-card">
            <div class="card-header-custom">
                <div class="card-title-custom">
                    <div class="card-title-icon"><i class="bi bi-clock-history"></i></div>
                    Transaksi Terakhir
                </div>
                <a href="laporan.php" style="font-size:12.5px;color:#1a6b3c;font-weight:600">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div style="overflow-x:auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jenis</th>
                            <th style="text-align:right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($q_trx) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($q_trx)): ?>
                            <tr>
                                <td class="muted"><?= tgl_indo($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td>
                                    <?php if ($row['jenis'] === 'masuk'): ?>
                                        <span class="badge-in"><i class="bi bi-arrow-down-short"></i> Pemasukan</span>
                                    <?php else: ?>
                                        <span class="badge-out"><i class="bi bi-arrow-up-short"></i> Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;font-weight:700;color:<?= $row['jenis']==='masuk' ? '#1a6b3c' : '#dc2626' ?>">
                                    <?= $row['jenis']==='masuk' ? '+' : '-' ?><?= rupiah($row['jumlah']) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>Belum ada transaksi</p>
                                </div>
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
initDashboardChart(
    <?= json_encode($labels) ?>,
    <?= json_encode($data_masuk) ?>,
    <?= json_encode($data_keluar) ?>
);
</script>

<style>
@media(max-width:992px){
    .dash-grid { grid-template-columns: 1fr !important; }
}
</style>

</body>
</html>

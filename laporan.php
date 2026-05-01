<?php
// laporan.php
session_start();
require_once 'config/koneksi.php';
cekLogin();

$halaman_aktif = 'laporan';

// ── Filter Bulan & Tahun ─────────────────
$filter_bln = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? $_GET['bulan'] : '';
$filter_thn = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : '';

// Bangun WHERE clause
$where_masuk  = "1=1";
$where_keluar = "1=1";

if ($filter_thn) {
    $where_masuk  .= " AND YEAR(tanggal) = $filter_thn";
    $where_keluar .= " AND YEAR(tanggal) = $filter_thn";
}
if ($filter_bln) {
    $where_masuk  .= " AND MONTH(tanggal) = " . (int)$filter_bln;
    $where_keluar .= " AND MONTH(tanggal) = " . (int)$filter_bln;
}

// ── Ambil Data ───────────────────────────
$q_masuk  = mysqli_query($koneksi,
    "SELECT *, 'masuk' AS jenis FROM pemasukan WHERE $where_masuk");
$q_keluar = mysqli_query($koneksi,
    "SELECT *, 'keluar' AS jenis FROM pengeluaran WHERE $where_keluar");

// ── Total ────────────────────────────────
$t_masuk  = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COALESCE(SUM(jumlah),0) AS total FROM pemasukan WHERE $where_masuk"));
$t_keluar = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COALESCE(SUM(jumlah),0) AS total FROM pengeluaran WHERE $where_keluar"));
$total_masuk  = $t_masuk['total'];
$total_keluar = $t_keluar['total'];
$saldo        = $total_masuk - $total_keluar;

// ── Gabung & Urutkan ─────────────────────
$semua = [];
while ($r = mysqli_fetch_assoc($q_masuk))  $semua[] = $r;
while ($r = mysqli_fetch_assoc($q_keluar)) $semua[] = $r;
usort($semua, function($a, $b) {
    $cmp = strcmp($b['tanggal'], $a['tanggal']);
    return $cmp !== 0 ? $cmp : $b['id'] - $a['id'];
});

// Daftar tahun untuk filter
$years = mysqli_query($koneksi,
    "SELECT DISTINCT YEAR(tanggal) AS thn FROM (
        SELECT tanggal FROM pemasukan
        UNION ALL
        SELECT tanggal FROM pengeluaran
     ) t ORDER BY thn DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan &mdash; SiKeMas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<?php include '_sidebar.php'; ?>

<div class="main-wrapper">

    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
            <div>
                <div class="page-title">Laporan Keuangan</div>
                <div class="page-bread">Dashboard / Laporan</div>
            </div>
        </div>
        <div class="topbar-right">
            <button class="btn-topbar btn-success-solid" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </header>

    <main class="page-content">

        <!-- ── Summary Banner ───────────── -->
        <div class="summary-box">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px">
                <div>
                    <h4><i class="bi bi-bank2" style="margin-right:6px"></i>Laporan Keuangan Masjid</h4>
                    <div class="value"><?= rupiah($saldo) ?></div>
                    <div style="font-size:13px;opacity:.65;margin-top:6px">
                        Saldo Kas <?= ($filter_bln || $filter_thn) ? 'Periode yang Dipilih' : 'Keseluruhan' ?>
                    </div>
                </div>
                <div style="opacity:.7;font-size:13px;text-align:right">
                    Dicetak: <?= date('d F Y H:i') ?><br>
                    Oleh: <?= htmlspecialchars($_SESSION['user_nama']) ?>
                </div>
            </div>
        </div>

        <!-- ── Summary Row ───────────────── -->
        <div class="summary-row">
            <div class="summary-item">
                <div class="label"><i class="bi bi-arrow-down-circle"></i> Total Pemasukan</div>
                <div class="val green"><?= rupiah($total_masuk) ?></div>
            </div>
            <div class="summary-item">
                <div class="label"><i class="bi bi-arrow-up-circle"></i> Total Pengeluaran</div>
                <div class="val red"><?= rupiah($total_keluar) ?></div>
            </div>
            <div class="summary-item">
                <div class="label"><i class="bi bi-wallet2"></i> Saldo Akhir</div>
                <div class="val <?= $saldo >= 0 ? 'green' : 'red' ?>"><?= rupiah($saldo) ?></div>
            </div>
        </div>

        <!-- ── Filter & Grafik ────────────── -->
        <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:20px;margin-bottom:24px" class="lap-grid">

            <!-- Filter -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon"><i class="bi bi-funnel"></i></div>
                        Filter Laporan
                    </div>
                </div>
                <div class="card-body-custom">
                    <form method="GET">
                        <div class="form-group-custom">
                            <label class="form-label-custom">Bulan</label>
                            <select name="bulan" class="form-control-custom">
                                <option value="">-- Semua Bulan --</option>
                                <?php
                                $bulan_arr = ['Januari','Februari','Maret','April','Mei','Juni',
                                              'Juli','Agustus','September','Oktober','November','Desember'];
                                foreach ($bulan_arr as $i => $b): ?>
                                <option value="<?= $i+1 ?>" <?= $filter_bln == ($i+1) ? 'selected' : '' ?>>
                                    <?= $b ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom">Tahun</label>
                            <select name="tahun" class="form-control-custom">
                                <option value="">-- Semua Tahun --</option>
                                <?php
                                mysqli_data_seek($years, 0);
                                while ($yr = mysqli_fetch_assoc($years)): ?>
                                <option value="<?= $yr['thn'] ?>" <?= $filter_thn == $yr['thn'] ? 'selected' : '' ?>>
                                    <?= $yr['thn'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div style="display:flex;gap:10px;margin-top:20px">
                            <button type="submit" class="btn-primary-custom">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="laporan.php" class="btn-secondary-custom">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Donut Chart -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon"><i class="bi bi-pie-chart"></i></div>
                        Proporsi Keuangan
                    </div>
                </div>
                <div class="card-body-custom">
                    <div class="chart-container" style="height:230px">
                        <canvas id="chartDonut"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Tabel Semua Transaksi ───────── -->
        <div class="content-card">
            <div class="card-header-custom">
                <div class="card-title-custom">
                    <div class="card-title-icon"><i class="bi bi-list-ul"></i></div>
                    Rincian Transaksi
                    <span style="background:#e8f5ee;color:#1a6b3c;padding:2px 10px;border-radius:20px;font-size:11.5px;font-weight:700">
                        <?= count($semua) ?> data
                    </span>
                </div>
            </div>
            <div style="overflow-x:auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th style="text-align:center">Jenis</th>
                            <th style="text-align:right">Pemasukan</th>
                            <th style="text-align:right">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($semua) > 0):
                            $no = 1;
                            foreach ($semua as $row): ?>
                        <tr>
                            <td class="muted"><?= $no++ ?></td>
                            <td class="muted" style="white-space:nowrap"><?= tgl_indo($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td style="text-align:center">
                                <?php if ($row['jenis'] === 'masuk'): ?>
                                    <span class="badge-in"><i class="bi bi-arrow-down-short"></i> Masuk</span>
                                <?php else: ?>
                                    <span class="badge-out"><i class="bi bi-arrow-up-short"></i> Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;font-weight:700;color:#1a6b3c">
                                <?= $row['jenis']==='masuk' ? rupiah($row['jumlah']) : '' ?>
                            </td>
                            <td style="text-align:right;font-weight:700;color:#dc2626">
                                <?= $row['jenis']==='keluar' ? rupiah($row['jumlah']) : '' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Row Total -->
                        <tr style="background:#f4f7f6;font-weight:800;border-top:2px solid #e2e8f0">
                            <td colspan="4" style="text-align:right;padding:14px 16px;font-size:13px;color:#1a2332">
                                <strong>TOTAL</strong>
                            </td>
                            <td style="text-align:right;color:#1a6b3c;padding:14px 16px"><?= rupiah($total_masuk) ?></td>
                            <td style="text-align:right;color:#dc2626;padding:14px 16px"><?= rupiah($total_keluar) ?></td>
                        </tr>
                        <tr style="background:#0f3d23">
                            <td colspan="4" style="text-align:right;padding:14px 16px;font-size:13.5px;color:rgba(255,255,255,0.8)">
                                <strong>SALDO AKHIR</strong>
                            </td>
                            <td colspan="2" style="text-align:right;color:white;font-size:16px;font-weight:900;padding:14px 16px">
                                <?= rupiah($saldo) ?>
                            </td>
                        </tr>

                        <?php else: ?>
                        <tr><td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Tidak ada data untuk periode yang dipilih.</p>
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
initDonutChart(<?= $total_masuk ?>, <?= $total_keluar ?>);
</script>
<style>
@media(max-width:992px){
    .lap-grid { grid-template-columns: 1fr !important; }
}
</style>
</body>
</html>

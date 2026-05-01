<?php
// pemasukan.php
session_start();
require_once 'config/koneksi.php';
cekLogin();

$halaman_aktif = 'pemasukan';

// Ambil pesan dari session
$sukses = $_SESSION['sukses'] ?? '';
$error  = $_SESSION['error']  ?? '';
unset($_SESSION['sukses'], $_SESSION['error']);

// Ambil semua data pemasukan (terbaru dulu)
$data = mysqli_query($koneksi,
    "SELECT * FROM pemasukan ORDER BY tanggal DESC, id DESC");

// Total
$q_total = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COALESCE(SUM(jumlah),0) AS total, COUNT(*) AS jml FROM pemasukan"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemasukan &mdash; SiKeMas</title>
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
                <div class="page-title">Manajemen Pemasukan</div>
                <div class="page-bread">Dashboard / Pemasukan</div>
            </div>
        </div>
    </header>

    <main class="page-content">

        <!-- Alert -->
        <?php if ($sukses): ?>
        <div class="alert-success-custom">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($sukses) ?>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-danger-custom">
            <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:1fr 1.8fr;gap:24px;align-items:start" class="pemas-grid">

            <!-- ── Form Tambah ──────────────── -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon" style="background:#e8f5ee;color:#1a6b3c">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        Tambah Pemasukan
                    </div>
                </div>
                <div class="card-body-custom">
                    <form action="process/tambah_pemasukan.php" method="POST">

                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-calendar3" style="color:#1a6b3c;margin-right:4px"></i>
                                Tanggal
                            </label>
                            <input type="date" name="tanggal" id="tanggal"
                                   class="form-control-custom" required>
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-cash-coin" style="color:#1a6b3c;margin-right:4px"></i>
                                Jumlah (Rp)
                            </label>
                            <div class="input-group-custom">
                                <span class="input-prefix">Rp</span>
                                <input type="number" name="jumlah" id="jumlah"
                                       class="form-control-custom"
                                       placeholder="500000"
                                       min="1" required>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-card-text" style="color:#1a6b3c;margin-right:4px"></i>
                                Keterangan
                            </label>
                            <input type="text" name="keterangan"
                                   class="form-control-custom"
                                   placeholder="Contoh: Infaq Jumat, Donasi, Zakat..."
                                   required maxlength="255">
                        </div>

                        <div style="display:flex;gap:10px;margin-top:24px">
                            <button type="submit" class="btn-primary-custom">
                                <i class="bi bi-plus-lg"></i> Simpan Data
                            </button>
                            <button type="reset" class="btn-secondary-custom">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ── Tabel Data ───────────────── -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon" style="background:#e8f5ee;color:#1a6b3c">
                            <i class="bi bi-table"></i>
                        </div>
                        Data Pemasukan
                    </div>
                    <div style="display:flex;gap:12px;align-items:center">
                        <div style="text-align:right">
                            <div style="font-size:11.5px;color:#6b7a8d"><?= $q_total['jml'] ?> transaksi</div>
                            <div style="font-size:14px;font-weight:800;color:#1a6b3c"><?= rupiah($q_total['total']) ?></div>
                        </div>
                    </div>
                </div>
                <div style="overflow-x:auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th style="text-align:right">Jumlah</th>
                                <th style="text-align:center;width:80px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($data) > 0):
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td class="muted"><?= $no++ ?></td>
                                <td class="muted" style="white-space:nowrap"><?= tgl_indo($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td style="text-align:right;font-weight:700;color:#1a6b3c;white-space:nowrap">
                                    <?= rupiah($row['jumlah']) ?>
                                </td>
                                <td style="text-align:center">
                                    <button class="btn-danger-custom"
                                        onclick="confirmDelete('process/hapus_pemasukan.php?id=<?= $row['id'] ?>', '<?= htmlspecialchars(addslashes($row['keterangan'])) ?>')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>Belum ada data pemasukan.<br>Gunakan form di samping untuk menambah.</p>
                                </div>
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>

<script src="assets/js/script.js"></script>
<style>
@media(max-width:992px){
    .pemas-grid { grid-template-columns: 1fr !important; }
}
</style>
</body>
</html>

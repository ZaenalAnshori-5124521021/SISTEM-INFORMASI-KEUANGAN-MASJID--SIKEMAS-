<?php
// pengeluaran.php
session_start();
require_once 'config/koneksi.php';
cekLogin();

$halaman_aktif = 'pengeluaran';

$sukses = $_SESSION['sukses'] ?? '';
$error  = $_SESSION['error']  ?? '';
unset($_SESSION['sukses'], $_SESSION['error']);

$data = mysqli_query($koneksi,
    "SELECT * FROM pengeluaran ORDER BY tanggal DESC, id DESC");

$q_total = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COALESCE(SUM(jumlah),0) AS total, COUNT(*) AS jml FROM pengeluaran"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran &mdash; SiKeMas</title>
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
                <div class="page-title">Manajemen Pengeluaran</div>
                <div class="page-bread">Dashboard / Pengeluaran</div>
            </div>
        </div>
    </header>

    <main class="page-content">

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

        <div style="display:grid;grid-template-columns:1fr 1.8fr;gap:24px;align-items:start" class="keluar-grid">

            <!-- Form Tambah -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon" style="background:#fef2f2;color:#dc2626">
                            <i class="bi bi-dash-circle"></i>
                        </div>
                        Tambah Pengeluaran
                    </div>
                </div>
                <div class="card-body-custom">
                    <form action="process/tambah_pengeluaran.php" method="POST">

                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-calendar3" style="color:#dc2626;margin-right:4px"></i>
                                Tanggal
                            </label>
                            <input type="date" name="tanggal" id="tanggal"
                                   class="form-control-custom" required
                                   style="--focus-color:#dc2626">
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-cash-coin" style="color:#dc2626;margin-right:4px"></i>
                                Jumlah (Rp)
                            </label>
                            <div class="input-group-custom">
                                <span class="input-prefix" style="background:#fef2f2;color:#dc2626;border-color:#fecaca">Rp</span>
                                <input type="number" name="jumlah" id="jumlah"
                                       class="form-control-custom"
                                       placeholder="500000"
                                       min="1" required
                                       style="border-left-color:#fecaca">
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="form-label-custom">
                                <i class="bi bi-card-text" style="color:#dc2626;margin-right:4px"></i>
                                Keterangan
                            </label>
                            <input type="text" name="keterangan"
                                   class="form-control-custom"
                                   placeholder="Contoh: Tagihan Listrik, Honor Imam..."
                                   required maxlength="255">
                        </div>

                        <div style="display:flex;gap:10px;margin-top:24px">
                            <button type="submit"
                                style="background:linear-gradient(135deg,#dc2626,#ef4444);color:white;border:none;padding:11px 22px;border-radius:8px;font-size:13.5px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 3px 10px rgba(220,38,38,0.3);transition:all .2s;font-family:inherit"
                                onmouseover="this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.transform=''">
                                <i class="bi bi-plus-lg"></i> Simpan Data
                            </button>
                            <button type="reset" class="btn-secondary-custom">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="content-card">
                <div class="card-header-custom">
                    <div class="card-title-custom">
                        <div class="card-title-icon" style="background:#fef2f2;color:#dc2626">
                            <i class="bi bi-table"></i>
                        </div>
                        Data Pengeluaran
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:11.5px;color:#6b7a8d"><?= $q_total['jml'] ?> transaksi</div>
                        <div style="font-size:14px;font-weight:800;color:#dc2626"><?= rupiah($q_total['total']) ?></div>
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
                                <td style="text-align:right;font-weight:700;color:#dc2626;white-space:nowrap">
                                    <?= rupiah($row['jumlah']) ?>
                                </td>
                                <td style="text-align:center">
                                    <button class="btn-danger-custom"
                                        onclick="confirmDelete('process/hapus_pengeluaran.php?id=<?= $row['id'] ?>', '<?= htmlspecialchars(addslashes($row['keterangan'])) ?>')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>Belum ada data pengeluaran.<br>Gunakan form di samping untuk menambah.</p>
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
    .keluar-grid { grid-template-columns: 1fr !important; }
}
</style>
</body>
</html>

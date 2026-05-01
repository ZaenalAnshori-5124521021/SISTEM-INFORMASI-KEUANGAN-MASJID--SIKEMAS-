<?php
// _sidebar.php — Komponen sidebar (diinclude di setiap halaman)
// Variabel $halaman_aktif harus sudah diset sebelum include ini
$halaman_aktif = $halaman_aktif ?? 'dashboard';
?>
<!-- Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">

    <!-- Brand Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-bank2"></i></div>
            <div class="brand-text">
                <h5>SiKeMas</h5>
                <small>Keuangan Masjid</small>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>

        <a href="dashboard.php" class="nav-link-item <?= $halaman_aktif==='dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="nav-section-label" style="margin-top:8px">Keuangan</div>

        <a href="pemasukan.php" class="nav-link-item <?= $halaman_aktif==='pemasukan' ? 'active' : '' ?>">
            <i class="bi bi-arrow-down-circle"></i> Pemasukan
        </a>

        <a href="pengeluaran.php" class="nav-link-item <?= $halaman_aktif==='pengeluaran' ? 'active' : '' ?>">
            <i class="bi bi-arrow-up-circle"></i> Pengeluaran
        </a>

        <div class="nav-section-label" style="margin-top:8px">Laporan</div>

        <a href="laporan.php" class="nav-link-item <?= $halaman_aktif==='laporan' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Keuangan
        </a>

    </nav>

    <!-- Footer User Info -->
    <div class="sidebar-footer">
        <div class="user-info-sidebar">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_nama'] ?? 'A', 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['user_nama'] ?? 'Admin') ?></div>
                <div class="user-role"><?= ucfirst($_SESSION['user_role'] ?? 'admin') ?></div>
            </div>
        </div>
        <a href="logout.php" style="display:flex;align-items:center;gap:8px;padding:9px 12px;margin-top:8px;border-radius:8px;color:rgba(255,255,255,0.5);font-size:13px;font-weight:500;transition:all .2s"
           onmouseover="this.style.background='rgba(239,68,68,0.15)';this.style.color='#fca5a5'"
           onmouseout="this.style.background='';this.style.color='rgba(255,255,255,0.5)'">
            <i class="bi bi-box-arrow-left"></i> Keluar
        </a>
    </div>

</aside>

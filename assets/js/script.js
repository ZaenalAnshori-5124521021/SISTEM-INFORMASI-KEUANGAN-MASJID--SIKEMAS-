// ============================================
// assets/js/script.js
// Sistem Informasi Keuangan Masjid
// ============================================

// ── Sidebar Toggle (Mobile) ──────────────
const sidebar        = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const sidebarToggle  = document.getElementById('sidebarToggle');

function openSidebar() {
    if (sidebar) sidebar.classList.add('open');
    if (sidebarOverlay) sidebarOverlay.classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (sidebarOverlay) sidebarOverlay.classList.remove('show');
    document.body.style.overflow = '';
}

if (sidebarToggle)  sidebarToggle.addEventListener('click', openSidebar);
if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

// ── Auto-dismiss Alerts ──────────────────
const alerts = document.querySelectorAll('.alert-success-custom, .alert-danger-custom');
alerts.forEach(function(alert) {
    setTimeout(function() {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity    = '0';
        setTimeout(function() { alert.style.display = 'none'; }, 500);
    }, 4000);
});

// ── Format Currency Input ────────────────
const jumlahInput = document.getElementById('jumlah');
if (jumlahInput) {
    jumlahInput.addEventListener('input', function() {
        let val = this.value.replace(/\D/g, '');
        this.value = val;
    });
}

// ── Confirm Delete ───────────────────────
function confirmDelete(url, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?\n\n"' + nama + '"')) {
        window.location.href = url;
    }
}

// ── Chart.js: Dashboard ──────────────────
function initDashboardChart(labels, pemasukanData, pengeluaranData) {
    const ctx = document.getElementById('chartKeuangan');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: pemasukanData,
                    borderColor: '#1a6b3c',
                    backgroundColor: 'rgba(26,107,60,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#1a6b3c',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Pengeluaran',
                    data: pengeluaranData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.06)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' },
                        color: '#1a2332',
                        usePointStyle: true,
                        pointStyleWidth: 16,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#0f3d23',
                    titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '700' },
                    bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.dataset.label + ': Rp ' +
                                new Intl.NumberFormat('id-ID').format(ctx.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                        color: '#6b7a8d'
                    }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                        color: '#6b7a8d',
                        callback: function(val) {
                            if (val >= 1000000) return 'Rp ' + (val/1000000).toFixed(1) + 'jt';
                            if (val >= 1000)    return 'Rp ' + (val/1000).toFixed(0) + 'rb';
                            return 'Rp ' + val;
                        }
                    }
                }
            }
        }
    });
}

// ── Chart.js: Donut (Laporan) ────────────
function initDonutChart(masuk, keluar) {
    const ctx = document.getElementById('chartDonut');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pemasukan', 'Pengeluaran'],
            datasets: [{
                data: [masuk, keluar],
                backgroundColor: ['#1a6b3c', '#ef4444'],
                borderColor: ['#fff', '#fff'],
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' },
                        color: '#1a2332',
                        usePointStyle: true,
                        padding: 16
                    }
                },
                tooltip: {
                    backgroundColor: '#0f3d23',
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.label + ': Rp ' +
                                new Intl.NumberFormat('id-ID').format(ctx.raw);
                        }
                    }
                }
            }
        }
    });
}

// ── Set Today as Default Date ─────────────
const tglInput = document.getElementById('tanggal');
if (tglInput && !tglInput.value) {
    const today = new Date().toISOString().split('T')[0];
    tglInput.value = today;
}

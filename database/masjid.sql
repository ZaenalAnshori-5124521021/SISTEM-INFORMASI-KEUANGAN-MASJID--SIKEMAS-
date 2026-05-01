-- ============================================
-- DATABASE: Sistem Keuangan Masjid
-- Dibuat untuk Tugas Akhir Mahasiswa
-- ============================================

CREATE DATABASE IF NOT EXISTS masjid_keuangan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE masjid_keuangan;

-- ============================================
-- TABEL: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100)  NOT NULL,
    email       VARCHAR(100)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('admin','bendahara') DEFAULT 'admin',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL: pemasukan
-- ============================================
CREATE TABLE IF NOT EXISTS pemasukan (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    tanggal     DATE          NOT NULL,
    jumlah      DECIMAL(15,2) NOT NULL,
    keterangan  VARCHAR(255)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL: pengeluaran
-- ============================================
CREATE TABLE IF NOT EXISTS pengeluaran (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    tanggal     DATE          NOT NULL,
    jumlah      DECIMAL(15,2) NOT NULL,
    keterangan  VARCHAR(255)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- DATA DEFAULT: Admin
-- Email    : admin@gmail.com
-- Password : 123456
-- ============================================
INSERT INTO users (nama, email, password, role) VALUES
('Administrator', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================
-- DATA CONTOH: Pemasukan
-- ============================================
INSERT INTO pemasukan (tanggal, jumlah, keterangan) VALUES
('2025-01-03', 2500000,  'Infaq Jumat Pertama Januari'),
('2025-01-10', 3100000,  'Infaq Jumat Kedua Januari'),
('2025-01-17', 2800000,  'Infaq Jumat Ketiga Januari'),
('2025-01-24', 3300000,  'Infaq Jumat Keempat Januari'),
('2025-02-07', 2700000,  'Infaq Jumat Pertama Februari'),
('2025-02-14', 4500000,  'Donasi Renovasi Masjid'),
('2025-02-21', 2900000,  'Infaq Jumat Ketiga Februari'),
('2025-03-07', 5000000,  'Zakat Mal Jamaah'),
('2025-03-14', 3200000,  'Infaq Jumat Maret'),
('2025-03-21', 7500000,  'Donasi Ramadhan'),
('2025-04-04', 6200000,  'Zakat Fitrah'),
('2025-04-11', 4100000,  'Infaq Idul Fitri'),
('2025-05-02', 2600000,  'Infaq Jumat Mei'),
('2025-05-09', 1800000,  'Kotak Amal Harian');

-- ============================================
-- DATA CONTOH: Pengeluaran
-- ============================================
INSERT INTO pengeluaran (tanggal, jumlah, keterangan) VALUES
('2025-01-05', 850000,   'Tagihan Listrik Januari'),
('2025-01-12', 300000,   'Pembelian Perlengkapan Kebersihan'),
('2025-01-19', 1200000,  'Honor Imam & Muadzin'),
('2025-02-02', 850000,   'Tagihan Listrik Februari'),
('2025-02-15', 2500000,  'Pembelian Cat Renovasi'),
('2025-02-22', 1200000,  'Honor Imam & Muadzin'),
('2025-03-01', 850000,   'Tagihan Listrik Maret'),
('2025-03-10', 500000,   'Konsumsi Pengajian Rutin'),
('2025-03-20', 1200000,  'Honor Imam & Muadzin'),
('2025-04-01', 850000,   'Tagihan Listrik April'),
('2025-04-08', 2000000,  'Konsumsi Buka Puasa Bersama'),
('2025-04-15', 1500000,  'Paket Sembako Fakir Miskin'),
('2025-05-03', 850000,   'Tagihan Listrik Mei'),
('2025-05-10', 750000,   'Perbaikan Sound System');

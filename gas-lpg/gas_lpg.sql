-- =====================================================
-- DATABASE: Gas LPG Delivery System
-- Dibuat untuk sistem pemesanan dan pengantaran gas LPG
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- Buat Database
-- =====================================================
CREATE DATABASE IF NOT EXISTS `gas_lpg`;
USE `gas_lpg`;

-- =====================================================
-- Tabel: user
-- Menyimpan data semua pengguna (Admin, Kurir, Pembeli)
-- =====================================================
CREATE TABLE `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nama_depan` varchar(100) NOT NULL COMMENT 'Nama depan pengguna',
  `nama_belakang` varchar(100) DEFAULT NULL COMMENT 'Nama belakang pengguna',
  `email` varchar(150) NOT NULL COMMENT 'Email untuk login',
  `password` varchar(255) NOT NULL COMMENT 'Password terenkripsi',
  `telepon` varchar(15) DEFAULT NULL COMMENT 'Nomor telepon',
  `alamat` text COMMENT 'Alamat default pengguna',
  `jk` enum('L','P') DEFAULT NULL COMMENT 'Jenis kelamin: L=Laki-laki, P=Perempuan',
  `role` enum('Admin','Agen','Kurir','Pembeli') NOT NULL DEFAULT 'Pembeli' COMMENT 'Role pengguna',
  `id_agen` int DEFAULT NULL COMMENT 'ID agen untuk admin (relasi admin-agen)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal registrasi',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `fk_user_agen` FOREIGN KEY (`id_agen`) REFERENCES `user` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Data Default User
-- Password: 123456 (untuk testing, nanti ganti dengan hash)
-- =====================================================
INSERT INTO `user` (`id_user`, `nama_depan`, `nama_belakang`, `email`, `password`, `telepon`, `alamat`, `jk`, `role`) VALUES
(1, 'Admin', 'Gas LPG', 'admin@gaslpg.com', '123456', '081234567890', 'Jl. Admin No. 1', 'L', 'Admin'),
(2, 'Agen', 'Gas LPG', 'agen@gaslpg.com', '123456', '081234567893', 'Jl. Agen No. 1', 'L', 'Agen'),
(3, 'Kurir', 'Satu', 'kurir@gaslpg.com', '123456', '081234567891', 'Jl. Kurir No. 1', 'L', 'Kurir'),
(4, 'Pembeli', 'Test', 'pembeli@gaslpg.com', '123456', '081234567892', 'Jl. Pembeli No. 1', 'P', 'Pembeli');

-- Update admin agar terhubung ke agen
UPDATE `user` SET `id_agen` = 2 WHERE `id_user` = 1;

-- =====================================================
-- Tabel: tb_produk
-- Menyimpan data produk gas LPG
-- =====================================================
CREATE TABLE `tb_produk` (
  `id_produk` int NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(100) NOT NULL COMMENT 'Nama produk gas',
  `deskripsi` text COMMENT 'Deskripsi produk',
  `harga` decimal(10,2) NOT NULL COMMENT 'Harga per unit',
  `stok` int NOT NULL DEFAULT 0 COMMENT 'Jumlah stok tersedia',
  `gambar` varchar(255) DEFAULT NULL COMMENT 'Nama file gambar produk',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif' COMMENT 'Status produk',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Data Default Produk
-- =====================================================
INSERT INTO `tb_produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `stok`, `gambar`, `status`) VALUES
(1, 'Gas LPG 3 Kg', 'Tabung gas LPG 3 kilogram (Elpiji Melon) untuk kebutuhan rumah tangga', 18000.00, 100, 'lpg_3kg.jpg', 'aktif');

-- =====================================================
-- Tabel: tb_stok_agen
-- Menyimpan stok gas milik agen
-- =====================================================
CREATE TABLE `tb_stok_agen` (
  `id_stok` int NOT NULL AUTO_INCREMENT,
  `id_agen` int NOT NULL COMMENT 'ID user agen',
  `id_produk` int NOT NULL COMMENT 'ID produk gas',
  `jumlah_stok` int NOT NULL DEFAULT 0 COMMENT 'Jumlah stok saat ini',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stok`),
  UNIQUE KEY `agen_produk` (`id_agen`, `id_produk`),
  KEY `id_agen` (`id_agen`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `tb_stok_agen_ibfk_1` FOREIGN KEY (`id_agen`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_stok_agen_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Data Default Stok Agen
-- =====================================================
INSERT INTO `tb_stok_agen` (`id_agen`, `id_produk`, `jumlah_stok`) VALUES
(2, 1, 500);

-- =====================================================
-- Tabel: tb_riwayat_stok
-- Mencatat riwayat pergerakan stok agen (masuk/keluar)
-- =====================================================
CREATE TABLE `tb_riwayat_stok` (
  `id_riwayat` int NOT NULL AUTO_INCREMENT,
  `id_agen` int NOT NULL COMMENT 'ID user agen',
  `id_produk` int NOT NULL COMMENT 'ID produk gas',
  `tipe` enum('masuk','keluar') NOT NULL COMMENT 'Tipe: masuk dari supplier, keluar ke admin',
  `jumlah` int NOT NULL COMMENT 'Jumlah stok',
  `stok_sebelum` int NOT NULL COMMENT 'Stok sebelum transaksi',
  `stok_sesudah` int NOT NULL COMMENT 'Stok sesudah transaksi',
  `keterangan` text COMMENT 'Keterangan tambahan',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_riwayat`),
  KEY `id_agen` (`id_agen`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `tb_riwayat_stok_ibfk_1` FOREIGN KEY (`id_agen`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_riwayat_stok_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Tabel: tb_permintaan_gas
-- Menyimpan permintaan gas dari admin ke agen
-- =====================================================
CREATE TABLE `tb_permintaan_gas` (
  `id_permintaan` int NOT NULL AUTO_INCREMENT,
  `kode_permintaan` varchar(20) NOT NULL COMMENT 'Kode unik permintaan',
  `id_admin` int NOT NULL COMMENT 'ID admin yang request',
  `id_agen` int NOT NULL COMMENT 'ID agen tujuan',
  `id_produk` int NOT NULL COMMENT 'ID produk gas',
  `jumlah` int NOT NULL COMMENT 'Jumlah yang diminta',
  `status` enum('menunggu','ditolak','selesai') DEFAULT 'menunggu' COMMENT 'Status permintaan',
  `catatan_admin` text COMMENT 'Catatan dari admin',
  `catatan_agen` text COMMENT 'Catatan/alasan dari agen',
  `waktu_permintaan` datetime DEFAULT CURRENT_TIMESTAMP,
  `waktu_respon` datetime DEFAULT NULL COMMENT 'Waktu agen merespon',
  `waktu_selesai` datetime DEFAULT NULL COMMENT 'Waktu distribusi selesai',
  PRIMARY KEY (`id_permintaan`),
  UNIQUE KEY `kode_permintaan` (`kode_permintaan`),
  KEY `id_admin` (`id_admin`),
  KEY `id_agen` (`id_agen`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `tb_permintaan_gas_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_permintaan_gas_ibfk_2` FOREIGN KEY (`id_agen`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_permintaan_gas_ibfk_3` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Tabel: tb_distribusi
-- Mencatat distribusi gas dari agen ke admin
-- =====================================================
CREATE TABLE `tb_distribusi` (
  `id_distribusi` int NOT NULL AUTO_INCREMENT,
  `id_permintaan` int DEFAULT NULL COMMENT 'ID permintaan terkait (jika ada)',
  `id_agen` int NOT NULL COMMENT 'ID agen pengirim',
  `id_admin` int NOT NULL COMMENT 'ID admin penerima',
  `id_produk` int NOT NULL COMMENT 'ID produk gas',
  `jumlah` int NOT NULL COMMENT 'Jumlah yang didistribusikan',
  `waktu_distribusi` datetime DEFAULT CURRENT_TIMESTAMP,
  `keterangan` text COMMENT 'Keterangan tambahan',
  PRIMARY KEY (`id_distribusi`),
  KEY `id_permintaan` (`id_permintaan`),
  KEY `id_agen` (`id_agen`),
  KEY `id_admin` (`id_admin`),
  KEY `id_produk` (`id_produk`),
  CONSTRAINT `tb_distribusi_ibfk_1` FOREIGN KEY (`id_permintaan`) REFERENCES `tb_permintaan_gas` (`id_permintaan`) ON DELETE SET NULL,
  CONSTRAINT `tb_distribusi_ibfk_2` FOREIGN KEY (`id_agen`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_distribusi_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_distribusi_ibfk_4` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Tabel: tb_pesanan
-- Menyimpan data pesanan dari pembeli
-- =====================================================
CREATE TABLE `tb_pesanan` (
  `id_pesanan` int NOT NULL AUTO_INCREMENT,
  `kode_pesanan` varchar(20) NOT NULL COMMENT 'Kode unik pesanan',
  `id_user` int NOT NULL COMMENT 'ID pembeli',
  `id_produk` int NOT NULL COMMENT 'ID produk yang dipesan',
  `jumlah` int NOT NULL DEFAULT 1 COMMENT 'Jumlah produk',
  `total_harga` decimal(12,2) NOT NULL COMMENT 'Total harga pesanan',
  `nama_depan` varchar(100) NOT NULL COMMENT 'Nama depan penerima',
  `nama_belakang` varchar(100) DEFAULT NULL COMMENT 'Nama belakang penerima',
  `telepon` varchar(15) NOT NULL COMMENT 'Telepon penerima',
  `alamat_pengantaran` text NOT NULL COMMENT 'Alamat lengkap pengantaran',
  `status` enum('pending','paid','confirmed','delivering','completed','cancelled','expired') DEFAULT 'pending' COMMENT 'Status pesanan',
  `id_kurir` int DEFAULT NULL COMMENT 'ID kurir yang mengantar',
  `waktu_pesan` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pesanan dibuat',
  `waktu_bayar` datetime DEFAULT NULL COMMENT 'Waktu pembayaran dilakukan',
  `waktu_konfirmasi` datetime DEFAULT NULL COMMENT 'Waktu admin konfirmasi',
  `waktu_antar` datetime DEFAULT NULL COMMENT 'Waktu kurir mulai antar',
  `waktu_selesai` datetime DEFAULT NULL COMMENT 'Waktu pesanan selesai',
  `catatan` text COMMENT 'Catatan tambahan dari pembeli',
  PRIMARY KEY (`id_pesanan`),
  UNIQUE KEY `kode_pesanan` (`kode_pesanan`),
  KEY `id_user` (`id_user`),
  KEY `id_produk` (`id_produk`),
  KEY `id_kurir` (`id_kurir`),
  CONSTRAINT `tb_pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `tb_pesanan_ibfk_3` FOREIGN KEY (`id_kurir`) REFERENCES `user` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Tabel: tb_pembayaran
-- Menyimpan data pembayaran pesanan
-- =====================================================
CREATE TABLE `tb_pembayaran` (
  `id_pembayaran` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL COMMENT 'ID pesanan terkait',
  `metode_pembayaran` varchar(50) NOT NULL COMMENT 'Metode: BCA, Mandiri, BNI, dll',
  `midtrans_transaction_id` varchar(100) DEFAULT NULL COMMENT 'Transaction ID dari Midtrans',
  `midtrans_order_id` varchar(100) DEFAULT NULL COMMENT 'Order ID untuk Midtrans',
  `status_pembayaran` enum('pending','success','failed','expired') DEFAULT 'pending' COMMENT 'Status pembayaran',
  `bukti_pembayaran` varchar(255) DEFAULT NULL COMMENT 'Nama file bukti transfer',
  `jumlah_bayar` decimal(12,2) NOT NULL COMMENT 'Jumlah yang dibayar',
  `waktu_pembayaran` datetime DEFAULT NULL COMMENT 'Waktu pembayaran berhasil',
  `waktu_expired` datetime DEFAULT NULL COMMENT 'Waktu kadaluarsa pembayaran',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pembayaran`),
  KEY `id_pesanan` (`id_pesanan`),
  CONSTRAINT `tb_pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `tb_pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Tabel: tb_notifikasi
-- Menyimpan notifikasi untuk admin dan kurir
-- =====================================================
CREATE TABLE `tb_notifikasi` (
  `id_notifikasi` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL COMMENT 'ID user penerima notifikasi',
  `id_pesanan` int DEFAULT NULL COMMENT 'ID pesanan terkait (opsional)',
  `judul` varchar(100) NOT NULL COMMENT 'Judul notifikasi',
  `pesan` text NOT NULL COMMENT 'Isi pesan notifikasi',
  `tipe` enum('pesanan_baru','pembayaran','konfirmasi','pengantaran','selesai') NOT NULL COMMENT 'Tipe notifikasi',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'Status sudah dibaca: 0=belum, 1=sudah',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notifikasi`),
  KEY `id_user` (`id_user`),
  KEY `id_pesanan` (`id_pesanan`),
  CONSTRAINT `tb_notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tb_notifikasi_ibfk_2` FOREIGN KEY (`id_pesanan`) REFERENCES `tb_pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- Trigger: Kurangi stok saat pesanan dibuat
-- =====================================================
DELIMITER $
CREATE TRIGGER `kurangi_stok` AFTER INSERT ON `tb_pesanan` 
FOR EACH ROW 
BEGIN
    UPDATE tb_produk SET stok = stok - NEW.jumlah WHERE id_produk = NEW.id_produk;
END$
DELIMITER ;

-- =====================================================
-- Trigger: Kembalikan stok saat pesanan dibatalkan/expired
-- =====================================================
DELIMITER $
CREATE TRIGGER `kembalikan_stok` AFTER UPDATE ON `tb_pesanan` 
FOR EACH ROW 
BEGIN
    IF (NEW.status = 'cancelled' OR NEW.status = 'expired') AND OLD.status != NEW.status THEN
        UPDATE tb_produk SET stok = stok + NEW.jumlah WHERE id_produk = NEW.id_produk;
    END IF;
END$
DELIMITER ;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

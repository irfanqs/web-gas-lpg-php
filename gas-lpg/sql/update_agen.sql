-- =====================================================
-- UPDATE DATABASE: Menambahkan Role Agen
-- Jalankan script ini setelah gas_lpg.sql
-- =====================================================

USE `gas_lpg`;

-- =====================================================
-- 1. Update ENUM role di tabel user
-- =====================================================
ALTER TABLE `user` 
MODIFY COLUMN `role` enum('Admin','Agen','Kurir','Pembeli') NOT NULL DEFAULT 'Pembeli' 
COMMENT 'Role pengguna';

-- =====================================================
-- 2. Tabel: tb_stok_agen
-- Menyimpan stok gas milik agen
-- =====================================================
CREATE TABLE IF NOT EXISTS `tb_stok_agen` (
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
-- 3. Tabel: tb_riwayat_stok
-- Mencatat riwayat pergerakan stok agen (masuk/keluar)
-- =====================================================
CREATE TABLE IF NOT EXISTS `tb_riwayat_stok` (
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
-- 4. Tabel: tb_permintaan_gas
-- Menyimpan permintaan gas dari admin ke agen
-- =====================================================
CREATE TABLE IF NOT EXISTS `tb_permintaan_gas` (
  `id_permintaan` int NOT NULL AUTO_INCREMENT,
  `kode_permintaan` varchar(20) NOT NULL COMMENT 'Kode unik permintaan',
  `id_admin` int NOT NULL COMMENT 'ID admin yang request',
  `id_agen` int NOT NULL COMMENT 'ID agen tujuan',
  `id_produk` int NOT NULL COMMENT 'ID produk gas',
  `jumlah` int NOT NULL COMMENT 'Jumlah yang diminta',
  `status` enum('menunggu','disetujui','ditolak','selesai') DEFAULT 'menunggu' COMMENT 'Status permintaan',
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
-- 5. Tabel: tb_distribusi
-- Mencatat distribusi gas dari agen ke admin
-- =====================================================
CREATE TABLE IF NOT EXISTS `tb_distribusi` (
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
-- 6. Tambah kolom id_agen di tabel user untuk admin
-- Menghubungkan admin dengan agen tertentu
-- =====================================================
ALTER TABLE `user` 
ADD COLUMN `id_agen` int DEFAULT NULL COMMENT 'ID agen untuk admin (relasi admin-agen)' AFTER `role`,
ADD CONSTRAINT `fk_user_agen` FOREIGN KEY (`id_agen`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;

-- =====================================================
-- 7. Insert user Agen default
-- =====================================================
INSERT INTO `user` (`nama_depan`, `nama_belakang`, `email`, `password`, `telepon`, `alamat`, `jk`, `role`) VALUES
('Agen', 'Gas LPG', 'agen@gaslpg.com', '123456', '081234567893', 'Jl. Agen No. 1', 'L', 'Agen');

-- =====================================================
-- 8. Update admin agar terhubung ke agen
-- =====================================================
UPDATE `user` SET `id_agen` = (SELECT id_user FROM (SELECT id_user FROM `user` WHERE role = 'Agen' LIMIT 1) AS temp) WHERE role = 'Admin';

-- =====================================================
-- 9. Insert stok awal untuk agen
-- =====================================================
INSERT INTO `tb_stok_agen` (`id_agen`, `id_produk`, `jumlah_stok`)
SELECT u.id_user, p.id_produk, 500
FROM `user` u, `tb_produk` p
WHERE u.role = 'Agen';

COMMIT;

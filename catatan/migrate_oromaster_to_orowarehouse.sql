-- =========================================================================
-- SQL Migration Script: Move Warehouse2 Tables from oromaster to orowarehouse
-- =========================================================================
-- This script exports all warehouse2 related tables from oromaster database
-- and imports them into the new orowarehouse database
-- =========================================================================

-- Step 1: Create tables in orowarehouse database if they don't exist
-- These CREATE TABLE statements are based on oromaster structure

USE orowarehouse;

-- Table 1: barang_masuk_pemasok
CREATE TABLE IF NOT EXISTS `barang_masuk_pemasok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_dokumen` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `nama_pemasok` varchar(255) DEFAULT NULL,
  `kode_barang` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `harga_satuan` decimal(15,2) DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `no_surat_jalan` varchar(255) DEFAULT NULL,
  `catatan` longtext,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanggal` (`tanggal`),
  KEY `status` (`status`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 2: barang_masuk_produksi
CREATE TABLE IF NOT EXISTS `barang_masuk_produksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_dokumen` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `no_spk` varchar(255) DEFAULT NULL,
  `nama_produksi` varchar(255) DEFAULT NULL,
  `kode_barang` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `jumlah_keluar` decimal(15,2) DEFAULT NULL,
  `jumlah_terima` decimal(15,2) DEFAULT NULL,
  `nama_penerima` varchar(255) DEFAULT NULL,
  `catatan` longtext,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanggal` (`tanggal`),
  KEY `no_spk` (`no_spk`),
  KEY `status` (`status`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 3: barang_keluar_pembeli
CREATE TABLE IF NOT EXISTS `barang_keluar_pembeli` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_dokumen` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `nama_pembeli` varchar(255) DEFAULT NULL,
  `kode_barang` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL,
  `harga_satuan` decimal(15,2) DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT NULL,
  `no_so` varchar(255) DEFAULT NULL,
  `no_invoice` varchar(255) DEFAULT NULL,
  `alamat_pengiriman` longtext,
  `catatan` longtext,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanggal` (`tanggal`),
  KEY `status` (`status`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 4: barang_masuk_dari_produksi
CREATE TABLE IF NOT EXISTS `barang_masuk_dari_produksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_dokumen` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `no_spk` varchar(255) DEFAULT NULL,
  `nama_produksi` varchar(255) DEFAULT NULL,
  `kode_barang` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `jumlah_produksi` decimal(15,2) DEFAULT NULL,
  `jumlah_reject` decimal(15,2) DEFAULT NULL,
  `jumlah_diterima` decimal(15,2) DEFAULT NULL,
  `nama_penerima` varchar(255) DEFAULT NULL,
  `catatan` longtext,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanggal` (`tanggal`),
  KEY `no_spk` (`no_spk`),
  KEY `status` (`status`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 5: orowarehouse
CREATE TABLE IF NOT EXISTS `orowarehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date DEFAULT NULL,
  `spk` varchar(255) DEFAULT NULL,
  `produk` varchar(255) DEFAULT NULL,
  `shift` int(2) DEFAULT NULL,
  `grup` varchar(255) DEFAULT NULL,
  `hasil_bagus` decimal(15,2) DEFAULT NULL,
  `hasil_reject` decimal(15,2) DEFAULT NULL,
  `material` longtext,
  `colorant` longtext,
  `packaging` longtext,
  `operator` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `catatan` longtext,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanggal` (`tanggal`),
  KEY `spk` (`spk`),
  KEY `status` (`status`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- Step 2: Copy existing data from oromaster to orowarehouse (if any)
-- =========================================================================
-- Uncomment these lines if you want to copy existing data from oromaster

-- INSERT INTO orowarehouse.barang_masuk_pemasok SELECT * FROM oromaster.barang_masuk_pemasok WHERE deleted_at IS NULL;
-- INSERT INTO orowarehouse.barang_masuk_produksi SELECT * FROM oromaster.barang_masuk_produksi WHERE deleted_at IS NULL;
-- INSERT INTO orowarehouse.barang_keluar_pembeli SELECT * FROM oromaster.barang_keluar_pembeli WHERE deleted_at IS NULL;
-- INSERT INTO orowarehouse.barang_masuk_dari_produksi SELECT * FROM oromaster.barang_masuk_dari_produksi WHERE deleted_at IS NULL;
-- INSERT INTO orowarehouse.orowarehouse SELECT * FROM oromaster.orowarehouse WHERE deleted_at IS NULL;

-- =========================================================================
-- Step 3: Verify tables created
-- =========================================================================
-- Show all tables in orowarehouse database
SHOW TABLES IN orowarehouse;

-- =========================================================================
-- Notes:
-- 1. Run this script in phpMyAdmin or MySQL client
-- 2. Make sure orowarehouse database already exists
-- 3. All tables use soft delete (deleted_at column)
-- 4. All tables have timestamps (created_at, updated_at)
-- 5. Uncomment INSERT statements if you want to migrate existing data
-- =========================================================================

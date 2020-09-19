-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.40-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for pajak2
DROP DATABASE IF EXISTS `pajak2`;
CREATE DATABASE IF NOT EXISTS `pajak2` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `pajak2`;

-- Dumping structure for table pajak2.client
DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(50) NOT NULL DEFAULT '',
  `kap_mesin_produksi` varchar(50) NOT NULL DEFAULT '',
  `satuan_kap_mesin_produksi` varchar(50) NOT NULL DEFAULT '',
  `kap_prod_produksi` varchar(50) NOT NULL DEFAULT '',
  `kap_prod_operasional` varchar(50) NOT NULL DEFAULT '',
  `kap_prod_hari_operasional` varchar(50) NOT NULL DEFAULT '',
  `kap_prod_jumlah_produksi` varchar(50) NOT NULL DEFAULT '',
  `water_meter_no_seri` varchar(50) NOT NULL DEFAULT '',
  `water_meter_kondisi` varchar(50) NOT NULL DEFAULT '',
  `meteran_akhir` int(11) NOT NULL,
  `periode` date NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.client: ~7 rows (approximately)
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
REPLACE INTO `client` (`id`, `nama_perusahaan`, `kap_mesin_produksi`, `satuan_kap_mesin_produksi`, `kap_prod_produksi`, `kap_prod_operasional`, `kap_prod_hari_operasional`, `kap_prod_jumlah_produksi`, `water_meter_no_seri`, `water_meter_kondisi`, `meteran_akhir`, `periode`, `status`) VALUES
	(8, 'Perusahaan A', '11', 'hari', '1', '1', '1', '1', '1', '1', 130, '2020-06-30', 0);
/*!40000 ALTER TABLE `client` ENABLE KEYS */;

-- Dumping structure for table pajak2.config
DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `denda_input` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'persen',
  `harga_per_watt` int(11) NOT NULL,
  `denda_bayar` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'persen',
  `batas_input` int(11) NOT NULL COMMENT 'hari',
  `batas_bayar` int(11) NOT NULL COMMENT 'per x hari'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.config: ~0 rows (approximately)
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
REPLACE INTO `config` (`denda_input`, `harga_per_watt`, `denda_bayar`, `batas_input`, `batas_bayar`) VALUES
	(0.25, 1000, 0.02, 15, 30);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;

-- Dumping structure for table pajak2.detail_transaksi
DROP TABLE IF EXISTS `detail_transaksi`;
CREATE TABLE IF NOT EXISTS `detail_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaksi_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `awal` int(11) NOT NULL,
  `akhir` int(11) NOT NULL,
  `pemakaian` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '1 => Tidak Libur\r\n0 => Libur\r\n',
  PRIMARY KEY (`id`),
  KEY `FK_detail_transaksi_transaksi` (`transaksi_id`),
  CONSTRAINT `FK_detail_transaksi_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.detail_transaksi: ~31 rows (approximately)
/*!40000 ALTER TABLE `detail_transaksi` DISABLE KEYS */;
REPLACE INTO `detail_transaksi` (`id`, `transaksi_id`, `tanggal`, `awal`, `akhir`, `pemakaian`, `status`) VALUES
	(249, 9, '2020-06-01', 100, 101, 1, 1),
	(250, 9, '2020-06-02', 101, 102, 1, 1),
	(251, 9, '2020-06-03', 102, 103, 1, 1),
	(252, 9, '2020-06-04', 103, 104, 1, 1),
	(253, 9, '2020-06-05', 104, 105, 1, 1),
	(254, 9, '2020-06-06', 105, 106, 1, 1),
	(255, 9, '2020-06-07', 106, 107, 1, 1),
	(256, 9, '2020-06-08', 107, 108, 1, 1),
	(257, 9, '2020-06-09', 108, 109, 1, 1),
	(258, 9, '2020-06-10', 109, 110, 1, 1),
	(259, 9, '2020-06-11', 110, 111, 1, 1),
	(260, 9, '2020-06-12', 111, 112, 1, 1),
	(261, 9, '2020-06-13', 112, 113, 1, 1),
	(262, 9, '2020-06-14', 113, 114, 1, 1),
	(263, 9, '2020-06-15', 114, 115, 1, 1),
	(264, 9, '2020-06-16', 115, 116, 1, 1),
	(265, 9, '2020-06-17', 116, 117, 1, 1),
	(266, 9, '2020-06-18', 117, 118, 1, 1),
	(267, 9, '2020-06-19', 118, 119, 1, 1),
	(268, 9, '2020-06-20', 119, 120, 1, 1),
	(269, 9, '2020-06-21', 120, 121, 1, 1),
	(270, 9, '2020-06-22', 121, 122, 1, 1),
	(271, 9, '2020-06-23', 122, 123, 1, 1),
	(272, 9, '2020-06-24', 123, 124, 1, 1),
	(273, 9, '2020-06-25', 124, 125, 1, 1),
	(274, 9, '2020-06-26', 125, 126, 1, 1),
	(275, 9, '2020-06-27', 126, 127, 1, 1),
	(276, 9, '2020-06-28', 127, 128, 1, 1),
	(277, 9, '2020-06-29', 128, 129, 1, 1),
	(278, 9, '2020-06-30', 129, 130, 1, 1);
/*!40000 ALTER TABLE `detail_transaksi` ENABLE KEYS */;

-- Dumping structure for table pajak2.pegawai
DROP TABLE IF EXISTS `pegawai`;
CREATE TABLE IF NOT EXISTS `pegawai` (
  `id` int(11) DEFAULT NULL,
  `nama` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.pegawai: ~0 rows (approximately)
/*!40000 ALTER TABLE `pegawai` DISABLE KEYS */;
/*!40000 ALTER TABLE `pegawai` ENABLE KEYS */;

-- Dumping structure for table pajak2.pengguna
DROP TABLE IF EXISTS `pengguna`;
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(250) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role_id` int(11) NOT NULL,
  `foreign_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `role_id_foreign_id` (`role_id`,`foreign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.pengguna: ~6 rows (approximately)
/*!40000 ALTER TABLE `pengguna` DISABLE KEYS */;
REPLACE INTO `pengguna` (`id`, `nama`, `email`, `username`, `password`, `role_id`, `foreign_id`) VALUES
	(1, 'Admin', 'admin@admin.com', 'admin@admin.com', 'asd', 1, 3),
	(8, 'Perusahaan A', 'a@a.com', 'a', 'asd', 3, 8);
/*!40000 ALTER TABLE `pengguna` ENABLE KEYS */;

-- Dumping structure for table pajak2.role
DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.role: ~3 rows (approximately)
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
REPLACE INTO `role` (`id`, `nama`) VALUES
	(1, 'Administrator'),
	(2, 'Pegawai'),
	(3, 'Client');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;

-- Dumping structure for table pajak2.status
DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  `singkatan` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.status: ~7 rows (approximately)
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
REPLACE INTO `status` (`id`, `status`, `singkatan`) VALUES
	(1, 'Input Data', NULL),
	(2, 'Gagal verifikasi', NULL),
	(3, 'Menunggu Pembayaran', NULL),
	(4, 'Menunggu Konfirmasi Pembayaran', NULL),
	(5, 'Gagal Pembayaran', NULL),
	(6, 'Transaksi Berhasil', NULL),
	(99, 'Transaksi Gagal', NULL);
/*!40000 ALTER TABLE `status` ENABLE KEYS */;

-- Dumping structure for table pajak2.transaksi
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `periode` date NOT NULL,
  `file_meteran` varchar(250) NOT NULL,
  `file_pembayaran` varchar(250) NOT NULL,
  `biaya` int(11) DEFAULT NULL,
  `harga_per_watt` int(11) DEFAULT NULL,
  `meteran_awal` int(11) NOT NULL,
  `meteran_akhir` int(11) NOT NULL,
  `waktu_input` datetime DEFAULT NULL,
  `waktu_verifikasi` datetime DEFAULT NULL,
  `waktu_pembayaran` datetime DEFAULT NULL,
  `status_transaksi_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_transaksi_client` (`client_id`),
  CONSTRAINT `FK_transaksi_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.transaksi: ~1 rows (approximately)
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;
REPLACE INTO `transaksi` (`id`, `client_id`, `periode`, `file_meteran`, `file_pembayaran`, `biaya`, `harga_per_watt`, `meteran_awal`, `meteran_akhir`, `waktu_input`, `waktu_verifikasi`, `waktu_pembayaran`, `status_transaksi_id`) VALUES
	(9, 8, '2020-06-30', 'file-meteran-8-2020-06.jpg', 'file-pembayaran-8-2020-06.jpg', 37500, 1000, 100, 130, '2020-08-20 20:16:30', '2020-08-20 20:17:49', '2020-08-20 20:18:20', 6);
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;

-- Dumping structure for table pajak2.transaksi_status
DROP TABLE IF EXISTS `transaksi_status`;
CREATE TABLE IF NOT EXISTS `transaksi_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaksi_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;

-- Dumping data for table pajak2.transaksi_status: ~47 rows (approximately)
/*!40000 ALTER TABLE `transaksi_status` DISABLE KEYS */;
REPLACE INTO `transaksi_status` (`id`, `transaksi_id`, `status_id`, `waktu`) VALUES
	(1, 1, 1, '2020-07-30 01:55:10'),
	(2, 1, 3, '2020-07-30 01:56:06'),
	(3, 1, 2, '2020-07-30 01:56:21'),
	(4, 1, 3, '2020-07-30 01:57:47'),
	(5, 1, 4, '2020-07-30 01:58:04'),
	(6, 1, 6, '2020-07-30 01:58:38'),
	(7, 1, 5, '2020-07-30 01:59:09'),
	(8, 1, 6, '2020-07-30 01:59:20'),
	(9, 1, 4, '2020-08-01 21:53:41'),
	(10, 2, 1, '2020-08-01 21:57:46'),
	(11, 3, 1, '2020-08-01 21:58:16'),
	(12, 1, 4, '2020-08-01 21:58:45'),
	(13, 3, 3, '2020-08-01 22:28:16'),
	(14, 3, 2, '2020-08-01 22:28:38'),
	(15, 3, 3, '2020-08-01 22:28:53'),
	(16, 3, 5, '2020-08-01 22:30:58'),
	(17, 3, 6, '2020-08-01 22:31:10'),
	(18, 1, 3, '2020-08-03 20:06:55'),
	(19, 4, 1, '2020-08-03 21:03:16'),
	(20, 4, 3, '2020-08-03 21:26:09'),
	(21, 7, 1, '2020-08-07 11:17:21'),
	(23, 11, 1, '2020-08-07 11:57:31'),
	(24, 1, 4, '2020-08-07 11:59:53'),
	(25, 1, 4, '2020-08-07 12:02:26'),
	(26, 12, 1, '2020-08-07 12:07:07'),
	(27, 1, 4, '2020-08-07 12:07:35'),
	(28, 1, 1, '2020-08-07 19:00:42'),
	(29, 2, 1, '2020-08-08 12:18:10'),
	(30, 3, 1, '2020-08-08 12:21:02'),
	(31, 4, 1, '2020-08-08 12:21:47'),
	(32, 5, 1, '2020-08-08 12:22:11'),
	(33, 6, 1, '2020-08-08 12:22:51'),
	(34, 7, 1, '2020-08-08 12:28:46'),
	(36, 1, 4, '2020-08-08 12:45:27'),
	(37, 1, 4, '2020-08-08 12:48:59'),
	(38, 1, 4, '2020-08-08 12:50:28'),
	(39, 1, 4, '2020-08-08 12:52:11'),
	(41, 1, 4, '2020-08-17 00:40:10'),
	(47, 1, 4, '2020-08-17 02:29:44'),
	(51, 1, 4, '2020-08-17 15:07:18'),
	(54, 9, 1, '2020-08-20 20:16:30'),
	(55, 9, 3, '2020-08-20 20:17:49'),
	(56, 1, 4, '2020-08-20 20:18:20'),
	(57, 9, 6, '2020-08-20 20:18:58');
/*!40000 ALTER TABLE `transaksi_status` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

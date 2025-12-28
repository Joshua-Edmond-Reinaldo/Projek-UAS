-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Des 2025 pada 12.10
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_toko`
--
CREATE DATABASE IF NOT EXISTS `penjualan_software` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `penjualan_software`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_pembelian`
--

CREATE TABLE `table_pembelian` (
  `id` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `nama_software` varchar(100) NOT NULL,
  `jumlah_lisensi` int(11) NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_penjualan`
--

CREATE TABLE `table_penjualan` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `jumlah_lisensi` int(11) NOT NULL,
  `nama_software` varchar(100) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `alamat` text NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tipe_lisensi` varchar(20) NOT NULL,
  `status_pembayaran` varchar(20) NOT NULL,
  `fitur_tambahan` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_stok`
--

CREATE TABLE `table_stok` (
  `id` int(11) NOT NULL,
  `nama_software` varchar(100) NOT NULL,
  `jumlah_stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_user`
--

CREATE TABLE `table_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `level` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--
ALTER TABLE `table_pembelian` ADD PRIMARY KEY (`id`);
ALTER TABLE `table_penjualan` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);
ALTER TABLE `table_stok` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `nama_software` (`nama_software`);
ALTER TABLE `table_user` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_logs`
--

CREATE TABLE `table_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- AUTO_INCREMENT for dumped tables
--
ALTER TABLE `table_pembelian` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `table_penjualan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `table_stok` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `table_user` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dumping data for table `table_user`
--

INSERT INTO `table_user` (`username`, `password`, `email`, `level`) VALUES
('admin', 'admin123', 'admin@toko.com', 'admin'),
('staff', 'staff123', 'staff@toko.com', 'user');

--
-- Dumping data for table `table_stok`
--

INSERT INTO `table_stok` (`nama_software`, `jumlah_stok`) VALUES
('Antivirus Pro 2025', 100),
('Video Editor X', 50),
('Cloud ERP System', 20),
('Dev Tools Ultimate', 75);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
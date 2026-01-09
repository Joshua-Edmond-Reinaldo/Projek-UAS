-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Jan 2026 pada 11.41
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_coupons`
--

CREATE TABLE `table_coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL DEFAULT 'fixed',
  `value` decimal(12,2) NOT NULL,
  `valid_until` date DEFAULT NULL,
  `usage_limit` int(11) DEFAULT 0,
  `used_count` int(11) DEFAULT 0,
  `limit_per_user` tinyint(1) DEFAULT 0,
  `apply_to_all` tinyint(1) DEFAULT 1,
  `apply_rule` varchar(50) DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `table_coupons`
--

INSERT INTO `table_coupons` (`id`, `code`, `type`, `value`, `valid_until`, `usage_limit`, `used_count`, `limit_per_user`, `apply_to_all`, `apply_rule`, `created_at`) VALUES
(2, 'HEMAT10', 'percentage', 5.00, '2026-01-08', 0, 1, 1, 1, 'all', '2026-01-07 13:54:12'),
(3, 'NEXUS10', 'percentage', 10.00, '2026-01-08', 0, 5, 0, 1, 'product', '2026-01-07 14:56:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_coupon_categories`
--

CREATE TABLE `table_coupon_categories` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_coupon_products`
--

CREATE TABLE `table_coupon_products` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `table_coupon_products`
--

INSERT INTO `table_coupon_products` (`id`, `coupon_id`, `product_name`) VALUES
(2, 3, 'Nexus OS');

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_coupon_usage`
--

CREATE TABLE `table_coupon_usage` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `table_coupon_usage`
--

INSERT INTO `table_coupon_usage` (`id`, `coupon_id`, `username`, `used_at`) VALUES
(1, 2, 'staff', '2026-01-07 13:55:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_logs`
--

CREATE TABLE `table_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `table_logs`
--

INSERT INTO `table_logs` (`id`, `username`, `action`, `details`, `timestamp`) VALUES
(1, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 19:35:01'),
(2, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 19:35:14'),
(3, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 19:35:44'),
(4, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 19:35:55'),
(5, 'staff', 'Cart Checkout', 'Membeli 1 jenis item (Pending)', '2026-01-07 19:36:12'),
(6, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 19:36:39'),
(7, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 19:36:47'),
(8, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 19:38:00'),
(9, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 19:38:09'),
(10, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 19:45:13'),
(11, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 19:45:26'),
(12, 'admin', 'Edit Penjualan', 'Mengubah data transaksi ID: 1 (staff)', '2026-01-07 19:46:17'),
(13, 'admin', 'Edit Penjualan', 'Mengubah data transaksi ID: 1 (staff)', '2026-01-07 19:47:44'),
(14, 'admin', 'Edit Penjualan', 'Mengubah data transaksi ID: 1 (staff)', '2026-01-07 19:54:19'),
(15, 'admin', 'Edit Penjualan', 'Mengubah data transaksi ID: 1 (staff)', '2026-01-07 19:59:41'),
(16, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 20:05:45'),
(17, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 20:06:26'),
(18, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 20:07:16'),
(19, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 20:07:25'),
(20, 'admin', 'Tambah Kupon', 'Menambahkan kupon: HEMAT10', '2026-01-07 20:08:35'),
(21, 'admin', 'Edit Kupon', 'Mengubah kupon ID: 1 (HEMAT10)', '2026-01-07 20:08:47'),
(22, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 20:20:03'),
(23, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 20:22:13'),
(24, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 20:25:35'),
(25, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 20:47:22'),
(26, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 20:47:37'),
(27, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 20:47:50'),
(28, 'admin', 'Input Penjualan', 'Menambahkan transaksi: staff - Nexus OS', '2026-01-07 20:51:28'),
(29, 'admin', 'Hapus Penjualan', 'Menghapus transaksi ID: 2', '2026-01-07 20:51:36'),
(30, 'admin', 'Hapus Kupon', 'Menghapus kupon: HEMAT10', '2026-01-07 20:53:36'),
(31, 'admin', 'Tambah Kupon', 'Menambahkan kupon: HEMAT10', '2026-01-07 20:54:12'),
(32, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 20:54:20'),
(33, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 20:54:28'),
(34, 'staff', 'Cart Checkout', 'Membeli 1 jenis item (Pending)', '2026-01-07 20:55:04'),
(35, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 20:55:42'),
(36, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 20:55:52'),
(37, 'admin', 'Verifikasi Pembayaran', 'Mengubah status transaksi ID 3 menjadi Lunas', '2026-01-07 20:56:13'),
(38, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 21:32:38'),
(39, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 21:32:51'),
(40, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 21:54:40'),
(41, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 21:54:59'),
(42, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 21:55:14'),
(43, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 21:55:23'),
(44, 'admin', 'Tambah Kupon', 'Menambahkan kupon: NEXUS10', '2026-01-07 21:56:27'),
(45, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 21:56:39'),
(46, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 21:56:51'),
(47, 'staff', 'Cart Checkout', 'Membeli 2 jenis item (Pending)', '2026-01-07 21:57:51'),
(48, 'staff', 'Cart Checkout', 'Membeli 2 jenis item (Pending)', '2026-01-07 22:10:32'),
(49, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 22:14:30'),
(50, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 22:14:41'),
(51, 'admin', 'Edit Kupon', 'Mengubah kupon ID: 3 (NEXUS10)', '2026-01-07 22:24:17'),
(52, 'admin', 'Logout', 'User berhasil logout', '2026-01-07 22:31:29'),
(53, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-07 22:31:37'),
(54, 'staff', 'Cart Checkout', 'Membeli 2 jenis item (Pending)', '2026-01-07 22:32:24'),
(55, 'staff', 'Cart Checkout', 'Membeli 1 jenis item (Pending)', '2026-01-07 22:32:48'),
(56, 'staff', 'Cart Checkout', 'Membeli 2 jenis item (Pending)', '2026-01-07 22:39:57'),
(57, 'staff', 'Logout', 'User berhasil logout', '2026-01-07 22:42:00'),
(58, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-07 22:42:08'),
(59, 'admin', 'Verifikasi Pembayaran', 'Mengubah status transaksi ID 12 menjadi Lunas', '2026-01-07 22:42:25'),
(60, 'admin', 'Verifikasi Pembayaran', 'Mengubah status transaksi ID 11 menjadi Lunas', '2026-01-07 22:42:58'),
(61, 'admin', 'Logout', 'User berhasil logout', '2026-01-08 09:39:33'),
(62, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-08 09:55:03'),
(63, 'staff', 'Cart Checkout', 'Membeli 1 jenis item (Pending)', '2026-01-08 09:56:46'),
(64, 'staff', 'Logout', 'User berhasil logout', '2026-01-08 10:01:13'),
(65, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-08 10:01:25'),
(66, 'admin', 'Logout', 'User berhasil logout', '2026-01-08 10:08:09'),
(67, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-08 10:08:16'),
(68, 'staff', 'Cart Checkout', 'Membeli 1 jenis item (Pending)', '2026-01-08 10:08:37'),
(69, 'staff', 'Logout', 'User berhasil logout', '2026-01-08 10:08:52'),
(70, 'admin', 'Login', 'User berhasil login ke sistem', '2026-01-08 10:08:59'),
(71, 'admin', 'Verifikasi Pembayaran', 'Mengubah status transaksi ID 14 menjadi Lunas', '2026-01-08 10:10:00'),
(72, 'admin', 'Logout', 'User berhasil logout', '2026-01-08 10:10:05'),
(73, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-08 10:10:12'),
(74, 'staff', 'Login', 'User berhasil login ke sistem', '2026-01-09 17:35:20'),
(75, 'staff', 'Logout', 'User berhasil logout', '2026-01-09 17:35:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `table_penjualan`
--

CREATE TABLE `table_penjualan` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `nama_pembeli` varchar(100) DEFAULT NULL,
  `jumlah_lisensi` int(11) DEFAULT NULL,
  `nama_software` varchar(100) DEFAULT NULL,
  `tanggal_transaksi` date DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tipe_lisensi` varchar(50) DEFAULT NULL,
  `status_pembayaran` enum('Lunas','Pending','Batal') DEFAULT NULL,
  `fitur_tambahan` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `table_penjualan`
--

INSERT INTO `table_penjualan` (`id`, `username`, `nama_pembeli`, `jumlah_lisensi`, `nama_software`, `tanggal_transaksi`, `harga`, `alamat`, `metode_pembayaran`, `no_hp`, `tipe_lisensi`, `status_pembayaran`, `fitur_tambahan`, `email`, `bukti_pembayaran`, `profile_picture`) VALUES
(1, 'staff', 'staff', 1, 'Nexus OS', '2026-01-07', 1200000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Lunas', 'Standar', 'staff@toko.com', 'uploads/bukti_bayar/1767789389_—Pngtree—website for mobile store rendered_3706125 (1).jpg', NULL),
(3, 'staff', 'staff', 1, 'Cloud ERP System', '2026-01-07', 1425000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Lunas', 'Standar', 'staff@toko.com', 'uploads/bukti_bayar/1767794130_Data Science.png', NULL),
(4, 'staff', 'staff', 1, 'Video Editor X', '2026-01-07', 750000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(5, 'staff', 'staff', 1, 'Nexus OS', '2026-01-07', 1200000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(6, 'staff', 'staff', 1, 'Nexus OS', '2026-01-07', 1200000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(7, 'staff', 'staff', 1, 'Video Editor X', '2026-01-07', 750000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(8, 'staff', 'staff', 1, 'Nexus OS', '2026-01-07', 1200000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(9, 'staff', 'staff', 1, 'Video Editor X', '2026-01-07', 750000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(10, 'staff', 'staff', 1, 'Nexus OS', '2026-01-07', 1200000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(11, 'staff', 'staff', 1, 'Nexus OS', '2026-01-07', 1080000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Lunas', 'Standar', 'staff@toko.com', 'uploads/bukti_bayar/1767800432_—Pngtree—website for mobile store rendered_3706125.jpg', NULL),
(12, 'staff', 'staff', 1, 'Video Editor X', '2026-01-07', 750000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Lunas', 'Standar', 'staff@toko.com', 'uploads/bukti_bayar/1767800450_Activity Diagram Login.png', NULL),
(13, 'staff', 'staff', 1, 'Nexus OS', '2026-01-08', 1200000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Batal', 'Standar', 'staff@toko.com', NULL, NULL),
(14, 'staff', 'staff', 1, 'Nexus OS', '2026-01-08', 1080000.00, 'plamongan', 'Transfer Bank', '089652952900', 'Personal', 'Lunas', 'Standar', 'staff@toko.com', 'uploads/bukti_bayar/1767841730_—Pngtree—website for mobile store rendered_3706125 (1).jpg', NULL);

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
-- Dumping data untuk tabel `table_user`
--

INSERT INTO `table_user` (`id`, `username`, `password`, `email`, `level`) VALUES
(1, 'admin', 'admin123', 'admin@toko.com', 'admin'),
(2, 'staff', 'admin123', 'staff@toko.com', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `table_coupons`
--
ALTER TABLE `table_coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks untuk tabel `table_coupon_categories`
--
ALTER TABLE `table_coupon_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `table_coupon_products`
--
ALTER TABLE `table_coupon_products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `table_coupon_usage`
--
ALTER TABLE `table_coupon_usage`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `table_logs`
--
ALTER TABLE `table_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `table_penjualan`
--
ALTER TABLE `table_penjualan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `table_user`
--
ALTER TABLE `table_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `table_coupons`
--
ALTER TABLE `table_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `table_coupon_categories`
--
ALTER TABLE `table_coupon_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `table_coupon_products`
--
ALTER TABLE `table_coupon_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `table_coupon_usage`
--
ALTER TABLE `table_coupon_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `table_logs`
--
ALTER TABLE `table_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT untuk tabel `table_penjualan`
--
ALTER TABLE `table_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `table_user`
--
ALTER TABLE `table_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

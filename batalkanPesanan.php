<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

// Pastikan ada parameter ID
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $username = $_SESSION['username'];

    // Cek apakah pesanan ini milik user yang login DAN statusnya masih Pending
    // Kita update status menjadi 'Batal' alih-alih menghapus data agar ada riwayat
    $stmt = $conn->prepare("UPDATE table_penjualan SET status_pembayaran = 'Batal' WHERE id = ? AND username = ? AND status_pembayaran = 'Pending'");
    $stmt->bind_param("is", $id, $username);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Pesanan berhasil dibatalkan.'); window.location='dashboardCustomer.php';</script>";
        } else {
            echo "<script>alert('Gagal membatalkan pesanan. Mungkin pesanan sudah diproses atau bukan milik Anda.'); window.location='dashboardCustomer.php';</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan sistem.'); window.location='dashboardCustomer.php';</script>";
    }
    $stmt->close();
} else {
    header("location:dashboardCustomer.php");
}
?>
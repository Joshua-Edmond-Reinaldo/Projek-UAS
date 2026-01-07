<?php
session_start();
// Cek apakah user sudah login dan levelnya admin
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Ambil kode kupon sebelum dihapus untuk keperluan log
    $res = $conn->query("SELECT code FROM table_coupons WHERE id=$id");
    
    if ($res->num_rows > 0) {
        $code = $res->fetch_assoc()['code'];

        // Hapus data terkait di tabel anak terlebih dahulu (Manual Cascade)
        // Ini untuk berjaga-jaga jika Foreign Key di database tidak diset ON DELETE CASCADE
        $conn->query("DELETE FROM table_coupon_usage WHERE coupon_id = $id");
        $conn->query("DELETE FROM table_coupon_products WHERE coupon_id = $id");
        $conn->query("DELETE FROM table_coupon_categories WHERE coupon_id = $id");

        // Hapus dari table_coupons
        $sql = "DELETE FROM table_coupons WHERE id = $id";
        
        if ($conn->query($sql)) {
            catatLog($conn, $_SESSION['username'], 'Hapus Kupon', "Menghapus kupon: $code");
            header("location: tampilDataKupon.php");
            exit;
        } else {
            // Tampilkan error jika gagal
            echo "<script>alert('Gagal menghapus kupon: " . $conn->error . "'); window.location='tampilDataKupon.php';</script>";
        }
    } else {
        // Data tidak ditemukan
        header("location: tampilDataKupon.php");
        exit;
    }
} else {
    header("location: tampilDataKupon.php");
    exit;
}
?>

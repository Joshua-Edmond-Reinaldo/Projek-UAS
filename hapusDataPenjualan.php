<?php
session_start();
// Cek apakah user sudah login dan levelnya admin
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    die("Akses Ditolak: Anda tidak memiliki izin untuk menghapus data.");
}

require "koneksi.php";

if (!isset($_GET["kode"])) {
    die("Error: ID tidak ditemukan.");
}

$id = (int)$_GET["kode"];

$sql = "DELETE FROM table_penjualan WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    catatLog($conn, $_SESSION['username'], 'Hapus Penjualan', "Menghapus transaksi ID: $id");
    header("location:tampilDataPenjualan.php");
    exit;
} else {
    echo "Error menghapus record: " . mysqli_error($conn);
}
mysqli_close($conn);
?>
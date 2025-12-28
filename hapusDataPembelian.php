<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Ambil data pembelian sebelum dihapus untuk mengembalikan stok
    $sql_get = "SELECT nama_software, jumlah_lisensi FROM table_pembelian WHERE id = $id";
    $result_get = $conn->query($sql_get);
    if ($result_get && $result_get->num_rows > 0) {
        $row = $result_get->fetch_assoc();
        $nama_software = $row['nama_software'];
        $jumlah_lisensi = $row['jumlah_lisensi'];

        // Kembalikan (kurangi) stok
        $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok - $jumlah_lisensi WHERE nama_software = '$nama_software'");
    }

    // Hapus data pembelian
    $sql_delete = "DELETE FROM table_pembelian WHERE id = $id";
    if ($conn->query($sql_delete)) {
        header("location: tampilDataPembelian.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
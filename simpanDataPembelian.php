<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $nama_software = mysqli_real_escape_string($conn, $_POST['nama_software']);
    $jumlah_lisensi = (int)$_POST['jumlah_lisensi'];
    $harga_beli = (float)$_POST['harga_beli'];
    $tanggal_pembelian = mysqli_real_escape_string($conn, $_POST['tanggal_pembelian']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // 1. Insert data ke table_pembelian
    $sql_insert = "INSERT INTO table_pembelian (nama_supplier, nama_software, jumlah_lisensi, tanggal_pembelian, harga_beli, keterangan) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssisds", $nama_supplier, $nama_software, $jumlah_lisensi, $tanggal_pembelian, $harga_beli, $keterangan);
    
    if ($stmt_insert->execute()) {
        // 2. Update (tambah) stok di table_stok
        $sql_update_stok = "UPDATE table_stok SET jumlah_stok = jumlah_stok + ? WHERE nama_software = ?";
        $stmt_update = $conn->prepare($sql_update_stok);
        $stmt_update->bind_param("is", $jumlah_lisensi, $nama_software);
        
        if ($stmt_update->execute()) {
            header("location: tampilDataPembelian.php");
        } else {
            echo "Error updating stock: " . $conn->error;
        }
        $stmt_update->close();
    } else {
        echo "Error inserting purchase data: " . $conn->error;
    }
    $stmt_insert->close();
    $conn->close();
}
?>
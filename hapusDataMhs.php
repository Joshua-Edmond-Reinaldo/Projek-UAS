<?php
// hapusDataMhs.php

// Memanggil file pustaka fungsi koneksi
require "koneksi.php";

// Memindahkan data ID kiriman dari URL (GET)
if (!isset($_GET["kode"])) {
    die("Error: ID tidak ditemukan.");
}

$id = (int)$_GET["kode"]; // Casting ke integer untuk mencegah SQL Injection

// Membuat query hapus data
// Catatan: Pastikan 'table_mhs' adalah nama tabel yang benar di database Anda.
$sql = "DELETE FROM table_mhs WHERE id = $id"; // $id sudah aman karena dipaksa jadi integer

// Eksekusi query
if (mysqli_query($conn, $sql)) {
    // Redirect kembali ke halaman tampilan data setelah berhasil
    header("location:tampilDataMhs.php");
    exit;
} else {
    // Tampilkan pesan error jika gagal
    echo "Error menghapus record: " . mysqli_error($conn);
}

// Tutup koneksi
mysqli_close($conn);

?>
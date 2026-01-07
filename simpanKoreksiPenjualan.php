<?php
session_start();
// Cek izin akses
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    die("Akses Ditolak: Hanya admin yang boleh mengedit data.");
}

require "koneksi.php";

$id = $_POST['id'];

// Ambil data lama sebelum diupdate untuk perbandingan stok
$sql_old = "SELECT nama_software, jumlah_lisensi, status_pembayaran FROM table_penjualan WHERE id='$id'";
$res_old = $conn->query($sql_old);
$old_data = $res_old->fetch_assoc();
$old_status = $old_data['status_pembayaran'];
$old_qty = $old_data['jumlah_lisensi'];
$old_software = $old_data['nama_software'];

$username = mysqli_real_escape_string($conn, $_POST['username']);
$nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
$jumlah_lisensi = mysqli_real_escape_string($conn, $_POST['jumlah_lisensi']);
$nama_software = mysqli_real_escape_string($conn, $_POST['nama_software']);
$tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
$harga = mysqli_real_escape_string($conn, $_POST['harga']);
$alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
$metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
$no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
$tipe_lisensi = isset($_POST['tipe_lisensi']) ? mysqli_real_escape_string($conn, $_POST['tipe_lisensi']) : '';
$status_pembayaran = mysqli_real_escape_string($conn, $_POST['status_pembayaran']);
$fitur_tambahan = mysqli_real_escape_string($conn, $_POST['fitur_tambahan']);
$email = mysqli_real_escape_string($conn, $_POST['email']);

$sql_update = "UPDATE table_penjualan SET
                username='$username',
                nama_pembeli='$nama_pembeli',
                jumlah_lisensi='$jumlah_lisensi',
                nama_software='$nama_software',
                tanggal_transaksi='$tanggal_transaksi',
                harga='$harga',
                alamat='$alamat',
                metode_pembayaran='$metode_pembayaran',
                no_hp='$no_hp',
                tipe_lisensi='$tipe_lisensi',
                status_pembayaran='$status_pembayaran',
                fitur_tambahan='$fitur_tambahan',
                email='$email' ";
$sql_update .= "WHERE id='$id'";

if (mysqli_query($conn, $sql_update)) {
    catatLog($conn, $_SESSION['username'], 'Edit Penjualan', "Mengubah data transaksi ID: $id ($nama_pembeli)");

    header("location:tampilDataPenjualan.php");
    exit;
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
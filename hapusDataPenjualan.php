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

// Ambil data penjualan sebelum dihapus untuk mengembalikan stok
$sql_get = "SELECT nama_software, jumlah_lisensi, status_pembayaran FROM table_penjualan WHERE id = $id";
$result_get = $conn->query($sql_get);

if ($result_get && $result_get->num_rows > 0) {
    $row = $result_get->fetch_assoc();
    // Hanya kembalikan stok jika status sebelumnya 'Lunas'
    if ($row['status_pembayaran'] == 'Lunas') {
        $nama_software = $row['nama_software'];
        $jumlah_lisensi = $row['jumlah_lisensi'];
        // Kembalikan stok
        $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok + $jumlah_lisensi WHERE nama_software = '$nama_software'");
    }
}

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
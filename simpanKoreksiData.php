<?php
// simpanKoreksiPenjualan.php

require "koneksi.php";

// Ambil data dari form POST
$id = $_POST['id'];

// Ambil data lama sebelum diupdate untuk perbandingan stok
$sql_old = "SELECT nama_software, jumlah_lisensi, status_pembayaran FROM table_penjualan WHERE id='$id'";
$res_old = $conn->query($sql_old);
$old_data = $res_old->fetch_assoc();
$old_status = $old_data['status_pembayaran'];
$old_qty = $old_data['jumlah_lisensi'];
$old_software = $old_data['nama_software'];

$username = $_POST['username'];
$nama_pembeli = $_POST['nama_pembeli'];
$jumlah_lisensi = $_POST['jumlah_lisensi'];
$nama_software = $_POST['nama_software'];
$tanggal_transaksi = $_POST['tanggal_transaksi'];
$harga = $_POST['harga'];
$alamat = $_POST['alamat'];
$metode_pembayaran = $_POST['metode_pembayaran'];
$no_hp = $_POST['no_hp'];
$tipe_lisensi = isset($_POST['tipe_lisensi']) ? $_POST['tipe_lisensi'] : '';
$status_pembayaran = $_POST['status_pembayaran'];
$fitur_tambahan = $_POST['fitur_tambahan'];
$email = $_POST['email'];
$password = $_POST['password']; // Ambil password, akan diupdate jika diisi

// Validation removed for simplicity

// Sanitasi data untuk keamanan (penting!)
$id = mysqli_real_escape_string($conn, $id);
$username = mysqli_real_escape_string($conn, $username);
$nama_pembeli = mysqli_real_escape_string($conn, $nama_pembeli);
$jumlah_lisensi = mysqli_real_escape_string($conn, $jumlah_lisensi);
$nama_software = mysqli_real_escape_string($conn, $nama_software);
$tanggal_transaksi = mysqli_real_escape_string($conn, $tanggal_transaksi);
$harga = mysqli_real_escape_string($conn, $harga);
$alamat = mysqli_real_escape_string($conn, $alamat);
$metode_pembayaran = mysqli_real_escape_string($conn, $metode_pembayaran);
$no_hp = mysqli_real_escape_string($conn, $no_hp);
$tipe_lisensi = mysqli_real_escape_string($conn, $tipe_lisensi);
$status_pembayaran = mysqli_real_escape_string($conn, $status_pembayaran);
$fitur_tambahan = mysqli_real_escape_string($conn, $fitur_tambahan);
$email = mysqli_real_escape_string($conn, $email);

// Bangun string query UPDATE
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

// Jika kolom password diisi, tambahkan ke query update dengan hashing
if (!empty($password)) {
    // Simpan password sebagai plain text (tidak terenkripsi)
    $password_plain = mysqli_real_escape_string($conn, $password);
    $sql_update .= ", password='$password_plain' ";
}

// Tambahkan klausa WHERE
$sql_update .= "WHERE id='$id'";

// Eksekusi query
if (mysqli_query($conn, $sql_update)) {
    // Logika penyesuaian stok
    // 1. Jika status berubah dari Batal/Pending menjadi Lunas
    if ($status_pembayaran == 'Lunas' && $old_status != 'Lunas') {
        $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok - $jumlah_lisensi WHERE nama_software = '$nama_software'");
    }
    // 2. Jika status berubah dari Lunas menjadi Batal/Pending
    elseif ($status_pembayaran != 'Lunas' && $old_status == 'Lunas') {
        $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok + $old_qty WHERE nama_software = '$old_software'");
    }
    // 3. Jika status tetap Lunas, tapi ada perubahan jumlah atau nama software
    elseif ($status_pembayaran == 'Lunas' && $old_status == 'Lunas') {
        if ($nama_software != $old_software || $jumlah_lisensi != $old_qty) {
            // Kembalikan stok lama, kurangi stok baru
            $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok + $old_qty WHERE nama_software = '$old_software'");
            $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok - $jumlah_lisensi WHERE nama_software = '$nama_software'");
        }
    }
    // Redirect ke halaman tampil data setelah sukses
    header("location:tampilDataPenjualan.php");
    exit;
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($conn);

?>

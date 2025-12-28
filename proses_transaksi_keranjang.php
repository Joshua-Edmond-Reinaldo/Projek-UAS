<?php
session_start();
require "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_SESSION['cart'])) {
    // Ambil data user
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    $tanggal_transaksi = date('Y-m-d');
    $status_pembayaran = 'Pending';
    $metode_pembayaran = 'Transfer Bank';
    $tipe_lisensi = 'Personal';
    $fitur_tambahan = 'Standar';

    // 1. Cek Username Duplikat (User & Penjualan)
    $check = $conn->query("SELECT id FROM table_penjualan WHERE username='$username'");
    $check_user = $conn->query("SELECT id FROM table_user WHERE username='$username'");
    
    if ($check->num_rows > 0 || $check_user->num_rows > 0) {
        die("<script>alert('Username sudah terdaftar! Silakan gunakan username lain.'); window.history.back();</script>");
    }

    // 2. Cek Stok Semua Barang Dulu
    foreach ($_SESSION['cart'] as $item) {
        $nama_software = mysqli_real_escape_string($conn, $item['name']);
        $qty = $item['qty'];
        
        $cek_stok = $conn->query("SELECT jumlah_stok FROM table_stok WHERE nama_software='$nama_software'");
        if ($cek_stok && $cek_stok->num_rows > 0) {
            $row_stok = $cek_stok->fetch_assoc();
            if ($row_stok['jumlah_stok'] < $qty) {
                die("<script>alert('Stok untuk $nama_software tidak mencukupi (Sisa: {$row_stok['jumlah_stok']}).'); window.history.back();</script>");
            }
        } else {
            die("<script>alert('Software $nama_software tidak ditemukan di database.'); window.history.back();</script>");
        }
    }

    // 3. Buat User Baru
    $sql_user = "INSERT INTO table_user (username, password, email, level) VALUES ('$username', '$password', '$email', 'user')";
    if (!$conn->query($sql_user)) {
        die("Error creating user: " . $conn->error);
    }

    // 4. Proses Insert Transaksi Loop
    $success_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $nama_software = mysqli_real_escape_string($conn, $item['name']);
        $harga = $item['price'] * $item['qty']; // Total harga per item line
        $jumlah_lisensi = $item['qty'];

        $sql = "INSERT INTO table_penjualan 
                (username, nama_pembeli, jumlah_lisensi, nama_software, tanggal_transaksi, harga, alamat, metode_pembayaran, no_hp, tipe_lisensi, status_pembayaran, fitur_tambahan, email, password) 
                VALUES 
                ('$username', '$nama_pembeli', '$jumlah_lisensi', '$nama_software', '$tanggal_transaksi', '$harga', '$alamat', '$metode_pembayaran', '$no_hp', '$tipe_lisensi', '$status_pembayaran', '$fitur_tambahan', '$email', '$password')";

        if ($conn->query($sql)) {
            // Kurangi Stok
            $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok - $jumlah_lisensi WHERE nama_software='$nama_software'");
            $success_count++;
        }
    }

    if ($success_count > 0) {
        // Catat Log
        catatLog($conn, $username, 'Cart Checkout', "Membeli $success_count jenis item (Pending)");
        
        // Kosongkan Keranjang
        unset($_SESSION['cart']);
        
        echo "<script>
                alert('Transaksi Berhasil! Silakan login untuk melihat status pesanan.');
                window.location.href = 'login.php';
              </script>";
    } else {
        die("Gagal memproses transaksi.");
    }

} else {
    header("Location: index.php");
}
?>
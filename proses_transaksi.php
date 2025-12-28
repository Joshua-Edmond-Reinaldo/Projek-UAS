<?php
require "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Plain text sesuai sistem yang ada
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    $nama_software = mysqli_real_escape_string($conn, $_POST['nama_software']);
    $harga = (int)$_POST['harga'];
    $jumlah_lisensi = 1;
    $tanggal_transaksi = date('Y-m-d');
    $status_pembayaran = 'Pending';
    $metode_pembayaran = 'Transfer Bank'; // Default
    $tipe_lisensi = 'Personal'; // Default
    $fitur_tambahan = 'Standar';

    // Cek Username Duplikat
    $check = $conn->query("SELECT id FROM table_penjualan WHERE username='$username'");
    if ($check->num_rows > 0) {
        die("<script>alert('Username sudah terdaftar! Silakan gunakan username lain.'); window.history.back();</script>");
    }

    // Cek Username Duplikat di table_user
    $check_user = $conn->query("SELECT id FROM table_user WHERE username='$username'");
    if ($check_user->num_rows > 0) {
        die("<script>alert('Username sudah digunakan! Silakan gunakan username lain.'); window.history.back();</script>");
    }

    // Cek Stok
    $cek_stok = $conn->query("SELECT jumlah_stok FROM table_stok WHERE nama_software='$nama_software'");
    if ($cek_stok && $cek_stok->num_rows > 0) {
        $row_stok = $cek_stok->fetch_assoc();
        if ($row_stok['jumlah_stok'] < $jumlah_lisensi) {
             die("<script>alert('Maaf, stok software ini habis atau tidak mencukupi.'); window.history.back();</script>");
        }
    } else {
         // Jika data stok software tidak ditemukan di table_stok, bisa dianggap stok 0 atau tidak dijual
         die("<script>alert('Software tidak ditemukan dalam database stok.'); window.history.back();</script>");
    }

    // Insert Data
    $sql = "INSERT INTO table_penjualan 
            (username, nama_pembeli, jumlah_lisensi, nama_software, tanggal_transaksi, harga, alamat, metode_pembayaran, no_hp, tipe_lisensi, status_pembayaran, fitur_tambahan, email, password) 
            VALUES 
            ('$username', '$nama_pembeli', '$jumlah_lisensi', '$nama_software', '$tanggal_transaksi', '$harga', '$alamat', '$metode_pembayaran', '$no_hp', '$tipe_lisensi', '$status_pembayaran', '$fitur_tambahan', '$email', '$password')";

    if ($conn->query($sql)) {
        // Sukses
        // Insert ke table_user agar bisa login
        $sql_user = "INSERT INTO table_user (username, password, email, level) VALUES ('$username', '$password', '$email', 'user')";
        $conn->query($sql_user);

        // Catat Log (Public Transaction)
        catatLog($conn, $username, 'Public Purchase', "Membeli $nama_software (Pending)");

        // Kurangi Stok
        $conn->query("UPDATE table_stok SET jumlah_stok = jumlah_stok - $jumlah_lisensi WHERE nama_software='$nama_software'");
    } else {
        die("Error: " . $conn->error);
    }
} else {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Berhasil</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .receipt {
            background: #fff;
            color: #333;
            padding: 40px;
            border-radius: 5px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 30px rgba(80, 250, 123, 0.2);
            position: relative;
        }
        .receipt::before {
            content: '';
            position: absolute;
            top: -5px; left: 0; right: 0; height: 10px;
            background: repeating-linear-gradient(45deg, #fff, #fff 10px, #eee 10px, #eee 20px);
        }
        .receipt::after {
            content: '';
            position: absolute;
            bottom: -5px; left: 0; right: 0; height: 10px;
            background: repeating-linear-gradient(45deg, #fff, #fff 10px, #eee 10px, #eee 20px);
        }
        h2 { text-align: center; margin-top: 0; color: #000; border-bottom: 2px dashed #ccc; padding-bottom: 20px; }
        .item { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9em; }
        .total { border-top: 2px dashed #ccc; padding-top: 15px; margin-top: 20px; font-weight: bold; font-size: 1.2em; display: flex; justify-content: space-between; }
        .btn-home {
            display: block;
            width: 100%;
            text-align: center;
            background: #0f0f23;
            color: #fff;
            text-decoration: none;
            padding: 15px;
            margin-top: 30px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status {
            text-align: center;
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="receipt">
    <h2>STRUK PEMBELIAN</h2>
    
    <div class="status">
        Status: <strong>PENDING</strong><br>
        Silakan lakukan pembayaran.
    </div>

    <div class="item">
        <span>Tanggal</span>
        <span><?= $tanggal_transaksi ?></span>
    </div>
    <div class="item">
        <span>Pembeli</span>
        <span><?= htmlspecialchars($nama_pembeli) ?></span>
    </div>
    <div class="item">
        <span>Username</span>
        <span><?= htmlspecialchars($username) ?></span>
    </div>
    <hr style="border:none; border-top:1px dashed #ccc; margin: 15px 0;">
    <div class="item">
        <span>Produk</span>
        <span><?= htmlspecialchars($nama_software) ?></span>
    </div>
    <div class="item">
        <span>Lisensi</span>
        <span>1 Unit</span>
    </div>
    
    <div class="total">
        <span>TOTAL</span>
        <span>Rp <?= number_format($harga, 0, ',', '.') ?></span>
    </div>

    <p style="text-align: center; font-size: 0.8em; margin-top: 30px; color: #666;">
        Simpan struk ini sebagai bukti pemesanan.<br>
        Terima kasih telah berbelanja di CyberSoft!
    </p>

    <a href="index.php" class="btn-home">Kembali ke Beranda</a>
</div>

</body>
</html>
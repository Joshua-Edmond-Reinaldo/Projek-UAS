<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}
require "koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$username = $_SESSION['username'];

// Ambil data pesanan untuk validasi kepemilikan
$stmt = $conn->prepare("SELECT * FROM table_penjualan WHERE id = ? AND username = ?");
$stmt->bind_param("is", $id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Pesanan tidak ditemukan!'); window.location='dashboardCustomer.php';</script>";
    exit;
}
$row = $result->fetch_assoc();

// Proses Upload
if (isset($_POST['upload'])) {
    $target_dir = "uploads/bukti_bayar/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = time() . "_" . basename($_FILES["bukti"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi gambar
    $check = getimagesize($_FILES["bukti"]["tmp_name"]);
    if($check === false) {
        echo "<script>alert('File bukan gambar valid.');</script>";
        $uploadOk = 0;
    }
    // Validasi ukuran (max 2MB)
    if ($_FILES["bukti"]["size"] > 2000000) {
        echo "<script>alert('Ukuran file terlalu besar (Max 2MB).');</script>";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
            // Update database
            $stmt_update = $conn->prepare("UPDATE table_penjualan SET bukti_pembayaran = ? WHERE id = ?");
            
            // Auto-fix: Jika kolom belum ada (Unknown column), tambahkan otomatis
            if (!$stmt_update && strpos($conn->error, "Unknown column") !== false) {
                $conn->query("ALTER TABLE table_penjualan ADD COLUMN bukti_pembayaran VARCHAR(255) NULL AFTER status_pembayaran");
                $stmt_update = $conn->prepare("UPDATE table_penjualan SET bukti_pembayaran = ? WHERE id = ?");
            }

            if ($stmt_update) {
                $stmt_update->bind_param("si", $target_file, $id);
                if ($stmt_update->execute()) {
                    echo "<script>alert('Bukti pembayaran berhasil diupload! Admin akan segera memverifikasi.'); window.location='dashboardCustomer.php';</script>";
                } else {
                    echo "<script>alert('Gagal update database.');</script>";
                }
                $stmt_update->close();
            } else {
                echo "<script>alert('Gagal menyiapkan query. Pastikan kolom bukti_pembayaran sudah ada di database. Error: " . addslashes($conn->error) . "');</script>";
            }
        } else {
            echo "<script>alert('Gagal upload file.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan #<?= $id ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        h2 { color: #50fa7b; text-align: center; margin-bottom: 20px; }
        .info-box { background: rgba(0,0,0,0.2); padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px dashed #bd93f9; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total { font-size: 1.2em; font-weight: bold; color: #50fa7b; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px; margin-top: 10px; }
        
        label { display: block; margin-bottom: 10px; color: #ffb86c; font-weight: bold; }
        input[type="file"] { width: 100%; background: #2d2d42; padding: 10px; border-radius: 8px; border: 1px solid #44475a; color: #f8f8f2; margin-bottom: 20px; }
        
        .btn { width: 100%; padding: 15px; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 1em; }
        .btn-upload { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; }
        .btn-upload:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3); }
        .btn-back { background: transparent; color: #6272a4; margin-top: 15px; display: block; text-align: center; text-decoration: none; }
        .btn-back:hover { color: #f8f8f2; }
    </style>
</head>
<body>
    <div class="container">
        <h2>💸 Konfirmasi Pembayaran</h2>
        
        <div class="info-box">
            <div class="info-row"><span>ID Pesanan:</span> <span>#<?= $id ?></span></div>
            <div class="info-row"><span>Produk:</span> <span><?= htmlspecialchars($row['nama_software']) ?></span></div>
            <div class="info-row total"><span>Total Tagihan:</span> <span>Rp <?= number_format($row['harga'], 0, ',', '.') ?></span></div>
        </div>

        <div class="info-box" style="border-color: #50fa7b;">
            <p style="margin: 0 0 10px 0; color: #bd93f9; font-weight: bold;">Silakan transfer ke:</p>
            <p style="margin: 5px 0;">🏦 BCA: <strong>123-456-7890</strong></p>
            <p style="margin: 5px 0;">🏦 Mandiri: <strong>098-765-4321</strong></p>
            <p style="margin: 5px 0;">📱 OVO/GoPay: <strong>0812-3456-7890</strong></p>
            <p style="margin: 10px 0 0 0; font-size: 0.9em; color: #a0a0b0;">a.n. CyberSoft Store</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <label>Upload Bukti Transfer (JPG/PNG)</label>
            <input type="file" name="bukti" required accept="image/*">
            <button type="submit" name="upload" class="btn btn-upload">Kirim Bukti Pembayaran</button>
        </form>
        
        <a href="dashboardCustomer.php" class="btn-back">Kembali</a>
    </div>
</body>
</html>
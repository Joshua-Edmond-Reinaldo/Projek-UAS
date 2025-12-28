<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}
require "koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pembelian dari Supplier</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        h1 { color: #8be9fd; text-align: center; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; }
        input, select, textarea { width: 100%; padding: 12px; background: #2d2d42; border: 1px solid #44475a; border-radius: 8px; color: #f8f8f2; margin-bottom: 20px; font-family: inherit; }
        input:focus, select:focus, textarea:focus { border-color: #8be9fd; outline: none; }
        .btn { width: 100%; padding: 14px; background: #8be9fd; color: #0f0f23; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #50fa7b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Input Pembelian Baru</h1>
        <form action="simpanDataPembelian.php" method="POST">
            <label for="nama_supplier">Nama Supplier</label>
            <input type="text" id="nama_supplier" name="nama_supplier" required placeholder="e.g., PT Software Jaya">
            
            <label for="nama_software">Nama Software</label>
            <select id="nama_software" name="nama_software" required>
                <option value="">-- Pilih Software --</option>
                <?php
                $sql_stok = "SELECT nama_software FROM table_stok ORDER BY nama_software ASC";
                $res_stok = $conn->query($sql_stok);
                while($row = $res_stok->fetch_assoc()) {
                    echo "<option value='{$row['nama_software']}'>{$row['nama_software']}</option>";
                }
                ?>
            </select>

            <label for="jumlah_lisensi">Jumlah Lisensi Dibeli</label>
            <input type="number" id="jumlah_lisensi" name="jumlah_lisensi" required placeholder="e.g., 50">

            <label for="harga_beli">Total Harga Beli (Rp)</label>
            <input type="number" id="harga_beli" name="harga_beli" required placeholder="e.g., 5000000">

            <label for="tanggal_pembelian">Tanggal Pembelian</label>
            <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" required>

            <label for="keterangan">Keterangan (Opsional)</label>
            <textarea id="keterangan" name="keterangan" rows="3" placeholder="e.g., Nomor invoice, dll."></textarea>
            
            <button type="submit" class="btn">Simpan Pembelian</button>
        </form>
        <a href="tampilDataPembelian.php" class="btn-back">← Batal</a>
    </div>
</body>
</html>
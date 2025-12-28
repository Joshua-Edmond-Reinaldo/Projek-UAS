<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

$id = (int)$_GET['id'];
$sql = "SELECT * FROM table_stok WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if (!$row) {
    die("Data stok tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreksi Stok Software</title>
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
            max-width: 500px;
            width: 100%;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        h1 { color: #ffb86c; text-align: center; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; }
        input { width: 100%; padding: 12px; background: #2d2d42; border: 1px solid #44475a; border-radius: 8px; color: #f8f8f2; margin-bottom: 20px; }
        input:focus { border-color: #ffb86c; outline: none; }
        .btn { width: 100%; padding: 14px; background: #ffb86c; color: #0f0f23; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #8be9fd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Koreksi Stok</h1>
        <form action="simpanKoreksiStok.php" method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <label for="nama_software">Nama Software</label>
            <input type="text" id="nama_software" name="nama_software" value="<?= htmlspecialchars($row['nama_software']) ?>" readonly style="background:#44475a; cursor: not-allowed;">
            <label for="jumlah_stok">Jumlah Stok</label>
            <input type="number" id="jumlah_stok" name="jumlah_stok" value="<?= $row['jumlah_stok'] ?>" required>
            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>
        <a href="tampilDataStok.php" class="btn-back">← Batal</a>
    </div>
</body>
</html>

```

### 5. Skrip Simpan Koreksi Stok (`simpanKoreksiStok.php`)

```diff
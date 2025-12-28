<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Stok Software</title>
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
        h1 { color: #50fa7b; text-align: center; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; }
        input { width: 100%; padding: 12px; background: #2d2d42; border: 1px solid #44475a; border-radius: 8px; color: #f8f8f2; margin-bottom: 20px; }
        input:focus { border-color: #50fa7b; outline: none; }
        .btn { width: 100%; padding: 14px; background: #50fa7b; color: #0f0f23; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #8be9fd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Software ke Stok</h1>
        <form action="simpanDataStok.php" method="POST">
            <label for="nama_software">Nama Software</label>
            <input type="text" id="nama_software" name="nama_software" required placeholder="e.g., Antivirus Pro 2025">
            
            <label for="jumlah_stok">Jumlah Stok Awal</label>
            <input type="number" id="jumlah_stok" name="jumlah_stok" required placeholder="e.g., 100">
            
            <button type="submit" class="btn">Simpan ke Stok</button>
        </form>
        <a href="tampilDataStok.php" class="btn-back">← Batal</a>
    </div>
</body>
</html>

```

### 3. Skrip Simpan Stok (`simpanDataStok.php`)

```diff
<?php
// c:\xampp\htdocs\Projek UAS\tambahDataKupon.php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}

// Ambil data produk dari file pusat
require "data_produk.php";

// Ambil kategori unik dari produk
$categories = array_unique(array_column($products, 'category'));
sort($categories);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kupon</title>
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        h1 { color: #50fa7b; text-align: center; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; }
        input, select { width: 100%; padding: 12px; background: #2d2d42; border: 1px solid #44475a; border-radius: 8px; color: #f8f8f2; margin-bottom: 20px; }
        .btn { width: 100%; padding: 14px; background: #50fa7b; color: #0f0f23; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #8be9fd; text-decoration: none; }
        .checkbox-group { display: flex; align-items: center; background: #2d2d42; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        .checkbox-group input { width: auto; margin: 0 10px 0 0; }
        .checkbox-group label { margin: 0; color: #f8f8f2; }
        .radio-group { display: flex; flex-direction: column; gap: 10px; background: #2d2d42; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .radio-group label { margin: 0; color: #f8f8f2; display: flex; align-items: center; }
        .radio-group input { width: auto; margin: 0 10px 0 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Kupon</h1>
        <form action="simpanDataKupon.php" method="POST">
            <label>Kode Promo (Unik)</label>
            <input type="text" name="code" required placeholder="Contoh: HEMAT10" style="text-transform: uppercase;">
            <label>Tipe Diskon</label>
            <select name="type" required>
                <option value="percentage">Persentase (%)</option>
                <option value="fixed">Nominal Tetap (Rp)</option>
            </select>
            <label>Nilai Diskon</label>
            <input type="number" name="value" required placeholder="Contoh: 10 (untuk 10%) atau 50000">
            
            <label>Berlaku Untuk</label>
            <div class="radio-group">
                <label><input type="radio" name="apply_rule" value="all" checked onchange="toggleRuleSelects()"> Semua Produk</label>
                <label><input type="radio" name="apply_rule" value="product" onchange="toggleRuleSelects()"> Produk Tertentu</label>
                <label><input type="radio" name="apply_rule" value="category" onchange="toggleRuleSelects()"> Kategori Tertentu</label>
            </div>

            <div id="product-select-wrapper" style="display:none; margin-bottom: 20px;">
                <label>Pilih Produk</label>
                <div class="checkbox-group" style="flex-direction: column; align-items: flex-start;">
                    <?php foreach ($products as $p): ?>
                        <label><input type="checkbox" name="products[]" value="<?= htmlspecialchars($p['name']) ?>"> <?= htmlspecialchars($p['name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="category-select-wrapper" style="display:none; margin-bottom: 20px;">
                <label>Pilih Kategori</label>
                <div class="checkbox-group" style="flex-direction: column; align-items: flex-start;">
                    <?php foreach ($categories as $cat): ?>
                        <label><input type="checkbox" name="categories[]" value="<?= htmlspecialchars($cat) ?>"> <?= htmlspecialchars($cat) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="limit_per_user" name="limit_per_user" value="1">
                <label for="limit_per_user">Batasi penggunaan 1x per user</label>
            </div>
            <label>Berlaku Sampai (Opsional)</label>
            <input type="date" name="valid_until">
            <label>Batas Penggunaan Global (0 = Tak Terbatas)</label>
            <input type="number" name="usage_limit" value="0">
            <button type="submit" class="btn">Simpan Kupon</button>
        </form>
        <a href="tampilDataKupon.php" class="btn-back">← Batal</a>
    </div>

    <script>
        function toggleRuleSelects() {
            const rule = document.querySelector('input[name="apply_rule"]:checked').value;
            const productWrapper = document.getElementById('product-select-wrapper');
            const categoryWrapper = document.getElementById('category-select-wrapper');
            productWrapper.style.display = (rule === 'product') ? 'block' : 'none';
            categoryWrapper.style.display = (rule === 'category') ? 'block' : 'none';
        }
    </script>
</body>
</html>
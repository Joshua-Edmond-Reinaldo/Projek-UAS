<?php
// c:\xampp\htdocs\Projek UAS\koreksiDataKupon.php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}
require "koneksi.php";

$id = (int)$_GET['id'];
$sql = "SELECT * FROM table_coupons WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if (!$row) die("Data tidak ditemukan.");

// Ambil data produk dari file pusat
require "data_produk.php";
$categories = array_unique(array_column($products, 'category'));
sort($categories);

// Ambil produk yang terhubung (jika ada)
$linked_products = [];
if ($row['apply_rule'] == 'product') {
    $res_p = $conn->query("SELECT product_name FROM table_coupon_products WHERE coupon_id = $id");
    while($p = $res_p->fetch_assoc()) {
        $linked_products[] = $p['product_name'];
    }
}

// Ambil kategori yang terhubung (jika ada)
$linked_categories = [];
if ($row['apply_rule'] == 'category') {
    $res_c = $conn->query("SELECT category_name FROM table_coupon_categories WHERE coupon_id = $id");
    while($c = $res_c->fetch_assoc()) {
        $linked_categories[] = $c['category_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kupon</title>
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
        h1 { color: #ffb86c; text-align: center; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; }
        input, select { width: 100%; padding: 12px; background: #2d2d42; border: 1px solid #44475a; border-radius: 8px; color: #f8f8f2; margin-bottom: 20px; }
        .btn { width: 100%; padding: 14px; background: #ffb86c; color: #0f0f23; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
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
        <h1>Edit Kupon</h1>
        <form action="simpanKoreksiKupon.php" method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <label>Kode Promo</label>
            <input type="text" name="code" value="<?= htmlspecialchars($row['code']) ?>" required style="text-transform: uppercase;">
            <label>Tipe Diskon</label>
            <select name="type" required>
                <option value="percentage" <?= $row['type'] == 'percentage' ? 'selected' : '' ?>>Persentase (%)</option>
                <option value="fixed" <?= $row['type'] == 'fixed' ? 'selected' : '' ?>>Nominal Tetap (Rp)</option>
            </select>
            <label>Nilai Diskon</label>
            <input type="number" name="value" value="<?= $row['value'] ?>" required>
            
            <label>Berlaku Untuk</label>
            <div class="radio-group">
                <label><input type="radio" name="apply_rule" value="all" <?= $row['apply_rule'] == 'all' ? 'checked' : '' ?> onchange="toggleRuleSelects()"> Semua Produk</label>
                <label><input type="radio" name="apply_rule" value="product" <?= $row['apply_rule'] == 'product' ? 'checked' : '' ?> onchange="toggleRuleSelects()"> Produk Tertentu</label>
                <label><input type="radio" name="apply_rule" value="category" <?= $row['apply_rule'] == 'category' ? 'checked' : '' ?> onchange="toggleRuleSelects()"> Kategori Tertentu</label>
            </div>

            <div id="product-select-wrapper" style="display:<?= $row['apply_rule'] == 'product' ? 'block' : 'none' ?>; margin-bottom: 20px;">
                <label>Pilih Produk</label>
                <div class="checkbox-group" style="flex-direction: column; align-items: flex-start;">
                    <?php foreach ($products as $p): ?>
                        <label><input type="checkbox" name="products[]" value="<?= htmlspecialchars($p['name']) ?>" <?= in_array($p['name'], $linked_products) ? 'checked' : '' ?>> <?= htmlspecialchars($p['name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="category-select-wrapper" style="display:<?= $row['apply_rule'] == 'category' ? 'block' : 'none' ?>; margin-bottom: 20px;">
                <label>Pilih Kategori</label>
                <div class="checkbox-group" style="flex-direction: column; align-items: flex-start;">
                    <?php foreach ($categories as $cat): ?>
                        <label><input type="checkbox" name="categories[]" value="<?= htmlspecialchars($cat) ?>" <?= in_array($cat, $linked_categories) ? 'checked' : '' ?>> <?= htmlspecialchars($cat) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="limit_per_user" name="limit_per_user" value="1" <?= $row['limit_per_user'] ? 'checked' : '' ?>>
                <label for="limit_per_user">Batasi penggunaan 1x per user</label>
            </div>
            <label>Berlaku Sampai</label>
            <input type="date" name="valid_until" value="<?= $row['valid_until'] ?>">
            <label>Batas Penggunaan Global</label>
            <input type="number" name="usage_limit" value="<?= $row['usage_limit'] ?>">
            <button type="submit" class="btn">Simpan Perubahan</button>
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
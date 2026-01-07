<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}
require "koneksi.php";

if (!isset($_GET['id'])) {
    header("location:tampilDataKupon.php");
    exit;
}

$id = (int)$_GET['id'];

// Ambil Data Kupon Utama
$stmt = $conn->prepare("SELECT * FROM table_coupons WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$coupon = $result->fetch_assoc();

if (!$coupon) {
    die("Kupon tidak ditemukan.");
}

// Ambil Detail Item (Produk/Kategori)
$items = [];
$item_type = "";

if ($coupon['apply_rule'] == 'product') {
    $item_type = "Daftar Produk";
    $stmt_d = $conn->prepare("SELECT product_name as name FROM table_coupon_products WHERE coupon_id = ?");
    $stmt_d->bind_param("i", $id);
    $stmt_d->execute();
    $res_d = $stmt_d->get_result();
    while($row = $res_d->fetch_assoc()) {
        $items[] = $row['name'];
    }
} elseif ($coupon['apply_rule'] == 'category') {
    $item_type = "Daftar Kategori";
    $stmt_d = $conn->prepare("SELECT category_name as name FROM table_coupon_categories WHERE coupon_id = ?");
    $stmt_d->bind_param("i", $id);
    $stmt_d->execute();
    $res_d = $stmt_d->get_result();
    while($row = $res_d->fetch_assoc()) {
        $items[] = $row['name'];
    }
} else {
    $item_type = "Info";
    $items[] = "Kupon ini berlaku untuk SEMUA produk.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kupon</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        h1 { color: #50fa7b; text-align: center; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-item label { display: block; color: #bd93f9; font-size: 0.9em; margin-bottom: 5px; }
        .info-item span { font-size: 1.1em; font-weight: bold; color: #fff; }
        
        .list-container { background: rgba(0,0,0,0.2); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); }
        .list-container h3 { margin-top: 0; color: #ffb86c; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 10px; }
        ul { list-style: none; padding: 0; }
        li { padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; }
        li:last-child { border-bottom: none; }
        li::before { content: '👉'; margin-right: 10px; }
        
        .btn-back { display: block; text-align: center; margin-top: 30px; color: #6272a4; text-decoration: none; padding: 12px; border: 1px solid #6272a4; border-radius: 8px; width: 150px; margin-left: auto; margin-right: auto; transition: 0.3s; }
        .btn-back:hover { background: #6272a4; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎟️ Detail Kupon: <?= htmlspecialchars($coupon['code']) ?></h1>
        
        <div class="info-grid">
            <div class="info-item"><label>Tipe Diskon</label><span><?= ucfirst($coupon['type']) ?></span></div>
            <div class="info-item"><label>Nilai</label><span><?= ($coupon['type'] == 'percentage') ? intval($coupon['value']).'%' : 'Rp '.number_format($coupon['value']) ?></span></div>
            <div class="info-item"><label>Aturan Aplikasi</label><span><?= ucfirst($coupon['apply_rule']) ?></span></div>
            <div class="info-item"><label>Status</label><span><?= ($coupon['valid_until'] && $coupon['valid_until'] < date('Y-m-d')) ? 'Kadaluarsa' : 'Aktif' ?></span></div>
        </div>

        <div class="list-container">
            <h3>📋 <?= $item_type ?> yang Terhubung</h3>
            <ul>
                <?php foreach($items as $item): ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="tampilDataKupon.php" class="btn-back">← Kembali</a>
    </div>
</body>
</html>
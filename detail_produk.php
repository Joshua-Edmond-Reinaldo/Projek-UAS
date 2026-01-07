<?php
session_start();
require_once "data_produk.php";

$name = isset($_GET['name']) ? urldecode($_GET['name']) : '';
$product = null;

// Cari produk berdasarkan nama
foreach ($products as $p) {
    if ($p['name'] === $name) {
        $product = $p;
        break;
    }
}

if (!$product) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Detail Produk</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'JetBrains Mono', monospace;
            background: #0f0f23;
            color: #e2e8f0;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Background Asset */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('asset/background.jpg') no-repeat center center/cover;
            opacity: 0.3;
            z-index: -2;
            filter: blur(4px);
        }

        .container {
            max-width: 900px;
            width: 100%;
            background: rgba(30, 30, 46, 0.8);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: fadeInUp 0.6s ease-out;
        }

        @media (min-width: 768px) {
            .container {
                flex-direction: row;
            }
        }

        .product-image {
            flex: 1;
            min-height: 300px;
            background: #1a1a2e;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-image:hover img {
            transform: scale(1.1);
        }

        .product-info {
            flex: 1.5;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }

        h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .category {
            display: inline-block;
            background: rgba(189, 147, 249, 0.2);
            color: #bd93f9;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            align-self: flex-start;
        }

        .price {
            font-size: 1.8rem;
            font-weight: bold;
            color: #50fa7b;
            margin-bottom: 20px;
        }

        .section-title {
            color: #ffb86c;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 5px;
        }

        p { line-height: 1.6; color: #a0a0b0; margin-bottom: 10px; }

        .btn-group { margin-top: auto; padding-top: 30px; display: flex; gap: 15px; }
        
        .btn { padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: bold; text-align: center; transition: 0.3s; }
        .btn-buy { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; flex: 1; }
        .btn-buy:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3); }
        .btn-back { background: rgba(255, 255, 255, 0.1); color: #e2e8f0; }
        .btn-back:hover { background: rgba(255, 255, 255, 0.2); }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-image">
            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='https://via.placeholder.com/400x400?text=Software'">
        </div>
        <div class="product-info">
            <span class="category"><?= htmlspecialchars($product['category']) ?></span>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <div class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
            
            <div class="section-title">Deskripsi</div>
            <p><?= htmlspecialchars($product['details']) ?></p>

            <div class="section-title">Fungsi Utama</div>
            <p><?= htmlspecialchars($product['function']) ?></p>

            <div class="section-title">Teknologi (Tech Stack)</div>
            <p style="color: #bd93f9; font-family: monospace;"><?= htmlspecialchars($product['tech_stack']) ?></p>

            <div class="btn-group">
                <a href="index.php" class="btn btn-back">Kembali</a>
                <?php if(isset($_SESSION['username'])): ?>
                    <a href="tambah_keranjang.php?software=<?= urlencode($product['name']) ?>&price=<?= $product['price'] ?>&redirect=dashboard" class="btn btn-buy">🛒 Beli Sekarang</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-buy">Login untuk Membeli</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
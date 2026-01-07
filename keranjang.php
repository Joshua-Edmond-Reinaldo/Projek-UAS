<?php
session_start();

// Handle Hapus Item
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    unset($_SESSION['cart'][$id]);
    // Reset array keys agar urutan rapi
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: keranjang.php");
    exit;
}

// Handle Update Qty (jika diperlukan pengembangan lanjut)
// ...

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_bayar = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        h1 { color: #50fa7b; text-align: center; margin-bottom: 30px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { color: #8be9fd; text-transform: uppercase; font-size: 0.9em; }
        td { color: #f8f8f2; }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-delete { background: #ff5555; color: white; font-size: 0.8em; }
        .btn-checkout { 
            background: linear-gradient(135deg, #50fa7b, #40e66b); 
            color: #0f0f23; 
            width: 100%; 
            text-align: center; 
            padding: 15px;
            font-size: 1.1em;
        }
        .btn-continue { 
            background: #44475a; 
            color: #f8f8f2; 
            text-align: center;
            margin-top: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }

        .total-row { font-size: 1.2em; font-weight: bold; color: #50fa7b; }
        .empty-cart { text-align: center; padding: 50px; color: #6272a4; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Keranjang Belanja</h1>
        
        <?php if (empty($cart)): ?>
            <div class="empty-cart">
                <p>Keranjang Anda masih kosong.</p>
                <a href="dashboardCustomer.php" class="btn btn-checkout" style="width: auto;">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($cart as $key => $item): 
                        $subtotal = $item['price'] * $item['qty'];
                        $total_bayar += $subtotal;
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                        <td>
                            <a href="?hapus=<?= $key ?>" class="btn btn-delete" onclick="return confirm('Hapus item ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL BAYAR</td>
                        <td colspan="2" class="total-row">Rp <?= number_format($total_bayar, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>

            <a href="checkout_keranjang.php" class="btn btn-checkout">Lanjut ke Pembayaran</a>
            <a href="dashboardCustomer.php" class="btn btn-continue">Belanja Lagi</a>
        <?php endif; ?>
    </div>
</body>
</html>
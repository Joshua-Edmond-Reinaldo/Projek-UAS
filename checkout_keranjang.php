<?php
session_start();
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$total_bayar = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_bayar += ($item['price'] * $item['qty']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Keranjang</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        * { box-sizing: border-box; }
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }
        .container {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 700px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        h2 { text-align: center; color: #f8f8f2; margin-bottom: 30px; }
        
        .summary {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 12px;
            border: 1px dashed #50fa7b;
            margin-bottom: 30px;
        }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.9em; color: #a0a0b0; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 15px; font-size: 1.2em; font-weight: bold; color: #50fa7b; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #bd93f9; font-weight: 600; }
        input, textarea {
            width: 100%; padding: 14px; background: #2d2d42;
            border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 10px;
            color: #f8f8f2; font-family: inherit; outline: none;
        }
        input:focus, textarea:focus { border-color: #50fa7b; }
        
        .btn-submit {
            width: 100%; padding: 16px; background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23; border: none; border-radius: 12px; font-weight: bold;
            font-size: 1.1em; cursor: pointer; text-transform: uppercase; margin-top: 10px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(80, 250, 123, 0.3); }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #6272a4; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout Keranjang</h2>
        
        <div class="summary">
            <?php foreach($_SESSION['cart'] as $item): ?>
                <div class="summary-item">
                    <span><?= $item['qty'] ?>x <?= htmlspecialchars($item['name']) ?></span>
                    <span>Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-total">
                <span>Total Bayar</span>
                <span>Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
            </div>
        </div>

        <form action="proses_transaksi_keranjang.php" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_pembeli" required placeholder="Nama Anda">
            </div>

            <div class="form-group">
                <label>Username (untuk akun)</label>
                <input type="text" name="username" required placeholder="Buat username unik">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Buat password akun">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="email@contoh.com">
            </div>

            <div class="form-group">
                <label>Nomor HP</label>
                <input type="text" name="no_hp" required placeholder="08xxxxxxxxxx">
            </div>

            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="alamat" rows="3" required placeholder="Alamat pengiriman"></textarea>
            </div>

            <button type="submit" class="btn-submit">Bayar Sekarang</button>
        </form>
        <a href="keranjang.php" class="back-link">← Kembali ke Keranjang</a>
    </div>
</body>
</html>
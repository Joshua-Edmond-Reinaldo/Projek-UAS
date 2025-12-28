<?php
$software = isset($_GET['software']) ? htmlspecialchars($_GET['software']) : '';
$price = isset($_GET['price']) ? (int)$_GET['price'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= $software ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * { box-sizing: border-box; }

        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 700px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            position: relative;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #50fa7b, #ffb86c);
            border-radius: 20px 20px 0 0;
        }

        h2 {
            text-align: center;
            color: #f8f8f2;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .product-summary {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 12px;
            border: 1px dashed #50fa7b;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-info h3 { margin: 0; color: #8be9fd; }
        .product-price { font-size: 1.5em; font-weight: bold; color: #50fa7b; }

        form { display: grid; gap: 20px; }

        .form-group { display: flex; flex-direction: column; }
        
        label {
            margin-bottom: 8px;
            color: #bd93f9;
            font-weight: 600;
            font-size: 0.9em;
        }

        input, select, textarea {
            padding: 14px;
            background: #2d2d42;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 10px;
            color: #f8f8f2;
            font-family: inherit;
            outline: none;
            transition: 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #50fa7b;
            box-shadow: 0 0 10px rgba(80, 250, 123, 0.2);
        }

        .btn-submit {
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1.1em;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(80, 250, 123, 0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6272a4;
            text-decoration: none;
        }
        .back-link:hover { color: #f8f8f2; }
    </style>
</head>
<body>

<div class="container">
    <h2>Form Pembelian</h2>
    
    <div class="product-summary">
        <div class="product-info">
            <span style="display:block; font-size:0.8em; color:#aaa;">Anda akan membeli:</span>
            <h3><?= $software ?></h3>
        </div>
        <div class="product-price">Rp <?= number_format($price, 0, ',', '.') ?></div>
    </div>

    <form action="proses_transaksi.php" method="POST">
        <input type="hidden" name="nama_software" value="<?= $software ?>">
        <input type="hidden" name="harga" value="<?= $price ?>">
        <input type="hidden" name="jumlah_lisensi" value="1">
        <input type="hidden" name="status_pembayaran" value="Pending">
        <input type="hidden" name="tanggal_transaksi" value="<?= date('Y-m-d') ?>">

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_pembeli" required placeholder="Masukkan nama lengkap Anda">
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
            <textarea name="alamat" rows="3" required placeholder="Alamat pengiriman / tagihan"></textarea>
        </div>

        <button type="submit" class="btn-submit">Konfirmasi Pembelian</button>
    </form>
    <a href="index.php" class="back-link">← Batalkan & Kembali</a>
</div>

</body>
</html>
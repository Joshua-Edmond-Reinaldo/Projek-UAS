<?php
session_start();
require "koneksi.php";

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Cek login (Wajib login untuk checkout)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$nama_pembeli = '';
$email = '';
$no_hp = '';
$alamat = '';

// 1. Ambil email dari table_user
$stmt = $conn->prepare("SELECT email FROM table_user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $email = $row['email'];
}

// 2. Ambil data profil terakhir dari riwayat transaksi (jika ada)
$stmt_profile = $conn->prepare("SELECT nama_pembeli, no_hp, alamat FROM table_penjualan WHERE username = ? ORDER BY id DESC LIMIT 1");
$stmt_profile->bind_param("s", $username);
$stmt_profile->execute();
$res_profile = $stmt_profile->get_result();
if ($row_profile = $res_profile->fetch_assoc()) {
    $nama_pembeli = $row_profile['nama_pembeli'];
    $no_hp = $row_profile['no_hp'];
    $alamat = $row_profile['alamat'];
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
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid #0f0f23;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .discount-box {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            margin-bottom: 20px;
        }
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
                <span id="totalDisplay">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="discount-box">
            <label style="display:block; margin-bottom:10px; color:#bd93f9; font-weight:600;">Kode Promo</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" id="couponCode" placeholder="HEMAT10" style="margin-bottom: 0; flex: 1;">
                <button type="button" onclick="applyCartCoupon()" style="padding: 10px 20px; background: #bd93f9; color: #0f0f23; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Cek</button>
            </div>
            <small id="couponMessage" style="display: block; margin-top: 8px; font-weight: bold;"></small>
        </div>

        <form action="proses_transaksi_keranjang.php" method="POST" onsubmit="showSpinner()">
            <input type="hidden" name="coupon_code" id="inputCouponCode" value="">
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_pembeli" required placeholder="Nama Anda" value="<?= htmlspecialchars($nama_pembeli) ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="email@contoh.com" value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="form-group">
                <label>Nomor HP</label>
                <input type="text" name="no_hp" required placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($no_hp) ?>">
            </div>

            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="alamat" rows="3" required placeholder="Alamat pengiriman"><?= htmlspecialchars($alamat) ?></textarea>
            </div>

            <button type="submit" class="btn-submit" id="checkoutBtn">
                <span class="spinner" id="spinner"></span>
                Bayar Sekarang
            </button>
        </form>
        <a href="keranjang.php" class="back-link">← Kembali ke Keranjang</a>
    </div>

    <script>
        function showSpinner() {
            const btn = document.getElementById('checkoutBtn');
            const spinner = document.getElementById('spinner');
            btn.disabled = true;
            spinner.style.display = 'inline-block';
        }

        let totalOriginal = <?= $total_bayar ?>;

        function applyCartCoupon() {
            const code = document.getElementById('couponCode').value.trim().toUpperCase();
            const msg = document.getElementById('couponMessage');
            const totalDisplay = document.getElementById('totalDisplay');
            const inputCoupon = document.getElementById('inputCouponCode');

            if (code === '') {
                msg.textContent = '';
                return;
            }

            fetch(`check_coupon.php?code=${encodeURIComponent(code)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        msg.style.color = '#50fa7b';
                        msg.textContent = data.message;
                        totalDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.new_total);
                        inputCoupon.value = code;
                    } else {
                        msg.style.color = '#ff5555';
                        msg.textContent = data.message;
                        totalDisplay.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalOriginal);
                        inputCoupon.value = '';
                    }
                })
                .catch(err => {
                    console.error(err);
                    msg.style.color = '#ff5555';
                    msg.textContent = 'Gagal memuat data kupon.';
                });
        }
    </script>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}
require "koneksi.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Post Action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $new_status = ($action == 'confirm') ? 'Lunas' : 'Batal';
    
    $stmt = $conn->prepare("UPDATE table_penjualan SET status_pembayaran = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    
    if ($stmt->execute()) {
        catatLog($conn, $_SESSION['username'], 'Verifikasi Pembayaran', "Mengubah status transaksi ID $id menjadi $new_status");
        echo "<script>alert('Status berhasil diperbarui menjadi $new_status!'); window.location='tampilDataPenjualan.php';</script>";
    } else {
        echo "<script>alert('Gagal update status.');</script>";
    }
}

// Fetch Data
$stmt = $conn->prepare("SELECT * FROM table_penjualan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='tampilDataPenjualan.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 900px;
            width: 100%;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        h1 { color: #50fa7b; text-align: center; margin: 0; }
        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-group {
            background: rgba(0,0,0,0.2);
            padding: 15px;
            border-radius: 10px;
        }
        .info-label { color: #bd93f9; font-size: 0.9em; margin-bottom: 5px; }
        .info-value { font-size: 1.1em; font-weight: bold; color: #fff; }
        
        .proof-img {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 10px;
            border: 2px solid #44475a;
            background: #000;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1em;
            transition: 0.3s;
            text-transform: uppercase;
        }
        .btn-confirm { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; }
        .btn-confirm:hover { box-shadow: 0 0 20px rgba(80, 250, 123, 0.4); transform: translateY(-2px); }
        
        .btn-reject { background: linear-gradient(135deg, #ff5555, #ff4444); color: white; }
        .btn-reject:hover { box-shadow: 0 0 20px rgba(255, 85, 85, 0.4); transform: translateY(-2px); }
        
        .btn-back { background: #44475a; color: #e2e8f0; }
        .btn-back:hover { background: #6272a4; }
        
        @media (max-width: 768px) {
            .details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verifikasi Pembayaran</h1>
        
        <div class="details">
            <div class="info-column">
                <div class="info-group">
                    <div class="info-label">ID Transaksi</div>
                    <div class="info-value">#<?= $row['id'] ?></div>
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <div class="info-label">Pembeli</div>
                    <div class="info-value"><?= htmlspecialchars($row['nama_pembeli']) ?> (<?= htmlspecialchars($row['username']) ?>)</div>
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <div class="info-label">Produk</div>
                    <div class="info-value"><?= htmlspecialchars($row['nama_software']) ?></div>
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <div class="info-label">Total Tagihan</div>
                    <div class="info-value" style="color: #50fa7b;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <div class="info-label">Tanggal Transaksi</div>
                    <div class="info-value"><?= date('d F Y', strtotime($row['tanggal_transaksi'])) ?></div>
                </div>
            </div>
            
            <div class="proof-column">
                <div class="info-label">Bukti Transfer:</div>
                <?php if (!empty($row['bukti_pembayaran'])): ?>
                    <a href="<?= $row['bukti_pembayaran'] ?>" target="_blank">
                        <img src="<?= $row['bukti_pembayaran'] ?>" alt="Bukti Pembayaran" class="proof-img">
                    </a>
                    <div style="text-align: center; margin-top: 5px; font-size: 0.8em; color: #a0a0b0;">Klik gambar untuk memperbesar</div>
                <?php else: ?>
                    <div style="padding: 50px; text-align: center; background: rgba(0,0,0,0.2); border-radius: 10px; color: #ff5555;">
                        Belum ada bukti pembayaran yang diupload.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" class="actions">
            <a href="tampilDataPenjualan.php" class="btn btn-back" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">Kembali</a>
            <button type="submit" name="action" value="reject" class="btn btn-reject" onclick="return confirm('Yakin ingin menolak pembayaran ini? Status akan menjadi Batal.')">❌ Tolak (Batal)</button>
            <button type="submit" name="action" value="confirm" class="btn btn-confirm" onclick="return confirm('Yakin konfirmasi pembayaran valid? Status akan menjadi Lunas.')">✅ Konfirmasi Lunas</button>
        </form>
    </div>
</body>
</html>
<?php
session_start();
// Cek apakah user login
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Jika admin mencoba akses, alihkan ke dashboard admin
if ($_SESSION['level'] == 'admin') {
    header("location:dashboard.php");
    exit;
}

require "koneksi.php";
$username = $_SESSION['username'];

// Ambil data user untuk foto profil
$sql_user = "SELECT profile_picture, email FROM table_penjualan WHERE username='$username' LIMIT 1";
$res_user = $conn->query($sql_user);
$user_data = ($res_user->num_rows > 0) ? $res_user->fetch_assoc() : ['profile_picture' => '', 'email' => ''];

// Ambil riwayat pembelian user ini
$sql_history = "SELECT * FROM table_penjualan WHERE username='$username' ORDER BY tanggal_transaksi DESC";
$result = $conn->query($sql_history);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 30px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .user-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #50fa7b;
            object-fit: cover;
        }
        .user-details h2 { margin: 0; color: #50fa7b; font-size: 1.5em; }
        .user-details p { margin: 5px 0 0; color: #bd93f9; }
        
        .btn-group { display: flex; gap: 10px; }
        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            color: #0f0f23;
            transition: 0.3s;
        }
        .btn-shop { background: linear-gradient(135deg, #8be9fd, #bd93f9); }
        .btn-settings { background: linear-gradient(135deg, #ffb86c, #ff79c6); }
        .btn-logout { background: linear-gradient(135deg, #ff5555, #ff4444); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }

        .history-section {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        h3 { color: #f8f8f2; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-top: 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { color: #8be9fd; font-weight: 600; text-transform: uppercase; font-size: 0.9em; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: rgba(255,255,255,0.02); }
        
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.8em; font-weight: bold; }
        .status-lunas { background: rgba(80, 250, 123, 0.2); color: #50fa7b; }
        .status-pending { background: rgba(255, 184, 108, 0.2); color: #ffb86c; }
        .status-batal { background: rgba(255, 85, 85, 0.2); color: #ff5555; }

        .empty-state { text-align: center; padding: 40px; color: #6272a4; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-info">
                <?php 
                $pp = !empty($user_data['profile_picture']) ? $user_data['profile_picture'] : 'https://ui-avatars.com/api/?name='.urlencode($username).'&background=50fa7b&color=0f0f23';
                ?>
                <img src="<?= $pp ?>" alt="Profile">
                <div class="user-details">
                    <h2>Halo, <?= htmlspecialchars($username) ?>!</h2>
                    <p>Member sejak: <?= date('Y') ?></p>
                </div>
            </div>
            <div class="btn-group">
                <a href="index.php" class="btn btn-shop">🛍️ Belanja Lagi</a>
                <a href="settings.php" class="btn btn-settings">⚙️ Akun</a>
                <a href="logout.php" class="btn btn-logout">🚪 Logout</a>
            </div>
        </div>

        <div class="history-section">
            <h3>📜 Riwayat Pembelian Saya</h3>
            <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Software</th>
                        <th>Lisensi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['tanggal_transaksi'])) ?></td>
                        <td style="color: #f8f8f2; font-weight: bold;"><?= htmlspecialchars($row['nama_software']) ?></td>
                        <td><?= $row['jumlah_lisensi'] ?> Unit</td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($row['status_pembayaran']) ?>">
                                <?= $row['status_pembayaran'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>Anda belum memiliki riwayat pembelian.</p>
                    <a href="index.php" style="color: #50fa7b;">Mulai Belanja Sekarang &rarr;</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
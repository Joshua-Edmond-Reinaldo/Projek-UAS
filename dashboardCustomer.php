<?php
session_start();
// Cek apakah user login
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Jika admin mencoba akses, alihkan ke dashboard admin
if (isset($_SESSION['level']) && $_SESSION['level'] == 'admin') {
    header("location:dashboard.php");
    exit;
}

require "koneksi.php";
require_once "data_produk.php"; // Memuat data produk
$username = $_SESSION['username'];

// Ambil data user untuk foto profil dan nama lengkap dari transaksi terakhir
$sql_user = "SELECT profile_picture, nama_pembeli FROM table_penjualan WHERE username=? ORDER BY id DESC LIMIT 1";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$user_data = ($res_user->num_rows > 0) ? $res_user->fetch_assoc() : ['profile_picture' => '', 'nama_pembeli' => $username];

// --- Logika Pencarian ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_param = "%" . $search . "%";

// Ambil riwayat pembelian user ini
$sql_history = "SELECT * FROM table_penjualan WHERE username=? AND nama_software != '-' ORDER BY tanggal_transaksi DESC";

$params = [$username];
$types = "s";

if ($search) {
    // Modifikasi query untuk menyertakan filter pencarian
    $sql_history = "SELECT * FROM table_penjualan WHERE username=? AND nama_software != '-' AND (nama_software LIKE ? OR status_pembayaran LIKE ?) ORDER BY tanggal_transaksi DESC";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param($types, ...$params);
$stmt_history->execute();
$result = $stmt_history->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dashboard Customer</title><style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: #0f0f23;
            color: #e2e8f0;
            margin: 0;
            padding: 100px 0 0 0;
            min-height: 100vh;
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

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(15, 15, 35, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            padding: 1rem 0;
            z-index: 1000;
        }
        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #50fa7b;
            text-decoration: none;
        }
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .hamburger {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            margin-left: 15px;
            position: relative;
        }
        .hamburger:hover {
            background: rgba(80, 250, 123, 0.1);
            transform: scale(1.1);
        }
        .hamburger span {
            width: 25px;
            height: 3px;
            background: #e2e8f0;
            transition: all 0.3s ease;
            transform-origin: center;
        }
        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
        .nav-links {
            display: flex;
            position: absolute;
            top: 100%;
            right: 20px;
            width: 250px;
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            padding: 1rem 0;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-links.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem;
            text-align: left;
            border-radius: 8px;
        }
        .nav-links a:hover {
            color: #50fa7b;
            background: rgba(80, 250, 123, 0.1);
        }
        .btn-logout-nav {
            background: linear-gradient(135deg, #ff5555, #ff4444);
            padding: 8px 16px;
            border-radius: 8px;
            color: white !important;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .main-content {
            margin-top: 100px;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
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

        /* Search Wrapper Style */
        .search-wrapper {
            margin-bottom: 25px;
        }
        .search-wrapper form {
            display: flex;
            gap: 10px;
            background: rgba(0,0,0,0.2);
            padding: 10px;
            border-radius: 12px;
        }
        .search-wrapper input {
            flex-grow: 1;
            padding: 10px 15px;
            background: #2d2d42;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: #f8f8f2;
            font-family: inherit;
            outline: none;
        }
        .search-wrapper button, .search-wrapper .btn-reset {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        .search-wrapper button { background: #50fa7b; color: #0f0f23; }
        .search-wrapper .btn-reset { background: #ff5555; color: white; display: inline-flex; align-items: center; }

        .btn-invoice {
            display: inline-block;
            padding: 5px 10px;
            background: rgba(140, 233, 253, 0.2);
            color: #8be9fd;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.8em;
            border: 1px solid rgba(140, 233, 253, 0.3);
            transition: 0.3s;
            margin-right: 5px;
        }
        .btn-invoice:hover { background: rgba(140, 233, 253, 0.4); }
        
        .btn-cancel {
            display: inline-block;
            padding: 5px 10px;
            background: rgba(255, 85, 85, 0.2);
            color: #ff5555;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.8em;
            border: 1px solid rgba(255, 85, 85, 0.3);
            transition: 0.3s;
        }
        .btn-cancel:hover { background: rgba(255, 85, 85, 0.4); }

        .btn-pay {
            display: inline-block;
            padding: 5px 10px;
            background: rgba(80, 250, 123, 0.2);
            color: #50fa7b;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.8em;
            border: 1px solid rgba(80, 250, 123, 0.3);
            transition: 0.3s;
            margin-right: 5px;
        }
        .btn-pay:hover { background: rgba(80, 250, 123, 0.4); }

        /* Product Grid Styles */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .product-card {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: #50fa7b;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .product-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }
        .product-title { font-size: 1.2em; color: #f8f8f2; margin: 0 0 10px 0; font-weight: bold; }
        .product-desc { color: #a0a0b0; font-size: 0.9em; margin-bottom: 15px; line-height: 1.5; }
        .product-features { list-style: none; padding: 0; margin: 0 0 15px 0; font-size: 0.85em; color: #bd93f9; flex-grow: 1; }
        .product-features li::before { content: '✓ '; color: #50fa7b; }
        .product-price { font-size: 1.3em; color: #50fa7b; font-weight: bold; margin-bottom: 15px; }
        .btn-buy-now {
            background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; text-align: center;
            padding: 12px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.3s; display: block;
        }
        .btn-buy-now:hover {
            box-shadow: 0 0 15px rgba(80, 250, 123, 0.4); transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">CyberSoft</a>
            <div class="navbar-actions">
                <a href="logout.php" class="btn-logout-nav">🚪 Logout</a>
                <div class="hamburger" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="nav-links">
                    <a href="keranjang.php">🛒 Keranjang</a>
                    <a href="settings.php">⚙️ Pengaturan Akun</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <div class="header">
            <div class="user-info">
                <?php 
                $pp = !empty($user_data['profile_picture']) ? $user_data['profile_picture'] : 'https://ui-avatars.com/api/?name='.urlencode($username).'&background=50fa7b&color=0f0f23';
                ?>
                <img src="<?= $pp ?>" alt="Profile">
                <div class="user-details">
                    <h2>Halo, <?= htmlspecialchars($user_data['nama_pembeli']) ?>!</h2>
                    <p>Username: <?= htmlspecialchars($username) ?></p>
                </div>
            </div>
        </div>

        <!-- Bagian Produk Software -->
        <div id="products">
            <h3>💻 Software Unggulan</h3>
            <div class="products-grid">
                <?php foreach ($products as $p): ?>
                    <div class="product-card">
                        <img src="<?= $p['image'] ?>" alt="<?= $p['name'] ?>" class="product-img" onerror="this.src='https://via.placeholder.com/300x200?text=Software'">
                        <div class="product-title"><?= $p['name'] ?></div>
                        <div class="product-desc"><?= $p['desc'] ?></div>
                        <ul class="product-features">
                            <?php foreach ($p['features'] as $f): ?>
                                <li><?= $f ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <a href="tambah_keranjang.php?software=<?= urlencode($p['name']) ?>&price=<?= $p['price'] ?>&redirect=dashboard" class="btn-buy-now">🛒 Masukkan Keranjang</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="history-section">
            <h3>📜 Riwayat Pembelian Saya</h3>

            <div class="search-wrapper">
                <form action="" method="GET">
                    <input type="text" name="search" placeholder="Cari nama software atau status..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">🔍 Cari</button>
                    <?php if ($search): ?>
                        <a href="dashboardCustomer.php" class="btn-reset">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Software</th>
                        <th>Lisensi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                        <td style="text-align: center; white-space: nowrap;">
                            <a href="cetak_invoice.php?id=<?= $row['id'] ?>" target="_blank" class="btn-invoice">📄 Invoice</a>
                            <?php if($row['status_pembayaran'] == 'Pending'): ?>
                                <?php if (isset($row['bukti_pembayaran']) && !empty($row['bukti_pembayaran'])): ?>
                                    <span style="color: #ffb86c; font-size: 0.8em; font-weight: bold; margin-right: 5px;">⏳ Dicek</span>
                                <?php else: ?>
                                    <a href="bayarPesanan.php?id=<?= $row['id'] ?>" class="btn-pay">💸 Bayar</a>
                                    <a href="batalkanPesanan.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')" class="btn-cancel">❌ Batal</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="empty-state">
                    <?php if ($search): ?>
                        <p>Tidak ada riwayat pembelian yang cocok dengan "<?= htmlspecialchars($search) ?>".</p>
                        <a href="dashboardCustomer.php" style="color: #ffb86c;">Tampilkan Semua Riwayat</a>
                    <?php else: ?>
                        <p>Anda belum memiliki riwayat pembelian.</p>
                        <a href="#products" style="color: #50fa7b;">Mulai Belanja Sekarang &rarr;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const hamburger = document.querySelector('.hamburger');
            const navLinks = document.querySelector('.nav-links');
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        }
    </script>
</body>
</html>

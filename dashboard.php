<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
}

// Proteksi: Hanya Admin yang boleh akses dashboard ini
if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("location:dashboardCustomer.php");
    exit;
}

require "koneksi.php";

// Ambil data user untuk foto profil
$username = $_SESSION['username'];
$sql_user = "SELECT profile_picture FROM table_penjualan WHERE username='$username' AND profile_picture IS NOT NULL ORDER BY id DESC LIMIT 1";
$res_user = $conn->query($sql_user);
$user_data = ($res_user && $res_user->num_rows > 0) ? $res_user->fetch_assoc() : null;

// 1. Total Pendapatan (Hanya yang Lunas)
$sql_revenue = "SELECT SUM(harga) as total FROM table_penjualan WHERE status_pembayaran = 'Lunas'";
$res_revenue = $conn->query($sql_revenue);
$row_revenue = $res_revenue->fetch_assoc();
$total_revenue = $row_revenue['total'] ?? 0;

// 2. Total Transaksi
$sql_trans = "SELECT COUNT(*) as total FROM table_penjualan";
$res_trans = $conn->query($sql_trans);
$row_trans = $res_trans->fetch_assoc();
$total_trans = $row_trans['total'];

// 3. Transaksi Pending
$sql_pending = "SELECT COUNT(*) as total FROM table_penjualan WHERE status_pembayaran = 'Pending'";
$res_pending = $conn->query($sql_pending);
$row_pending = $res_pending->fetch_assoc();
$total_pending = $row_pending['total'];

// 4. Total Lisensi Terjual (Lunas)
$sql_license = "SELECT SUM(jumlah_lisensi) as total FROM table_penjualan WHERE status_pembayaran = 'Lunas'";
$res_license = $conn->query($sql_license);
$row_license = $res_license->fetch_assoc();
$total_license = $row_license['total'] ?? 0;

// 5. 5 Transaksi Terakhir
$sql_recent = "SELECT * FROM table_penjualan ORDER BY id DESC LIMIT 5";
$res_recent = $conn->query($sql_recent);

// 6. Data Grafik Penjualan
$range = isset($_GET['range']) && $_GET['range'] == '30' ? 30 : 7;
$sql_chart = "SELECT tanggal_transaksi, SUM(harga) as total 
              FROM table_penjualan 
              WHERE status_pembayaran = 'Lunas' 
              GROUP BY tanggal_transaksi 
              ORDER BY tanggal_transaksi DESC 
              LIMIT $range";
$res_chart = $conn->query($sql_chart);

$chart_dates = [];
$chart_totals = [];

while ($row_c = $res_chart->fetch_assoc()) {
    $chart_dates[] = date('d M', strtotime($row_c['tanggal_transaksi']));
    $chart_totals[] = $row_c['total'];
}

// Reverse array agar urutan tanggal dari kiri ke kanan (lama ke baru)
$chart_dates = array_reverse($chart_dates);
$chart_totals = array_reverse($chart_totals);

// 7. Data Pie Chart (Distribusi Software)
$sql_pie = "SELECT nama_software, COUNT(*) as total FROM table_penjualan GROUP BY nama_software";
$res_pie = $conn->query($sql_pie);
$pie_labels = [];
$pie_data = [];
while ($row_p = $res_pie->fetch_assoc()) {
    $pie_labels[] = $row_p['nama_software'];
    $pie_data[] = $row_p['total'];
}

// 8. Data Grafik Penjualan Bulanan (Tahun Ini)
$sql_monthly = "SELECT MONTHNAME(tanggal_transaksi) as bulan, SUM(harga) as total 
                FROM table_penjualan 
                WHERE status_pembayaran = 'Lunas' AND YEAR(tanggal_transaksi) = YEAR(CURDATE())
                GROUP BY MONTH(tanggal_transaksi) 
                ORDER BY MONTH(tanggal_transaksi) ASC";
$res_monthly = $conn->query($sql_monthly);

$monthly_labels = [];
$monthly_data = [];
while ($row_m = $res_monthly->fetch_assoc()) {
    $monthly_labels[] = $row_m['bulan'];
    $monthly_data[] = $row_m['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * { box-sizing: border-box; }

        body {
            font-family: 'JetBrains Mono', monospace;
            background: #0f0f23;
            background-attachment: fixed;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-align: center;
            text-shadow: 0 0 20px rgba(80, 250, 123, 0.2);
        }

        .welcome-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            color: #bd93f9;
            margin-bottom: 40px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            text-align: center;
            transition: transform 0.3s, border-color 0.3s;
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, #50fa7b, #ffb86c);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #50fa7b;
            box-shadow: 0 15px 30px rgba(80, 250, 123, 0.1);
        }

        .stat-number {
            font-size: 2.2em;
            font-weight: 700;
            margin: 15px 0;
            color: #f8f8f2;
        }

        .stat-label {
            color: #bd93f9;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .icon { font-size: 2em; margin-bottom: 10px; display: block; }

        /* Chart Section */
        .charts-row {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }

        .chart-section {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            flex: 1;
            min-height: 400px;
            animation: fadeInUp 0.8s ease-out;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .main-chart { flex: 2; }

        @media (max-width: 900px) {
            .charts-row { flex-direction: column; }
        }

        .btn-filter {
            padding: 6px 12px;
            background: #2d2d42;
            color: #e2e8f0;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.8em;
            border: 1px solid rgba(99, 102, 241, 0.3);
            margin-left: 5px;
            transition: all 0.2s;
        }

        .btn-filter:hover {
            background: #3a3a52;
            border-color: #bd93f9;
        }

        .btn-filter.active {
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            border-color: #50fa7b;
            font-weight: bold;
            box-shadow: 0 0 10px rgba(80, 250, 123, 0.3);
        }

        /* Recent Transactions Table */
        .recent-section {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeInUp 0.8s ease-out 0.2s backwards;
        }

        h2 {
            color: #ffb86c;
            margin-top: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        th {
            color: #50fa7b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
        }

        tr:last-child td { border-bottom: none; }
        
        /* Status Badges Consistent with TampilData */
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 0.75em; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        .status-lunas { background: rgba(80, 250, 123, 0.15); color: #50fa7b; border: 1px solid rgba(80, 250, 123, 0.3); }
        .status-pending { background: rgba(255, 184, 108, 0.15); color: #ffb86c; border: 1px solid rgba(255, 184, 108, 0.3); }
        .status-batal { background: rgba(255, 85, 85, 0.15); color: #ff5555; border: 1px solid rgba(255, 85, 85, 0.3); }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 40px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        .btn-primary { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; }
        .btn-primary:hover { box-shadow: 0 0 15px rgba(80, 250, 123, 0.4); }

        .btn-secondary { background: rgba(45, 45, 66, 0.8); color: #e2e8f0; border: 1px solid rgba(99, 102, 241, 0.5); }
        .btn-secondary:hover { background: #3a3a52; border-color: #bd93f9; }

        .btn-danger { background: linear-gradient(135deg, #ff5555, #ff4444); color: white; }

        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(30, 30, 46, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            z-index: 1000;
            padding: 0;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            width: 100%;
            position: relative;
        }

        .navbar .logo {
            color: #50fa7b;
            text-decoration: none;
            font-size: 1.5em;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(80, 250, 123, 0.5);
        }

        .navbar .hamburger {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            margin-left: 15px;
        }

        .navbar .hamburger span {
            width: 25px;
            height: 3px;
            background: #e2e8f0;
            margin: 3px 0;
            transition: 0.3s;
        }

        .navbar .nav-links {
            display: flex;
            position: absolute;
            top: 100%;
            right: 20px;
            width: 250px;
            background: rgba(30, 30, 46, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            flex-direction: column;
            padding: 1rem;
            gap: 0.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar .nav-links.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .navbar .nav-links a {
            color: #e2e8f0;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 6px;
            transition: 0.3s;
            font-weight: 500;
            display: block;
        }

        .navbar .nav-links a:hover {
            background: rgba(80, 250, 123, 0.1);
            color: #50fa7b;
        }

        .navbar .nav-links .btn-login {
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            font-weight: bold;
        }

        .navbar .nav-links .btn-login:hover {
            box-shadow: 0 0 15px rgba(80, 250, 123, 0.5);
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }

        /* Adjust body padding for fixed navbar */
        body {
            padding-top: 80px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom Button Colors */
        .btn-coupon { background: linear-gradient(135deg, #8be9fd, #50fa7b); color: #0f0f23; }
        .btn-add-user { background: linear-gradient(135deg, #8be9fd, #bd93f9); color: #0f0f23; }
        .btn-manage-user { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; }
        .btn-log { background: linear-gradient(135deg, #ff79c6, #ff5555); color: #0f0f23; }
        .btn-settings { background: linear-gradient(135deg, #6272a4, #44475a); color: #f8f8f2; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="logo">Dashboard Admin</a>
            <div class="navbar-right">
                <a href="logout.php" class="btn-login" style="padding: 8px 16px; border-radius: 8px; text-decoration: none; color: white; font-weight: bold;">Logout</a>
                <div class="hamburger" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="nav-links">
                <a href="tampilDataPenjualan.php">Data Penjualan</a>
                <a href="tambahDataPenjualan.php">Input Transaksi</a>
                <a href="tampilDataKupon.php">Kupon & Diskon</a>
                <a href="tampilDataUser.php">Kelola User</a>
                <a href="tampilLogAktivitas.php">Log Aktivitas</a>
                <a href="laporanBulanan.php">Laporan</a>
                <a href="settings.php">Settings</a>
            </div>
        </div>
    </nav>

<div class="container">
    <h1>Dashboard Penjualan</h1>
    <div class="welcome-text">
        <?php 
        $pp = !empty($user_data) && !empty($user_data['profile_picture']) ? $user_data['profile_picture'] : 'https://ui-avatars.com/api/?name='.urlencode($username).'&background=50fa7b&color=0f0f23';
        ?>
        <img src="<?= $pp ?>" alt="Profile" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #50fa7b;">
        <span>Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! Berikut ringkasan performa penjualan Anda.</span>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="icon">💰</span>
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-number">Rp <?= number_format($total_revenue, 0, ',', '.') ?></div>
        </div>
        <div class="stat-card">
            <span class="icon">🧾</span>
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-number"><?= $total_trans ?></div>
        </div>
        <div class="stat-card">
            <span class="icon">⏳</span>
            <div class="stat-label">Menunggu Pembayaran</div>
            <div class="stat-number" style="color: #ffb86c;"><?= $total_pending ?></div>
        </div>
        <div class="stat-card">
            <span class="icon">🔑</span>
            <div class="stat-label">Produk Terjual</div>
            <div class="stat-number"><?= $total_license ?></div>
        </div>

    </div>

    <div class="charts-row">
        <div class="chart-section main-chart">
            <div class="chart-header">
                <h2 style="border: none; padding: 0; margin: 0;">📈 Tren Pendapatan</h2>
                <div>
                    <a href="?range=7" class="btn-filter <?= $range == 7 ? 'active' : '' ?>">7 Hari</a>
                    <a href="?range=30" class="btn-filter <?= $range == 30 ? 'active' : '' ?>">30 Hari</a>
                </div>
            </div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="chart-section">
            <h2 style="border: none; padding: 0; margin: 0 0 20px 0;">🍰 Distribusi Software</h2>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <div class="charts-row">
        <div class="chart-section" style="flex: 1;">
             <h2 style="border: none; padding: 0; margin: 0 0 20px 0;">📊 Penjualan Bulanan (Tahun Ini)</h2>
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="recent-section">
        <h2>📝 5 Transaksi Terakhir</h2>
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Pembeli</th><th>Software</th><th>Harga</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php while($row = $res_recent->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['tanggal_transaksi'] ?></td>
                    <td><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                    <td><?= htmlspecialchars($row['nama_software']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><span class="status-badge status-<?= strtolower($row['status_pembayaran']) ?>"><?= $row['status_pembayaran'] ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleMenu() {
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
    }

    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_dates) ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode($chart_totals) ?>,
                borderColor: '#50fa7b',
                backgroundColor: 'rgba(80, 250, 123, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#ffb86c',
                pointBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#e2e8f0', font: { family: "'JetBrains Mono', monospace" } } }
            },
            scales: {
                y: {
                    ticks: { color: '#bd93f9', font: { family: "'JetBrains Mono', monospace" } },
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }
                },
                x: {
                    ticks: { color: '#bd93f9', font: { family: "'JetBrains Mono', monospace" } },
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }
                }
            }
        }
    });

    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($pie_labels) ?>,
            datasets: [{
                data: <?= json_encode($pie_data) ?>,
                backgroundColor: ['#50fa7b', '#ffb86c', '#bd93f9', '#ff79c6', '#8be9fd', '#ff5555'],
                borderColor: '#1e1e2e',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#e2e8f0', font: { family: "'JetBrains Mono', monospace" } } }
            }
        }
    });

    const ctxMonthly = document.getElementById('monthlySalesChart').getContext('2d');
    const monthlySalesChart = new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: <?= json_encode($monthly_labels) ?>,
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: <?= json_encode($monthly_data) ?>,
                backgroundColor: 'rgba(189, 147, 249, 0.6)',
                borderColor: '#bd93f9',
                borderWidth: 2,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Performa Penjualan per Bulan (Tahun <?= date('Y') ?>)',
                    color: '#e2e8f0',
                    font: { family: "'JetBrains Mono', monospace", size: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#bd93f9', font: { family: "'JetBrains Mono', monospace" } },
                    grid: { color: 'rgba(255, 255, 255, 0.05)' }
                },
                x: { ticks: { color: '#bd93f9', font: { family: "'JetBrains Mono', monospace" } }, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
            }
        }
    });
</script>

</body>
</html>
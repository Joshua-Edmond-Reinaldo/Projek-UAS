<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Query Penjualan Bulanan
$sql_jual = "SELECT * FROM table_penjualan 
             WHERE MONTH(tanggal_transaksi) = '$bulan' 
             AND YEAR(tanggal_transaksi) = '$tahun' 
             AND status_pembayaran = 'Lunas'";
$res_jual = $conn->query($sql_jual);

// Hitung Total Penjualan
$total_penjualan = 0;
$data_jual = [];
while ($row = $res_jual->fetch_assoc()) {
    $total_penjualan += $row['harga'];
    $data_jual[] = $row;
}

// Query Pembelian Bulanan
$sql_beli = "SELECT * FROM table_pembelian 
             WHERE MONTH(tanggal_pembelian) = '$bulan' 
             AND YEAR(tanggal_pembelian) = '$tahun'";
$res_beli = $conn->query($sql_beli);

// Hitung Total Pembelian
$total_pembelian = 0;
$data_beli = [];
while ($row = $res_beli->fetch_assoc()) {
    $total_pembelian += $row['harga_beli'];
    $data_beli[] = $row;
}

$laba_bersih = $total_penjualan - $total_pembelian;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        h1 { color: #50fa7b; text-align: center; margin-bottom: 30px; }
        .filter-form {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
            background: rgba(0,0,0,0.2);
            padding: 20px;
            border-radius: 12px;
        }
        select, button {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #44475a;
            background: #2d2d42;
            color: #f8f8f2;
            font-family: inherit;
        }
        button {
            background: #bd93f9;
            color: #0f0f23;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card h3 { margin: 0 0 10px 0; color: #8be9fd; font-size: 1em; }
        .card .amount { font-size: 1.8em; font-weight: bold; color: #f8f8f2; }
        .profit { color: #50fa7b !important; }
        .loss { color: #ff5555 !important; }
        
        .btn-print {
            display: block;
            width: 200px;
            margin: 0 auto;
            text-align: center;
            background: #ff79c6;
            color: #0f0f23;
            padding: 15px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
        }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6272a4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Laporan Bulanan</h1>
        
        <form class="filter-form" method="GET">
            <select name="bulan">
                <?php
                for ($i = 1; $i <= 12; $i++) {
                    $selected = ($i == $bulan) ? 'selected' : '';
                    echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 10)) . "</option>";
                }
                ?>
            </select>
            <select name="tahun">
                <?php
                for ($i = date('Y'); $i >= 2020; $i--) {
                    $selected = ($i == $tahun) ? 'selected' : '';
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>
            <button type="submit">Tampilkan</button>
        </form>

        <div class="summary-cards">
            <div class="card">
                <h3>Total Penjualan (Masuk)</h3>
                <div class="amount" style="color: #50fa7b;">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></div>
            </div>
            <div class="card">
                <h3>Total Pembelian (Keluar)</h3>
                <div class="amount" style="color: #ff5555;">Rp <?= number_format($total_pembelian, 0, ',', '.') ?></div>
            </div>
            <div class="card">
                <h3>Laba Bersih</h3>
                <div class="amount <?= $laba_bersih >= 0 ? 'profit' : 'loss' ?>">
                    Rp <?= number_format($laba_bersih, 0, ',', '.') ?>
                </div>
            </div>
        </div>

        <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 30px;">
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="labaRugiChart"></canvas>
            </div>
        </div>

        <a href="cetakLaporanBulananPdf.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" target="_blank" class="btn-print">🖨️ Cetak PDF</a>
        <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
    </div>

    <script>
        const ctx = document.getElementById('labaRugiChart').getContext('2d');
        const labaRugiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Pemasukan (Penjualan)', 'Pengeluaran (Pembelian)'],
                datasets: [{
                    label: 'Total (Rp)',
                    data: [<?= $total_penjualan ?>, <?= $total_pembelian ?>],
                    backgroundColor: [
                        'rgba(80, 250, 123, 0.6)', // Hijau untuk Pemasukan
                        'rgba(255, 85, 85, 0.6)'   // Merah untuk Pengeluaran
                    ],
                    borderColor: [
                        '#50fa7b',
                        '#ff5555'
                    ],
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Grafik Perbandingan Keuangan',
                        color: '#e2e8f0',
                        font: { size: 16, family: "'JetBrains Mono', monospace" }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#bd93f9', font: { family: "'JetBrains Mono', monospace" } },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        ticks: { color: '#bd93f9', font: { family: "'JetBrains Mono', monospace" } },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>
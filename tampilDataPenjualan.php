<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Proteksi: Redirect customer ke dashboard mereka sendiri
if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("location:dashboardCustomer.php");
    exit;
}

// Cek Level User
$isAdmin = (isset($_SESSION['level']) && $_SESSION['level'] == 'admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan Software</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 177, 153, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(80, 250, 123, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 20px;
            box-shadow:
                0 20px 40px rgba(0,0,0,0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            backdrop-filter: blur(10px);
            overflow: hidden;
            position: relative;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #50fa7b, #ffb86c, #bd93f9, #ff79c6);
            border-radius: 20px 20px 0 0;
        }

        h1 {
            background: linear-gradient(145deg, #0f0f23, #1a1a2e);
            color: transparent;
            background-clip: text;
            -webkit-background-clip: text;
            background-image: linear-gradient(135deg, #50fa7b, #ffb86c);
            margin: 0;
            padding: 40px;
            text-align: center;
            font-size: 2.8em;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(80, 250, 123, 0.3);
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
            position: relative;
        }

        h1::before {
            content: '💻';
            position: absolute;
            left: 40px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2em;
            color: #e2e8f0;
        }

        .table-container {
            padding: 30px;
            overflow: auto;
            max-height: 70vh;
            animation: fadeInUp 0.6s ease-out;
        }

        .table-container::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #1e1e2e;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            border-radius: 10px;
            border: 2px solid #1e1e2e;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #40e66b, #ff9f43);
        }

        .table-container::-webkit-scrollbar-corner {
            background: #1e1e2e;
        }

        table {
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
            margin-top: 20px;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
            vertical-align: middle;
        }

        th {
            background: linear-gradient(135deg, #2d2d42, #3a3a52);
            color: #50fa7b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85em;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        tr:nth-child(even) {
            background: linear-gradient(145deg, #1a1a2e, #2a2a3e);
        }

        tr:hover {
            background: linear-gradient(145deg, #2d2d42, #3a3a52);
            transform: scale(1.01);
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(80, 250, 123, 0.1);
        }

        td {
            color: #e2e8f0;
            font-size: 0.9em;
            transition: color 0.2s ease;
        }

        tr:hover td {
            color: #f8f8f2;
        }

        .no-data {
            text-align: center;
            padding: 60px 40px;
            color: #ffb86c;
            font-size: 1.3em;
            background: linear-gradient(145deg, #1a1a2e, #2a2a3e);
            border-radius: 12px;
            margin: 20px 0;
            border: 1px solid rgba(255, 184, 108, 0.2);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .actions {
            text-align: center;
            padding: 30px;
            background: linear-gradient(145deg, #0f0f23, #1a1a2e);
            border-top: 1px solid rgba(99, 102, 241, 0.2);
        }

        .btn {
            display: inline-block;
            padding: 16px 32px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(80, 250, 123, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }

        .btn:hover {
            background: linear-gradient(135deg, #40e66b, #50fa7b);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(80, 250, 123, 0.4);
        }

        .btn-logout { background: linear-gradient(135deg, #ff5555, #ff4444); color: #ffffff; margin-left: 15px; }
        .btn-print { background: linear-gradient(135deg, #bd93f9, #ff79c6); color: #0f0f23; margin-left: 15px; }
        .btn-edit { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; padding: 8px 16px; border-radius: 6px; font-size: 0.85em; }
        .btn-delete { background: linear-gradient(135deg, #ff5555, #ff4444); color: #ffffff; padding: 8px 16px; border-radius: 6px; font-size: 0.85em; }

        .search-container { margin-bottom: 25px; display: flex; justify-content: center; gap: 15px; }
        .search-input { padding: 14px 20px; width: 100%; max-width: 400px; background: #2d2d42; border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 12px; color: #f8f8f2; }
        .btn-search { padding: 14px 24px; background: linear-gradient(135deg, #8be9fd, #50fa7b); color: #0f0f23; border: none; border-radius: 12px; cursor: pointer; font-weight: bold; }
        .btn-reset { padding: 14px 24px; background: linear-gradient(135deg, #ff5555, #ffb86c); color: #0f0f23; text-decoration: none; border-radius: 12px; font-weight: bold; }

        /* Status Badges */
        .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.8em; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; min-width: 80px; text-align: center; }
        .status-lunas { background: rgba(80, 250, 123, 0.15); color: #50fa7b; border: 1px solid rgba(80, 250, 123, 0.3); box-shadow: 0 0 10px rgba(80, 250, 123, 0.1); }
        .status-pending { background: rgba(255, 184, 108, 0.15); color: #ffb86c; border: 1px solid rgba(255, 184, 108, 0.3); box-shadow: 0 0 10px rgba(255, 184, 108, 0.1); }
        .status-batal { background: rgba(255, 85, 85, 0.15); color: #ff5555; border: 1px solid rgba(255, 85, 85, 0.3); box-shadow: 0 0 10px rgba(255, 85, 85, 0.1); }

        .btn-dashboard { background: linear-gradient(135deg, #8be9fd, #bd93f9); color: #0f0f23; margin-right: 15px; }
        .pagination { display: flex; justify-content: center; margin-top: 20px; gap: 8px; }
        .pagination a { color: #e2e8f0; padding: 8px 14px; text-decoration: none; background: #2d2d42; border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 8px; }
        .pagination a.active { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; border-color: #50fa7b; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data Penjualan</h1>
        
        <div class="search-container">
            <form action="" method="GET" style="display: flex; gap: 10px; width: 100%; justify-content: center; flex-wrap: wrap;">
                <input type="text" name="cari" class="search-input" placeholder="Cari Username, Pembeli, atau Software..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
                <button type="submit" class="btn-search">🔍 Cari</button>
                <?php if(isset($_GET['cari'])): ?>
                    <a href="tampilDataPenjualan.php" class="btn-reset">✖ Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <?php
            include 'koneksi.php';

            $jumlahDataPerHalaman = 5;
            $halamanAktif = (isset($_GET['halaman']) && (int)$_GET['halaman'] > 0) ? (int)$_GET['halaman'] : 1;
            $awalData = ($jumlahDataPerHalaman * $halamanAktif) - $jumlahDataPerHalaman;

            if (isset($_GET['cari'])) {
                $cari = mysqli_real_escape_string($conn, $_GET['cari']);
                $where = "WHERE username LIKE '%$cari%' OR nama_pembeli LIKE '%$cari%' OR nama_software LIKE '%$cari%'";
            } else {
                $where = "";
            }

            $sql_total = "SELECT COUNT(*) as total FROM table_penjualan $where";
            $result_total = $conn->query($sql_total);
            $row_total = $result_total->fetch_assoc();
            $jumlahData = $row_total['total'];
            $jumlahHalaman = ceil($jumlahData / $jumlahDataPerHalaman);

            $sql = "SELECT * FROM table_penjualan $where LIMIT $awalData, $jumlahDataPerHalaman";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table><thead><tr>
                    <th>No</th><th>Username</th><th>Nama Pembeli</th><th>Software</th><th>Lisensi</th>
                    <th>Tgl Transaksi</th><th>Harga</th><th>Status</th><th>Aksi</th>
                </tr></thead><tbody>";

                $no = $awalData + 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td style='text-align:center;'>{$no}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['nama_pembeli']}</td>
                        <td>{$row['nama_software']}</td>
                        <td style='text-align:center;'>{$row['jumlah_lisensi']}</td>
                        <td style='text-align:center;'>{$row['tanggal_transaksi']}</td>
                        <td style='text-align:right;'>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                        <td style='text-align:center;'><span class='status-badge status-" . strtolower($row['status_pembayaran']) . "'>{$row['status_pembayaran']}</span></td>
                        <td style='text-align:center; white-space:nowrap;'>";
                
                // Hanya Admin yang boleh Edit dan Hapus
                if ($isAdmin) {
                    echo "<a href='koreksiDataPenjualan.php?kode={$row['id']}' class='btn-edit'>Edit</a> ";
                    echo "<a href='hapusDataPenjualan.php?kode={$row['id']}' onclick=\"return confirm('Yakin hapus transaksi ini?')\" class='btn-delete'>Hapus</a>";
                } else {
                    echo "<span style='color:#6272a4; font-size:0.8em;'>View Only</span>";
                }

                echo "</td>
                    </tr>";
                    $no++;
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='no-data'>📭 Belum ada data penjualan yang tersimpan.</div>";
            }
            ?>
        </div>

        <!-- Pagination -->
        <?php if ($jumlahHalaman > 1): ?>
        <div class="pagination">
            <?php if ($halamanAktif > 1): ?>
                <a href="?halaman=<?= $halamanAktif - 1 ?><?= isset($_GET['cari']) ? '&cari='.$_GET['cari'] : '' ?>">&laquo; Prev</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $jumlahHalaman; $i++): ?>
                <a href="?halaman=<?= $i ?><?= isset($_GET['cari']) ? '&cari='.$_GET['cari'] : '' ?>" class="<?= ($i == $halamanAktif) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($halamanAktif < $jumlahHalaman): ?>
                <a href="?halaman=<?= $halamanAktif + 1 ?><?= isset($_GET['cari']) ? '&cari='.$_GET['cari'] : '' ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-dashboard">🏠 Dashboard</a>
            <a href="tambahDataPenjualan.php" class="btn">➕ Input Transaksi Baru</a>
            <a href="cetakDataPenjualanPdf.php" target="_blank" class="btn btn-print">🖨️ Cetak PDF</a>
            <a href="logout.php" class="btn btn-logout">🚪 Logout</a>
        </div>
    </div>
</body>
</html>
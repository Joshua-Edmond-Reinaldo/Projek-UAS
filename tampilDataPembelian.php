<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian dari Supplier</title>
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
            max-width: 1100px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            overflow: hidden;
        }
        h1 {
            background-image: linear-gradient(135deg, #8be9fd, #bd93f9);
            color: transparent;
            background-clip: text;
            -webkit-background-clip: text;
            padding: 40px;
            text-align: center;
            font-size: 2.5em;
            margin: 0;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        }
        .table-container { padding: 30px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid rgba(99, 102, 241, 0.1); }
        th { background: #2d2d42; color: #8be9fd; text-align: center; }
        tr:hover { background: #2d2d42; }
        .btn { display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .btn-add { background: #8be9fd; color: #0f0f23; }
        .btn-delete { background: #ff5555; color: #fff; padding: 6px 12px; font-size: 0.9em; }
        .actions { padding: 30px; text-align: center; border-top: 1px solid rgba(99, 102, 241, 0.2); }
        .btn-dashboard { background: #50fa7b; color: #0f0f23; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚚 Riwayat Pembelian dari Supplier</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Supplier</th>
                        <th>Nama Software</th>
                        <th>Jumlah</th>
                        <th>Harga Beli</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'koneksi.php';
                    $sql = "SELECT * FROM table_pembelian ORDER BY tanggal_pembelian DESC";
                    $result = $conn->query($sql);
                    $no = 1;
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td style='text-align:center;'>{$no}</td>
                                <td style='text-align:center;'>{$row['tanggal_pembelian']}</td>
                                <td>{$row['nama_supplier']}</td>
                                <td>{$row['nama_software']}</td>
                                <td style='text-align:center;'>{$row['jumlah_lisensi']}</td>
                                <td style='text-align:right;'>Rp " . number_format($row['harga_beli'], 0, ',', '.') . "</td>
                                <td style='text-align:center;'>
                                    <a href='hapusDataPembelian.php?id={$row['id']}' onclick=\"return confirm('Yakin hapus data pembelian ini? Stok akan dikurangi.')\" class='btn btn-delete'>Hapus</a>
                                </td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data pembelian.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="actions">
            <a href="tambahDataPembelian.php" class="btn btn-add">➕ Input Pembelian Baru</a>
            <a href="dashboard.php" class="btn btn-dashboard" style="margin-left: 15px;">🏠 Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
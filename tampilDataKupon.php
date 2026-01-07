<?php
// c:\xampp\htdocs\Projek UAS\tampilDataKupon.php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}
require "koneksi.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kode Promo</title>
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
            max-width: 1100px; /* Lebarkan container */
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 1px solid rgba(99, 102, 241, 0.2);
            padding: 30px;
        }
        h1 {
            background: linear-gradient(135deg, #8be9fd, #50fa7b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            margin-bottom: 30px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        th { background: #2d2d42; color: #50fa7b; }
        tr:hover { background: rgba(255,255,255,0.05); }
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; color: #0f0f23; transition: 0.3s; display: inline-block; }
        .btn-add { background: #50fa7b; padding: 12px 24px; margin-bottom: 20px; }
        .btn-edit { background: #ffb86c; font-size: 0.9em; }
        .btn-delete { background: #ff5555; color: white; font-size: 0.9em; }
        .btn-back { background: #6272a4; color: white; margin-top: 20px; display: block; text-align: center; width: 200px; margin-left: auto; margin-right: auto; }
        .status-active { color: #50fa7b; }
        .status-expired { color: #ff5555; }
        .limit-yes { color: #ffb86c; font-weight: bold; }
        .limit-no { color: #6272a4; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎟️ Kelola Kode Promo</h1>
        <a href="tambahDataKupon.php" class="btn btn-add">➕ Tambah Kupon Baru</a>
        
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tipe</th>
                    <th>Nilai</th>
                    <th>Limit 1x/User</th>
                    <th>Berlaku Sampai</th>
                    <th>Limit/Terpakai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM table_coupons ORDER BY created_at DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $is_expired = ($row['valid_until'] && $row['valid_until'] < date('Y-m-d'));
                        $status_class = $is_expired ? 'status-expired' : 'status-active';
                        $nilai = ($row['type'] == 'percentage') ? intval($row['value']) . '%' : 'Rp ' . number_format($row['value'], 0, ',', '.');
                        $limit_user_text = $row['limit_per_user'] ? "<span class='limit-yes'>Ya</span>" : "<span class='limit-no'>Tidak</span>";
                        
                        echo "<tr>
                            <td style='font-weight:bold; color:#fff;'>{$row['code']}</td>
                            <td>" . ucfirst($row['type']) . "</td>
                            <td>{$nilai}</td>
                            <td style='text-align:center;'>{$limit_user_text}</td>
                            <td class='{$status_class}'>" . ($row['valid_until'] ? $row['valid_until'] : 'Selamanya') . "</td>
                            <td>{$row['usage_limit']} / {$row['used_count']}</td>
                            <td>
                                <a href='detailKupon.php?id={$row['id']}' class='btn' style='background: #8be9fd; font-size: 0.9em; margin-right: 5px;'>Detail</a>
                                <a href='koreksiDataKupon.php?id={$row['id']}' class='btn btn-edit'>Edit</a>
                                <a href='hapusDataKupon.php?id={$row['id']}' onclick=\"return confirm('Yakin hapus kupon ini?')\" class='btn btn-delete'>Hapus</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding:20px;'>Belum ada kupon.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-back">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
<?php
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
    <title>Data User Terdaftar</title>
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
            max-width: 1200px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 1px solid rgba(99, 102, 241, 0.2);
            overflow: hidden;
            padding-bottom: 30px;
        }
        h1 {
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            padding: 30px;
            margin: 0;
            font-size: 2.5em;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        }
        .search-container {
            padding: 20px;
            text-align: center;
        }
        .search-input {
            padding: 12px;
            width: 300px;
            border-radius: 8px;
            border: 1px solid #44475a;
            background: #2d2d42;
            color: #fff;
        }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; color: #0f0f23; display: inline-block; transition: 0.3s; border: none; cursor: pointer; }
        .btn-search { background: #8be9fd; }
        .btn-delete { background: #ff5555; color: white; font-size: 0.8em; padding: 6px 12px; }
        .btn-back { background: #6272a4; color: white; margin-top: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { background: #2d2d42; color: #50fa7b; text-transform: uppercase; font-size: 0.9em; text-align: center; }
        tr:hover { background: rgba(255,255,255,0.02); }
        td { text-align: center; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8em; font-weight: bold; }
        .badge-admin { background: rgba(189, 147, 249, 0.2); color: #bd93f9; }
        .badge-user { background: rgba(80, 250, 123, 0.2); color: #50fa7b; }

        .pagination { display: flex; justify-content: center; margin-top: 20px; gap: 5px; }
        .pagination a { padding: 8px 12px; background: #2d2d42; color: #e2e8f0; text-decoration: none; border-radius: 5px; }
        .pagination a.active { background: #50fa7b; color: #0f0f23; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Daftar User Terdaftar</h1>
        
        <div class="search-container">
            <form method="GET">
                <input type="text" name="cari" class="search-input" placeholder="Cari Username atau Email..." value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
                <button type="submit" class="btn btn-search">Cari</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Nama Lengkap</th>
                    <th>Tgl Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $limit = 10;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $start = ($page - 1) * $limit;
                
                $where = "";
                if (isset($_GET['cari'])) {
                    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
                    $where = "WHERE u.username LIKE '%$cari%' OR u.email LIKE '%$cari%'";
                }

                // Query Total Data
                $sql_count = "SELECT COUNT(*) as total FROM table_user u $where";
                $res_count = $conn->query($sql_count);
                $total_data = $res_count->fetch_assoc()['total'];
                $total_pages = ceil($total_data / $limit);

                // Query Data User + Join ke Penjualan untuk ambil Nama Lengkap & Tgl Daftar (dari transaksi profil)
                // Kita ambil transaksi dengan nama_software = '-' yang dibuat saat register
                $sql = "SELECT u.id, u.username, u.email, u.level, 
                               COALESCE(p.nama_pembeli, '-') as nama_lengkap, 
                               COALESCE(p.tanggal_transaksi, '-') as tgl_daftar
                        FROM table_user u
                        LEFT JOIN table_penjualan p ON u.username = p.username AND p.nama_software = '-'
                        $where
                        ORDER BY u.id DESC 
                        LIMIT $start, $limit";
                
                $result = $conn->query($sql);
                $no = $start + 1;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $level_badge = ($row['level'] == 'admin') ? 'badge-admin' : 'badge-user';
                        echo "<tr>
                            <td>{$no}</td>
                            <td style='font-weight:bold; color:#fff;'>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td><span class='badge {$level_badge}'>" . strtoupper($row['level']) . "</span></td>
                            <td>{$row['nama_lengkap']}</td>
                            <td>{$row['tgl_daftar']}</td>
                            <td>";
                        
                        // Cegah admin menghapus dirinya sendiri
                        if ($row['username'] != $_SESSION['username']) {
                            echo "<a href='hapusUser.php?id={$row['id']}' onclick=\"return confirm('Yakin hapus user ini? User tidak akan bisa login lagi.')\" class='btn btn-delete'>Hapus</a>";
                        } else {
                            echo "<span style='color:#6272a4; font-size:0.8em;'>Current User</span>";
                        }
                        
                        echo "</td></tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding:30px;'>Tidak ada data user.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?><?= isset($_GET['cari']) ? '&cari='.$_GET['cari'] : '' ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="dashboard.php" class="btn btn-back">← Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
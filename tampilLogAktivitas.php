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
    <title>Log Aktivitas User</title>
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
            max-width: 1200px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            animation: fadeIn 0.6s ease-out;
        }
        h1 { color: #ff79c6; text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        th { background: #2d2d42; color: #ff79c6; }
        tr:hover { background: rgba(255, 255, 255, 0.05); }
        .btn-back { display: inline-block; margin-top: 20px; color: #8be9fd; text-decoration: none; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📜 Log Aktivitas User</h1>
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Username</th>
                    <th>Aksi</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM table_logs ORDER BY timestamp DESC LIMIT 100";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['timestamp']}</td>
                        <td style='color: #50fa7b;'>{$row['username']}</td>
                        <td style='font-weight:bold;'>{$row['action']}</td>
                        <td>{$row['details']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
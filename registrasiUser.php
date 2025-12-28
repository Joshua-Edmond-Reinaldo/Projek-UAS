<?php
session_start();
// Cek apakah user login dan levelnya admin
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi User Baru</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        h1 { color: #50fa7b; text-align: center; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; }
        input, select { width: 100%; padding: 12px; background: #2d2d42; border: 1px solid #44475a; border-radius: 8px; color: #f8f8f2; margin-bottom: 20px; font-family: inherit; }
        input:focus, select:focus { border-color: #50fa7b; outline: none; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3); }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #8be9fd; text-decoration: none; }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👤 Registrasi User</h1>
        <form action="simpanUser.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Username baru">
            
            <label for="email">Email (Untuk Reset Password)</label>
            <input type="email" id="email" name="email" required placeholder="email@contoh.com">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Password">
            
            <label for="level">Level Akses</label>
            <select id="level" name="level" required>
                <option value="user">User (Staff)</option>
                <option value="admin">Admin</option>
            </select>
            
            <button type="submit" class="btn">Daftarkan User</button>
        </form>
        <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
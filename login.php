<?php
session_start();
require "koneksi.php";

// Cek Cookie untuk Auto-Login (Remember Me)
if (!isset($_SESSION['username']) && isset($_COOKIE['remember_user'])) {
    $cookie_user = mysqli_real_escape_string($conn, $_COOKIE['remember_user']);
    // Cek validitas user dari cookie
    $sql_check = "SELECT * FROM table_user WHERE username = '$cookie_user'";
    $res_check = $conn->query($sql_check);
    if ($res_check && $res_check->num_rows > 0) {
        $row = $res_check->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['level'] = $row['level'];
        
        if ($row['level'] == 'admin') {
            header("location:dashboard.php");
        } else {
            header("location:dashboardCustomer.php");
        }
        exit;
    }
}

if (isset($_SESSION['username'])) {
    if (isset($_SESSION['level']) && $_SESSION['level'] == 'admin') {
        header("location:dashboard.php");
    } else {
        header("location:dashboardCustomer.php");
    }
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM table_user WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Catatan: Untuk keamanan produksi, gunakan password_verify() dan hash password di database
        if ($password == $row['password']) {
            $_SESSION['username'] = $username;
            $_SESSION['level'] = $row['level']; // Simpan level (admin/user)
            
            // Fitur Remember Me: Set Cookie jika dicentang
            if (isset($_POST['remember'])) {
                setcookie('remember_user', $username, time() + (86400 * 30), "/"); // Berlaku 30 hari
            }
            
            // Catat Log Login
            catatLog($conn, $username, 'Login', 'User berhasil login ke sistem');

            if ($row['level'] == 'admin') {
                header("location:dashboard.php");
            } else {
                header("location:dashboardCustomer.php");
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 { color: #50fa7b; margin-bottom: 30px; }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #2d2d42;
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 8px;
            color: white;
            font-family: inherit;
            box-sizing: border-box;
        }
        input:focus { border-color: #50fa7b; outline: none; }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            border: none;
            border-radius: 8px;
            color: #0f0f23;
            font-weight: bold;
            cursor: pointer;
            font-family: inherit;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3); }
        .error { color: #ff5555; margin-bottom: 15px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>🔐 Login Access</h2>
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <div style="text-align: left; margin: 10px 0; color: #bd93f9; font-size: 0.9em; display: flex; align-items: center;">
                <input type="checkbox" name="remember" id="remember" style="width: auto; margin: 0 10px 0 0;">
                <label for="remember" style="cursor: pointer;">Ingat Saya</label>
            </div>
            <button type="submit">LOGIN</button>
        </form>
        <div style="margin-top: 15px;">
            <a href="lupaPassword.php" style="color: #bd93f9; text-decoration: none; font-size: 0.9em;">Lupa Password?</a>
        </div>
        <div style="margin-top: 20px; font-size: 0.8em; color: #6272a4;">
            Default Admin: admin / admin123<br>
            Default User: staff / staff123
        </div>
    </div>
</body>
</html>
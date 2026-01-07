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
            header("location:dashboardCustomerphp");
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
    $user_input = isset($_POST['user_input']) ? trim($_POST['user_input']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Use prepared statement for SQL injection prevention
    // Allow login with username or email
    $stmt = $conn->prepare("SELECT * FROM table_user WHERE (username = ? OR email = ?)");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password (hanya plain text)
        if ($password == $row['password']) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['level'] = $row['level']; // Simpan level (admin/user)

            // Fitur Remember Me: Set Cookie jika dicentang
            if (isset($_POST['remember'])) {
                setcookie('remember_user', $row['username'], time() + (86400 * 30), "/"); // Berlaku 30 hari
            }

            // Catat Log Login
            catatLog($conn, $row['username'], 'Login', 'User berhasil login ke sistem');

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
        $error = "Username atau Email tidak ditemukan!";
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
            background: #0f0f23;
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        /* Background Animasi */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('asset/background.jpg') no-repeat center center/cover;
            opacity: 0.4;
            z-index: -1;
            filter: blur(5px);
            animation: bgMove 40s infinite linear;
        }
        @keyframes bgMove { 0% { transform: scale(1.1) translate(0,0); } 50% { transform: scale(1.2) translate(-20px, -20px); } 100% { transform: scale(1.1) translate(0,0); } }

        .login-card {
            background: rgba(30, 30, 46, 0.7);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            background: rgba(45, 45, 66, 0.6);
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
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 0 20px rgba(80, 250, 123, 0.5); }
        button::after {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: 0.5s;
        }
        button:hover::after { left: 100%; }

        button:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid #0f0f23;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error { color: #ff5555; margin-bottom: 15px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>🔐 Login Access</h2>
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" onsubmit="showSpinner()">
            <input type="text" name="user_input" placeholder="Username atau Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <div style="text-align: left; margin: 10px 0; color: #bd93f9; font-size: 0.9em; display: flex; align-items: center;">
                <input type="checkbox" name="remember" id="remember" style="width: auto; margin: 0 10px 0 0;">
                <label for="remember" style="cursor: pointer;">Ingat Saya</label>
            </div>
            <button type="submit" id="loginBtn">
                <span class="spinner" id="spinner"></span>
                LOGIN
            </button>
        </form>
        <div style="margin-top: 15px;">
            <a href="lupaPassword.php" style="color: #bd93f9; text-decoration: none; font-size: 0.9em;">Lupa Password?</a>
            <span style="color: #6272a4; margin: 0 5px;">|</span>
            <a href="register.php" style="color: #50fa7b; text-decoration: none; font-size: 0.9em;">Register User Baru</a>
        </div>
        <div style="margin-top: 20px; font-size: 0.8em; color: #6272a4;">
            <a href="index.php" style="color: #8be9fd; text-decoration: none;">← Kembali ke Katalog</a>
        </div>
    </div>

    <script>
        function showSpinner() {
            const btn = document.getElementById('loginBtn');
            const spinner = document.getElementById('spinner');
            btn.disabled = true;
            spinner.style.display = 'inline-block';
        }
    </script>
</body>
</html>
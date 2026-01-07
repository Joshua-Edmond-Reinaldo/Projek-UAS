<?php
session_start();
require "koneksi.php";

if (isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_panggilan = trim($_POST['username']); // Nama Panggilan
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah username atau email sudah ada
        $stmt_check = $conn->prepare("SELECT id FROM table_user WHERE username = ? OR email = ?");
        $stmt_check->bind_param("ss", $nama_panggilan, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error = "Username atau Email sudah terdaftar!";
        } else {

            // 1. Insert ke table_user
            $stmt_user = $conn->prepare("INSERT INTO table_user (username, password, email, level) VALUES (?, ?, ?, 'user')");
            $stmt_user->bind_param("sss", $nama_panggilan, $password, $email);

            if ($stmt_user->execute()) {
                // 2. Insert dummy ke table_penjualan untuk menyimpan Nama Lengkap (Profil)
                // Kita isi field lain dengan default '-' atau 0 karena ini hanya data profil
                $stmt_profile = $conn->prepare("INSERT INTO table_penjualan (username, nama_pembeli, email, tanggal_transaksi, harga, jumlah_lisensi, nama_software, alamat, metode_pembayaran, no_hp, tipe_lisensi, status_pembayaran, fitur_tambahan) VALUES (?, ?, ?, CURDATE(), 0, 0, '-', '-', '-', '-', '-', '-', '-')");
                $stmt_profile->bind_param("sss", $nama_panggilan, $nama_lengkap, $email);
                $stmt_profile->execute();
                $stmt_profile->close();

                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan sistem: " . $stmt_user->error;
            }
            $stmt_user->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi User</title>
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
            padding: 20px;
        }
        .login-card {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        h2 { color: #50fa7b; margin-bottom: 20px; text-align: center; }
        input {
            width: 100%; padding: 12px; margin: 8px 0; background: #2d2d42;
            border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 8px;
            color: white; font-family: inherit; box-sizing: border-box;
        }
        input:focus { border-color: #50fa7b; outline: none; }
        button {
            width: 100%; padding: 12px; margin-top: 20px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            border: none; border-radius: 8px; color: #0f0f23; font-weight: bold; cursor: pointer;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(80, 250, 123, 0.3); }
        .error { color: #ff5555; margin-bottom: 15px; font-size: 0.9em; text-align: center; }
        .success { color: #50fa7b; margin-bottom: 15px; font-size: 0.9em; text-align: center; }
        .link { display: block; text-align: center; margin-top: 15px; color: #bd93f9; text-decoration: none; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>📝 Registrasi User</h2>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Nama Panggilan (Username)" required>
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            <button type="submit">DAFTAR</button>
        </form>
        <a href="login.php" class="link">Sudah punya akun? Login disini</a>
    </div>
</body>
</html>
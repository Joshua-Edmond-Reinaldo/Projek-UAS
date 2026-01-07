<?php
require "koneksi.php";

$error = "";
$success = false;

if (isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if (empty($token) || empty($pass1) || empty($pass2)) {
        $error = "Semua field harus diisi.";
    } elseif ($pass1 !== $pass2) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($pass1) < 6) {
        $error = "Password baru minimal 6 karakter.";
    } else {
        // Verifikasi token sekali lagi
        $stmt = $conn->prepare("SELECT id, reset_token_expires FROM table_user WHERE reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (date("Y-m-d H:i:s") > $user['reset_token_expires']) {
                $error = "Token sudah kedaluwarsa.";
            } else {
                // Token valid, update password
                $stmt_update = $conn->prepare("UPDATE table_user SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
                $stmt_update->bind_param("si", $pass1, $user['id']);
                
                if ($stmt_update->execute()) {
                    $success = true;
                } else {
                    $error = "Gagal memperbarui password. Silakan coba lagi.";
                }
            }
        } else {
            $error = "Token tidak valid.";
        }
    }
} else {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Reset Password</title>
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
            max-width: 450px;
            width: 100%;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        h2 { color: #50fa7b; }
        p { color: #a0a0b0; }
        .alert-error { color: #ff5555; background: rgba(255,85,85,0.1); padding: 10px; border-radius: 8px; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #50fa7b;
            color: #0f0f23;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <h2>✅ Berhasil!</h2>
            <p>Password Anda telah berhasil diubah. Silakan login dengan password baru Anda.</p>
            <a href="index.php" class="btn">Login Sekarang</a>
        <?php else: ?>
            <h2>❌ Gagal!</h2>
            <p class="alert-error"><?= $error ?></p>
            <a href="lupaPassword.php" class="btn" style="background:#ffb86c;">Coba Lagi</a>
        <?php endif; ?>
    </div>
</body>
</html>
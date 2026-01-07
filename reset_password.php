<?php
require "koneksi.php";

$error = "";
$token_valid = false;

if (!isset($_GET['token'])) {
    $error = "Token tidak ditemukan. Link tidak valid.";
} else {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT id, reset_token_expires FROM table_user WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $now = date("Y-m-d H:i:s");
        if ($now > $user['reset_token_expires']) {
            $error = "Token sudah kedaluwarsa. Silakan ajukan permintaan reset password lagi.";
        } else {
            $token_valid = true;
        }
    } else {
        $error = "Token tidak valid.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        * { box-sizing: border-box; }
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
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        h2 { color: #50fa7b; text-align: center; margin-bottom: 30px; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            background: #2d2d42;
            border: 1px solid #44475a;
            border-radius: 8px;
            color: #f8f8f2;
            margin-bottom: 20px;
        }
        input:focus { border-color: #50fa7b; outline: none; }
        button {
            width: 100%;
            padding: 14px;
            background: #50fa7b;
            color: #0f0f23;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        .alert-error {
            background: rgba(255, 85, 85, 0.1);
            color: #ff5555;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 85, 85, 0.2);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #8be9fd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Buat Password Baru</h2>

        <?php if ($error): ?>
            <div class="alert-error"><?= $error ?></div>
            <a href="lupaPassword.php" class="back-link">Minta Link Baru</a>
        <?php endif; ?>

        <?php if ($token_valid): ?>
            <form action="proses_reset.php" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <label for="pass1">Password Baru</label>
                <input type="password" id="pass1" name="pass1" placeholder="Minimal 6 karakter" required>
                
                <label for="pass2">Konfirmasi Password Baru</label>
                <input type="password" id="pass2" name="pass2" placeholder="Ulangi password baru" required>
                
                <button type="submit" name="reset_password">Simpan Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
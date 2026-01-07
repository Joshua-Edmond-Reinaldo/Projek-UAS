<?php
session_start();
require "koneksi.php";
// require "config_mailer.php"; // Dinonaktifkan karena tidak ada notifikasi email

$error = "";
$success = "";

if (isset($_POST['send_link'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM table_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email ditemukan, buat token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600); // Token berlaku 1 jam

        $stmt_update = $conn->prepare("UPDATE table_user SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $stmt_update->bind_param("sss", $token, $expires, $email);
        $stmt_update->execute();

        // Buat link reset
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
        
        $success = "Link reset password Anda adalah: <br><a href='$reset_link' style='word-wrap:break-word; color: #50fa7b; text-decoration:underline;'>$reset_link</a><br><br><small>Silakan salin dan buka link di atas.</small>";
    } else {
        // Jika email tidak ditemukan, berikan pesan error.
        $error = "Email tidak ditemukan dalam sistem.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * { box-sizing: border-box; }

        body {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #ff5555, #ffb86c, #bd93f9);
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #ffb86c, #ff5555);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.8em;
        }
        p { color: #a0a0b0; text-align: center; margin-bottom: 30px; font-size: 0.9em; }

        label {
            display: block;
            margin-bottom: 8px;
            color: #bd93f9;
            font-weight: 600;
            font-size: 0.9em;
        }

        input[type="email"] {
            width: 100%;
            padding: 14px 16px;
            background: #333642;
            border: 1px solid #44475a;
            border-radius: 12px;
            color: #f8f8f2;
            font-family: inherit;
            margin-bottom: 20px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #ffb86c;
            box-shadow: 0 0 8px rgba(255, 184, 108, 0.3);
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ffb86c, #ff5555);
            color: #0f0f23;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
            font-family: inherit;
            text-transform: uppercase;
            transition: 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 85, 85, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid;
        }
        .alert-success { background: rgba(80, 250, 123, 0.1); color: #50fa7b; border-color: rgba(80, 250, 123, 0.2); }
        .alert-error { background: rgba(255, 85, 85, 0.1); color: #ff5555; border-color: rgba(255, 85, 85, 0.2); }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #8be9fd;
            text-decoration: none;
            font-size: 0.9em;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Lupa Password</h2>
    <p>Masukkan alamat email Anda. Kami akan mengirimkan link untuk mereset password.</p>

    <?php if ($error) echo "<div class='alert alert-error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <label>Email Terdaftar</label>
        <input type="email" name="email" placeholder="Masukkan Email Anda" required>
        <button type="submit" name="send_link">Kirim Link Reset</button>
    </form>
    <?php endif; ?>

    <a href="index.php" class="back-link">Kembali ke Login</a>
</div>

</body>
</html>
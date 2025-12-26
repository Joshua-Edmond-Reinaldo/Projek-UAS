<?php
session_start();
if (isset($_SESSION['username'])) {
    header("location:tampilDataMhs.php");
    exit;
}

require "koneksi.php";

$step = 1; // 1: Verifikasi, 2: Reset Password, 3: Sukses
$error = "";
$success = "";
$found_id = "";

// Tahap 1: Verifikasi NIM dan Email
if (isset($_POST['verify_user'])) {
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "SELECT id FROM table_mhs WHERE nim='$nim' AND email='$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $found_id = $row['id'];
        $step = 2; // Lanjut ke reset password
    } else {
        $error = "Data tidak ditemukan! Pastikan NIM dan Email sesuai dengan yang terdaftar.";
    }
}

// Tahap 2: Simpan Password Baru
if (isset($_POST['reset_pass'])) {
    $id = (int)$_POST['user_id'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 === $pass2) {
        if (strlen($pass1) < 6) {
            $error = "Password minimal 6 karakter!";
            $step = 2;
            $found_id = $id;
        } else {
            // Simpan sebagai plain text (sesuai permintaan sebelumnya)
            $pass_final = mysqli_real_escape_string($conn, $pass1);
            
            $sql_update = "UPDATE table_mhs SET pass='$pass_final' WHERE id=$id";
            
            if ($conn->query($sql_update)) {
                $step = 3; // Sukses
            } else {
                $error = "Gagal mengupdate password: " . $conn->error;
            }
        }
    } else {
        $error = "Konfirmasi password tidak cocok!";
        $step = 2;
        $found_id = $id;
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
            margin-bottom: 30px;
            background: linear-gradient(135deg, #ffb86c, #ff5555);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.8em;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #bd93f9;
            font-weight: 600;
            font-size: 0.9em;
        }

        input[type="text"], input[type="email"], input[type="password"] {
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

        .error {
            background: rgba(255, 85, 85, 0.1);
            color: #ff5555;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 85, 85, 0.2);
        }

        .success-msg {
            text-align: center;
            color: #50fa7b;
            margin-bottom: 20px;
        }

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
    <?php if ($step == 1): ?>
        <h2>🔐 Reset Password</h2>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <label>NIM</label>
            <input type="text" name="nim" placeholder="Masukkan NIM" required>
            
            <label>Email Terdaftar</label>
            <input type="email" name="email" placeholder="Masukkan Email" required>
            
            <button type="submit" name="verify_user">Verifikasi</button>
        </form>

    <?php elseif ($step == 2): ?>
        <h2>🔑 Password Baru</h2>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?= $found_id ?>">
            
            <label>Password Baru</label>
            <input type="password" name="pass1" placeholder="Minimal 6 karakter" required>
            
            <label>Konfirmasi Password</label>
            <input type="password" name="pass2" placeholder="Ulangi password" required>
            
            <button type="submit" name="reset_pass">Simpan Password</button>
        </form>

    <?php elseif ($step == 3): ?>
        <h2>✅ Berhasil!</h2>
        <p class="success-msg">Password Anda telah berhasil diperbarui.</p>
        <a href="login.php"><button>Login Sekarang</button></a>
    <?php endif; ?>

    <?php if ($step != 3): ?>
        <a href="login.php" class="back-link">Kembali ke Login</a>
    <?php endif; ?>
</div>

</body>
</html>
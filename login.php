<?php
session_start();
if (isset($_SESSION['username'])) {
    header("location:tampilDataMhs.php");
    exit;
}

require "koneksi.php";

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['nim']);
    $password = $_POST['passw'];
    
    // Ambil data user berdasarkan NIM
    $sql = "SELECT * FROM table_mhs WHERE nim='$username' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        die("Query Error: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($query) == 1) {
        $data = mysqli_fetch_assoc($query);
        // Cek password hash atau plain text
        if (password_verify($password, $data['pass']) || $password == $data['pass']) {
            $_SESSION['username'] = $data['nim'];
            header("location:tampilDataMhs.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "NIM tidak ditemukan!";
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
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
        }

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
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 177, 153, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(80, 250, 123, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            border-radius: 20px;
            box-shadow:
                0 20px 40px rgba(0,0,0,0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            backdrop-filter: blur(10px);
            overflow: hidden;
            position: relative;
            padding: 40px;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #50fa7b, #ffb86c, #bd93f9, #ff79c6);
            border-radius: 20px 20px 0 0;
        }

        h2 {
            background: linear-gradient(145deg, #0f0f23, #1a1a2e);
            color: transparent;
            background-clip: text;
            -webkit-background-clip: text;
            background-image: linear-gradient(135deg, #50fa7b, #ffb86c);
            margin: 0 0 30px 0;
            text-align: center;
            font-size: 2.2em;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(80, 250, 123, 0.3);
            letter-spacing: 2px;
            position: relative;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(99, 102, 241, 0.2);
        }

        h2::before {
            content: '🔐';
            position: absolute;
            left: 50%;
            top: -30px;
            transform: translateX(-50%);
            font-size: 1.2em;
            opacity: 0.8;
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            color: #bd93f9;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"] {
            padding: 14px 16px;
            background: #333642;
            border: 1px solid #44475a;
            border-radius: 12px;
            color: #f8f8f2;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1em;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #50fa7b;
            box-shadow: 0 0 8px rgba(80, 250, 123, 0.5), inset 0 1px 3px rgba(0, 0, 0, 0.5);
            background-color: #3a3d47;
        }

        input[type="submit"] {
            padding: 16px 32px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1em;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(80, 250, 123, 0.3);
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        input[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        input[type="submit"]:hover {
            background: linear-gradient(135deg, #40e66b, #50fa7b);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(80, 250, 123, 0.4);
        }

        input[type="submit"]:hover::before {
            left: 100%;
        }

        input[type="submit"]:active {
            transform: translateY(0);
        }

        p.error {
            color: #ff5555;
            text-align: center;
            margin-top: 20px;
            font-weight: 600;
            background: rgba(255, 85, 85, 0.1);
            padding: 10px;
            border-radius: 8px;
            border: 1px solid rgba(255, 85, 85, 0.2);
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 30px;
            }

            h2 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Sistem</h2>
        <form method="post">
            <div class="form-group">
                <label for="nim">NIM</label>
                <input type="text" name="nim" id="nim" required autofocus placeholder="Masukkan NIM Anda">
            </div>
            <div class="form-group">
                <label for="passw">Password</label>
                <div style="position: relative;">
                    <input type="password" name="passw" id="passw" required placeholder="Masukkan Password" style="width: 100%; padding-right: 40px;">
                    <span id="toggleLoginPass" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">👁️</span>
                </div>
            </div>
            <input type="submit" name="login" value="Login">
            <div style="text-align: center; margin-top: 15px;">
                <a href="lupaPassword.php" style="color: #8be9fd; text-decoration: none; font-size: 0.9em;">Lupa Password?</a>
            </div>
        </form>
        <?php
        if ($error) {
            echo "<p class='error'>$error</p>";
        }
        ?>
    </div>
    <script>
        const toggleLoginPass = document.querySelector('#toggleLoginPass');
        const passwordLogin = document.querySelector('#passw');

        toggleLoginPass.addEventListener('click', function (e) {
            const type = passwordLogin.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordLogin.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    </script>
</body>
</html>

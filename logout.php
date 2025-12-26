<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Berhasil</title>
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
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff5555, #ffb86c, #bd93f9, #ff5555);
            border-radius: 20px 20px 0 0;
        }

        h2 {
            background: linear-gradient(135deg, #ff5555, #ffb86c);
            color: transparent;
            background-clip: text;
            -webkit-background-clip: text;
            margin: 0 0 20px 0;
            font-size: 2em;
            font-weight: 700;
            text-shadow: 0 0 20px rgba(255, 85, 85, 0.3);
        }

        p {
            color: #bd93f9;
            margin-bottom: 30px;
            font-size: 1.1em;
        }

        .btn-login {
            display: inline-block;
            padding: 16px 32px;
            background: linear-gradient(135deg, #50fa7b, #40e66b);
            color: #0f0f23;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(80, 250, 123, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #40e66b, #50fa7b);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(80, 250, 123, 0.4);
        }

        .icon {
            font-size: 4em;
            margin-bottom: 20px;
            display: block;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="icon">👋</span>
        <h2>Sampai Jumpa!</h2>
        <p>Anda telah berhasil logout dari sistem.</p>
        <a href="login.php" class="btn-login">Login Kembali</a>
    </div>
</body>
</html>
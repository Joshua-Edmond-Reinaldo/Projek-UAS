<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

$username = $_SESSION['username'];
$msg = "";
$msg_type = "";

// Ambil data user saat ini
$sql_user = "SELECT * FROM table_user WHERE username='$username' LIMIT 1";
$res_user = $conn->query($sql_user);
if ($res_user && $res_user->num_rows > 0) {
    $user_data = $res_user->fetch_assoc();
} else {
    die("User data not found.");
}

// Ambil data foto profil dari table_penjualan (jika ada)
$sql_pic = "SELECT profile_picture FROM table_penjualan WHERE username='$username' LIMIT 1";
$res_pic = $conn->query($sql_pic);
$user_pic_data = ($res_pic && $res_pic->num_rows > 0) ? $res_pic->fetch_assoc() : ['profile_picture' => ''];

// Handle Update Email
if (isset($_POST['update_email'])) {
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Format email tidak valid!";
        $msg_type = "error";
    } else {
        $sql_update = "UPDATE table_user SET email='$new_email' WHERE username='$username'";
        if ($conn->query($sql_update)) {
            $msg = "Email berhasil diperbarui!";
            $msg_type = "success";
            $user_data['email'] = $new_email;
        } else {
            $msg = "Gagal mengupdate email: " . $conn->error;
            $msg_type = "error";
        }
    }
}

// Handle Ganti Password
if (isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    $db_pass = $user_data['password'];
    // Cek password (support hash atau plain text sesuai sistem yang ada)
    $is_valid = (password_verify($old_pass, $db_pass) || $old_pass == $db_pass);

    if (!$is_valid) {
        $msg = "Password lama salah!";
        $msg_type = "error";
    } elseif (strlen($new_pass) < 6) {
        $msg = "Password baru minimal 6 karakter!";
        $msg_type = "error";
    } elseif ($new_pass !== $confirm_pass) {
        $msg = "Konfirmasi password tidak cocok!";
        $msg_type = "error";
    } else {
        // Simpan password baru (Plain text sesuai konsistensi file lain)
        $new_pass_safe = mysqli_real_escape_string($conn, $new_pass);
        $sql_pw = "UPDATE table_user SET password='$new_pass_safe' WHERE username='$username'";
        
        if ($conn->query($sql_pw)) {
            $msg = "Password berhasil diubah!";
            $msg_type = "success";
            $user_data['password'] = $new_pass;
        } else {
            $msg = "Gagal mengubah password: " . $conn->error;
            $msg_type = "error";
        }
    }
}

// Handle Upload Foto Profil
if (isset($_POST['upload_picture'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name; // Rename agar unik
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi apakah file gambar asli
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if($check === false) {
        $msg = "File bukan gambar."; $msg_type = "error"; $uploadOk = 0;
    }
    // Validasi ukuran (max 2MB)
    if ($_FILES["profile_pic"]["size"] > 2000000) {
        $msg = "Ukuran file terlalu besar (Max 2MB)."; $msg_type = "error"; $uploadOk = 0;
    }
    // Validasi format
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $msg = "Hanya format JPG, JPEG, PNG & GIF yang diperbolehkan."; $msg_type = "error"; $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $pic_path = mysqli_real_escape_string($conn, $target_file);
            
            // Cek apakah user ada di table_penjualan (untuk simpan foto)
            $check_sales = $conn->query("SELECT id FROM table_penjualan WHERE username='$username'");
            if ($check_sales->num_rows > 0) {
                $sql_pic_update = "UPDATE table_penjualan SET profile_picture='$pic_path' WHERE username='$username'";
            } else {
                // Jika user (misal admin) belum ada di table_penjualan, buat record dummy untuk menyimpan foto
                $sql_pic_update = "INSERT INTO table_penjualan (username, profile_picture, nama_pembeli, jumlah_lisensi, nama_software, tanggal_transaksi, harga, alamat, metode_pembayaran, no_hp, tipe_lisensi, status_pembayaran, fitur_tambahan, email, password) VALUES ('$username', '$pic_path', 'Admin', 0, '-', CURDATE(), 0, '-', '-', '-', '-', '-', '-', '-', '')";
            }

            if ($conn->query($sql_pic_update)) {
                $msg = "Foto profil berhasil diupload!"; $msg_type = "success"; $user_pic_data['profile_picture'] = $pic_path;
            } else { $msg = "Database error: " . $conn->error; $msg_type = "error"; }
        } else { $msg = "Maaf, terjadi error saat mengupload file."; $msg_type = "error"; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Penjualan Software</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

        * { box-sizing: border-box; }

        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .container { max-width: 800px; margin: 0 auto; }

        h1 {
            background: linear-gradient(135deg, #50fa7b, #ffb86c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 0 0 20px rgba(80, 250, 123, 0.2);
        }

        .form-section {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 30px;
            border-radius: 16px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .form-section h2 { margin-top: 0; color: #bd93f9; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 15px; margin-bottom: 20px; font-size: 1.5em; }
        label { display: block; margin-bottom: 8px; color: #ffb86c; font-weight: 600; font-size: 0.9em; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 12px 16px; background: #2d2d42; border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 10px; color: #f8f8f2; font-family: inherit; margin-bottom: 20px; outline: none; transition: 0.3s; }
        input:focus { border-color: #50fa7b; box-shadow: 0 0 8px rgba(80, 250, 123, 0.3); }
        
        .btn { padding: 12px 24px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; font-family: inherit; text-transform: uppercase; transition: 0.3s; display: inline-block; text-decoration: none; }
        .btn-save { background: linear-gradient(135deg, #50fa7b, #40e66b); color: #0f0f23; width: 100%; }
        .btn-save:hover { background: linear-gradient(135deg, #40e66b, #50fa7b); box-shadow: 0 4px 15px rgba(80, 250, 123, 0.3); }
        .btn-back { background: #44475a; color: #f8f8f2; margin-bottom: 20px; }
        .btn-back:hover { background: #6272a4; }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .alert-success { background: rgba(80, 250, 123, 0.2); color: #50fa7b; border: 1px solid rgba(80, 250, 123, 0.3); }
        .alert-error { background: rgba(255, 85, 85, 0.2); color: #ff5555; border: 1px solid rgba(255, 85, 85, 0.3); }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="btn btn-back">← Kembali ke Dashboard</a>
    <h1>⚙️ Pengaturan Akun</h1>
    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg_type ?>"><?= $msg ?></div>
    <?php endif; ?>

    <div class="form-section">
        <h2>🖼️ Foto Profil</h2>
        <div style="text-align: center; margin-bottom: 20px;">
            <?php 
            $pp = !empty($user_pic_data['profile_picture']) ? $user_pic_data['profile_picture'] : 'https://ui-avatars.com/api/?name='.urlencode($username).'&background=50fa7b&color=0f0f23';
            ?>
            <img src="<?= $pp ?>" alt="Profile Picture" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #50fa7b; box-shadow: 0 0 15px rgba(80, 250, 123, 0.3);">
        </div>
        <form method="POST" enctype="multipart/form-data">
            <label>Upload Foto Baru (Max 2MB)</label>
            <input type="file" name="profile_pic" required style="background: #2d2d42; color: #f8f8f2; padding: 10px; border-radius: 10px; width: 100%; border: 1px solid rgba(99, 102, 241, 0.3);">
            <button type="submit" name="upload_picture" class="btn btn-save" style="margin-top: 15px;">Upload Foto</button>
        </form>
    </div>

    <div class="form-section">
        <h2>📧 Update Email</h2>
        <form method="POST">
            <label>Email Saat Ini</label>
            <input type="text" value="<?= htmlspecialchars($user_data['email']) ?>" disabled style="opacity: 0.7; cursor: not-allowed;">
            <label>Email Baru</label>
            <input type="email" name="email" placeholder="Masukkan email baru" required>
            <button type="submit" name="update_email" class="btn btn-save">Simpan Email</button>
        </form>
    </div>

    <div class="form-section">
        <h2>🔒 Ganti Password</h2>
        <form method="POST">
            <label>Password Lama</label>
            <input type="password" name="old_password" placeholder="Masukkan password saat ini" required>
            <label>Password Baru</label>
            <input type="password" name="new_password" placeholder="Minimal 6 karakter" required>
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" placeholder="Ulangi password baru" required>
            <button type="submit" name="change_password" class="btn btn-save">Update Password</button>
        </form>
    </div>
</div>
</body>
</html>
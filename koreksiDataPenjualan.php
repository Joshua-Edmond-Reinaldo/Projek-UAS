<?php
// koreksiDataPenjualan.php

require "koneksi.php";

// Ambil ID dari URL dengan nama parameter 'kode'
if (!isset($_GET['kode'])) {
    die("Error: ID tidak ditemukan.");
}

$id = (int)$_GET['kode']; // Casting ke integer untuk keamanan

// Query untuk mengambil data transaksi berdasarkan ID tersebut
$sql = "SELECT * FROM table_penjualan WHERE id = $id";

// Eksekusi query
$result = $conn->query($sql);
if (!$result) {
    die("Query Error: " . $conn->error);
}

// Cek apakah data ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Data transaksi tidak ditemukan.";
    exit;
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreksi Data Penjualan</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        /* Background yang sama */
        background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f0f23 75%, #1a1a2e 100%);
        background-attachment: fixed;
        color: #e2e8f0;
        margin: 0;
        padding: 40px 20px;
        min-height: 100vh;
    }
    
    body::before {
        /* Efek glow latar belakang */
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
        max-width: 800px;
        margin: 0 auto;
        /* Container style yang sama */
        background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
        border-radius: 20px;
        box-shadow:
            0 20px 40px rgba(0,0,0,0.3),
            0 0 0 1px rgba(255, 255, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.2);
        backdrop-filter: blur(10px);
        padding: 30px;
        position: relative;
    }

    .container::before {
        /* Garis neon di atas */
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff79c6, #bd93f9, #ffb86c, #50fa7b);
        border-radius: 20px 20px 0 0;
    }

    h1 {
        /* Header style yang sama */
        background-image: linear-gradient(135deg, #ffb86c, #50fa7b);
        color: transparent;
        background-clip: text;
        -webkit-background-clip: text;
        margin: 0 0 30px 0;
        padding: 0 0 15px 0;
        text-align: center;
        font-size: 2.2em;
        font-weight: 700;
        text-shadow: 0 0 15px rgba(255, 184, 108, 0.4);
        letter-spacing: 1px;
        border-bottom: 1px solid rgba(99, 102, 241, 0.2);
    }
    
    /* FORM STYLING */
    
    label {
        display: block;
        margin-top: 20px;
        margin-bottom: 5px;
        font-weight: 500;
        color: #bd93f9; /* Warna ungu untuk label */
        letter-spacing: 0.5px;
        font-size: 0.9em;
    }

    input[type="text"], 
    input[type="email"], 
    input[type="password"], 
    input[type="number"], 
    select, 
    textarea,
    input[type="date"] {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #44475a;
        border-radius: 12px;
        background: linear-gradient(145deg, #2d2d42, #3a3a52);
        color: #f8f8f2;
        box-sizing: border-box;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9em;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    input:focus, 
    select:focus, 
    textarea:focus {
        border-color: #50fa7b;
        box-shadow: 0 0 0 3px rgba(80, 250, 123, 0.1), 0 8px 25px rgba(80, 250, 123, 0.15);
        outline: none;
        transform: translateY(-2px);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Radio/Checkbox Group */
    .radio-group, .checkbox-group {
        margin-top: 10px;
        padding: 20px;
        border: 1px solid rgba(255, 184, 108, 0.2);
        border-radius: 12px;
        background: linear-gradient(145deg, #1a1a2e, #2a2a3e);
    }

    .radio-group input[type="radio"],
    .radio-group label {
        display: inline-block;
        margin-right: 20px;
        cursor: pointer;
        color: #e2e8f0;
    }
    
    .radio-group input[type="radio"] {
        margin-right: 5px;
        /* Sembunyikan input radio asli */
        appearance: none; 
        width: 16px;
        height: 16px;
        border: 2px solid #50fa7b;
        border-radius: 50%;
        vertical-align: middle;
        position: relative;
        top: -1px;
    }
    
    .radio-group input[type="radio"]:checked {
        background-color: #50fa7b;
        border-color: #50fa7b;
        box-shadow: 0 0 10px rgba(80, 250, 123, 0.5);
    }


    /* Tombol Submit */
    input[type="submit"] {
        display: block;
        width: 100%;
        padding: 16px 32px;
        margin-top: 30px;
        background: linear-gradient(135deg, #50fa7b, #40e66b);
        color: #0f0f23;
        text-decoration: none;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(80, 250, 123, 0.3);
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        font-size: 1em;
        position: relative;
        overflow: hidden;
    }

    input[type="submit"]:hover {
        background: linear-gradient(135deg, #40e66b, #50fa7b);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(80, 250, 123, 0.4);
    }
    
    /* Tambahan untuk placeholder */
    ::placeholder {
        color: #777;
        opacity: 0.7;
    }
    
</style>
</head>
<body>
    <div class="container">
        <h1>📝 Koreksi Data Penjualan</h1>
        
        <form action="simpanKoreksiPenjualan.php" method="POST">
            
            <input type="hidden" name="id" value="<?= isset($row['id']) ? $row['id'] : '' ?>">

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= isset($row['username']) ? $row['username'] : '' ?>" required>

            <label for="nama_pembeli">Nama Pembeli:</label>
            <input type="text" id="nama_pembeli" name="nama_pembeli" value="<?= isset($row['nama_pembeli']) ? $row['nama_pembeli'] : '' ?>" required>

            <label for="jumlah_lisensi">Jumlah Lisensi:</label>
            <input type="number" id="jumlah_lisensi" name="jumlah_lisensi" value="<?= isset($row['jumlah_lisensi']) ? $row['jumlah_lisensi'] : '' ?>">

            <label for="nama_software">Nama Software:</label>
            <input type="text" id="nama_software" name="nama_software" value="<?= isset($row['nama_software']) ? $row['nama_software'] : '' ?>">

            <label for="tanggal_transaksi">Tanggal Transaksi:</label>
            <input type="date" id="tanggal_transaksi" name="tanggal_transaksi" value="<?= isset($row['tanggal_transaksi']) ? $row['tanggal_transaksi'] : '' ?>">

            <label for="harga">Harga Total:</label>
            <input type="number" id="harga" name="harga" value="<?= isset($row['harga']) ? $row['harga'] : '' ?>">

            <label for="alamat">Alamat Tagihan:</label>
            <textarea id="alamat" name="alamat"><?= isset($row['alamat']) ? $row['alamat'] : '' ?></textarea>

            <label for="metode_pembayaran">Metode Pembayaran:</label>
            <input type="text" id="metode_pembayaran" name="metode_pembayaran" value="<?= isset($row['metode_pembayaran']) ? $row['metode_pembayaran'] : '' ?>">

            <label for="noHP">No HP:</label>
            <input type="text" id="noHP" name="no_hp" value="<?= isset($row['no_hp']) ? $row['no_hp'] : '' ?>">

            <label>Tipe Lisensi:</label>
            <div class="radio-group">
                <input type="radio" id="tipe_p" name="tipe_lisensi" value="Personal" <?= (isset($row['tipe_lisensi']) && $row['tipe_lisensi'] == 'Personal') ? 'checked' : '' ?>>
                <label for="tipe_p">Personal</label>
                <input type="radio" id="tipe_b" name="tipe_lisensi" value="Bisnis" <?= (isset($row['tipe_lisensi']) && $row['tipe_lisensi'] == 'Bisnis') ? 'checked' : '' ?>>
                <label for="tipe_b">Bisnis</label>
            </div>

            <label for="status_pembayaran">Status Pembayaran:</label>
            <select id="status_pembayaran" name="status_pembayaran">
                <option value="Lunas" <?= (isset($row['status_pembayaran']) && $row['status_pembayaran'] == 'Lunas') ? 'selected' : '' ?>>Lunas</option>
                <option value="Pending" <?= (isset($row['status_pembayaran']) && $row['status_pembayaran'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                <option value="Batal" <?= (isset($row['status_pembayaran']) && $row['status_pembayaran'] == 'Batal') ? 'selected' : '' ?>>Batal</option>
            </select>

            <label for="fitur_tambahan">Fitur Tambahan:</label>
            <input type="text" id="fitur_tambahan" name="fitur_tambahan" value="<?= isset($row['fitur_tambahan']) ? $row['fitur_tambahan'] : '' ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= isset($row['email']) ? $row['email'] : '' ?>">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Isi hanya jika ingin mengubah password">

            <input type="submit" value="💾 Simpan Perubahan">
        </form>
    </div>
</body>
</html>
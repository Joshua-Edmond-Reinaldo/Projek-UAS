<?php
// koreksiDataMhs.php

require "koneksi.php";

// Ambil ID dari URL dengan nama parameter 'kode'
if (!isset($_GET['kode'])) {
    die("Error: ID tidak ditemukan.");
}

$id = (int)$_GET['kode']; // Casting ke integer untuk keamanan

// Query untuk mengambil data mahasiswa berdasarkan ID tersebut
$sql = "SELECT * FROM table_mhs WHERE id = $id";

// Eksekusi query
$result = $conn->query($sql);
if (!$result) {
    die("Query Error: " . $conn->error);
}

// Cek apakah data ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Data mahasiswa tidak ditemukan.";
    exit;
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreksi Data Mahasiswa</title>
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
        border-radius: 8px;
        background-color: #333642; /* Warna input yang lebih gelap */
        color: #f8f8f2;
        box-sizing: border-box;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9em;
        transition: all 0.3s ease;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.5);
    }

    input:focus, 
    select:focus, 
    textarea:focus {
        border-color: #50fa7b; /* Border glow saat fokus */
        box-shadow: 0 0 8px rgba(80, 250, 123, 0.5), inset 0 1px 3px rgba(0, 0, 0, 0.5);
        outline: none;
        background-color: #3a3d47;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Radio/Checkbox Group */
    .radio-group, .checkbox-group {
        margin-top: 10px;
        padding: 10px;
        border: 1px dashed rgba(99, 102, 241, 0.2);
        border-radius: 8px;
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
        border: 2px solid #ff79c6;
        border-radius: 50%;
        vertical-align: middle;
        position: relative;
        top: -1px;
    }
    
    .radio-group input[type="radio"]:checked {
        background-color: #ff79c6;
        border-color: #ff79c6;
        box-shadow: 0 0 5px #ff79c6;
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
        <h1>📝 Koreksi Data Mahasiswa</h1>
        
        <form action="simpanKoreksiData.php" method="POST">
            
            <input type="hidden" name="id" value="<?= isset($row['id']) ? $row['id'] : '' ?>">

            <label for="nim">NIM:</label>
            <input type="text" id="nim" name="nim" value="<?= isset($row['nim']) ? $row['nim'] : '' ?>" required>

            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" value="<?= isset($row['nama']) ? $row['nama'] : '' ?>" required>

            <label for="umur">Umur:</label>
            <input type="number" id="umur" name="umur" value="<?= isset($row['umur']) ? $row['umur'] : '' ?>">

            <label for="tempatLahir">Tempat Lahir:</label>
            <input type="text" id="tempatLahir" name="tempatLahir" value="<?= isset($row['tempatLahir']) ? $row['tempatLahir'] : '' ?>">

            <label for="tanggalLahir">Tanggal Lahir:</label>
            <input type="date" id="tanggalLahir" name="tanggalLahir" value="<?= isset($row['tanggalLahir']) ? $row['tanggalLahir'] : '' ?>">

            <label for="jmlSaudara">Jumlah Saudara:</label>
            <input type="number" id="jmlSaudara" name="jmlSaudara" value="<?= isset($row['jmlSaudara']) ? $row['jmlSaudara'] : '' ?>">

            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat"><?= isset($row['alamat']) ? $row['alamat'] : '' ?></textarea>

            <label for="kota">Kota:</label>
            <input type="text" id="kota" name="kota" value="<?= isset($row['kota']) ? $row['kota'] : '' ?>">

            <label for="noHP">No HP:</label>
            <input type="text" id="noHP" name="noHP" value="<?= isset($row['noHP']) ? $row['noHP'] : '' ?>">

            <label>Jenis Kelamin:</label>
            <div class="radio-group">
                <input type="radio" id="jk_l" name="jenisKelamin" value="Laki - Laki" <?= (isset($row['jenisKelamin']) && $row['jenisKelamin'] == 'Laki - Laki') ? 'checked' : '' ?>>
                <label for="jk_l">Laki - Laki</label>
                <input type="radio" id="jk_p" name="jenisKelamin" value="Perempuan" <?= (isset($row['jenisKelamin']) && $row['jenisKelamin'] == 'Perempuan') ? 'checked' : '' ?>>
                <label for="jk_p">Perempuan</label>
            </div>

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Aktif" <?= (isset($row['status']) && $row['status'] == 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                <option value="Cuti" <?= (isset($row['status']) && $row['status'] == 'Cuti') ? 'selected' : '' ?>>Cuti</option>
                <option value="Lulus" <?= (isset($row['status']) && $row['status'] == 'Lulus') ? 'selected' : '' ?>>Lulus</option>
            </select>

            <label for="hobi">Hobi:</label>
            <input type="text" id="hobi" name="hobi" value="<?= isset($row['hobi']) ? $row['hobi'] : '' ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= isset($row['email']) ? $row['email'] : '' ?>">

            <label for="pass">Password:</label>
            <input type="password" id="pass" name="pass" placeholder="Isi hanya jika ingin mengubah password">

            <input type="submit" value="💾 Simpan Perubahan">
        </form>
    </div>
</body>
</html>
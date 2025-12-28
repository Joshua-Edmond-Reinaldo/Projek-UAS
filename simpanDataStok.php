<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_software = mysqli_real_escape_string($conn, $_POST['nama_software']);
    $jumlah_stok = (int)$_POST['jumlah_stok'];

    // Cek duplikat
    $sql_cek = "SELECT id FROM table_stok WHERE nama_software = '$nama_software'";
    $res_cek = $conn->query($sql_cek);
    if ($res_cek->num_rows > 0) {
        die("<script>alert('Software dengan nama ini sudah ada di stok!'); window.location.href='tambahDataStok.php';</script>");
    }

    // Insert data baru
    $sql_insert = "INSERT INTO table_stok (nama_software, jumlah_stok) VALUES ('$nama_software', $jumlah_stok)";
    if ($conn->query($sql_insert)) {
        catatLog($conn, $_SESSION['username'], 'Tambah Stok', "Menambahkan stok baru: $nama_software ($jumlah_stok)");
        header("location: tampilDataStok.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

```

### 4. Formulir Koreksi Stok (`koreksiDataStok.php`)

```diff
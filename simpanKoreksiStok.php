<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $jumlah_stok = (int)$_POST['jumlah_stok'];

    $sql_update = "UPDATE table_stok SET jumlah_stok = $jumlah_stok WHERE id = $id";
    if ($conn->query($sql_update)) {
        catatLog($conn, $_SESSION['username'], 'Edit Stok', "Mengupdate stok ID: $id menjadi $jumlah_stok");
        header("location: tampilDataStok.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

```

### 6. Skrip Hapus Stok (`hapusDataStok.php`)

```diff
<?php
session_start();

function bersihkan($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validasiNama($nama) {
    if (empty($nama)) return "Nama tidak boleh kosong.";
    return true;
}

function validasiJumlah($jumlah) {
    if (empty($jumlah)) return "Jumlah tidak boleh kosong.";
    if (!is_numeric($jumlah)) return "Jumlah harus berupa angka.";
    return true;
}

$username = bersihkan($_POST['username'] ?? '-');
$nama_pembeli = bersihkan($_POST['nama_pembeli'] ?? '-');
$jumlah_lisensi = bersihkan($_POST['jumlah_lisensi'] ?? '-');
$nama_software = bersihkan($_POST['nama_software'] ?? '-');
$tanggal_transaksi = bersihkan($_POST['tanggal_transaksi'] ?? '-');

$harga = bersihkan($_POST['harga'] ?? '-');
$alamat = bersihkan($_POST['alamat'] ?? '-');
$metode_pembayaran = bersihkan($_POST['metode_pembayaran'] ?? '-');
$no_hp = bersihkan($_POST['no_hp'] ?? '-');
$tipe_lisensi = isset($_POST['tipe_lisensi']) ? bersihkan($_POST['tipe_lisensi']) : "-";
$status_pembayaran = isset($_POST['status_pembayaran']) ? bersihkan($_POST['status_pembayaran']) : "-";
$email = bersihkan($_POST['email'] ?? '-');
// Password disimpan plain text sesuai permintaan sistem yang ada
$password = $_POST['password'] ?? '-';

$fitur_list = [];
if (!empty($_POST['fitur_tambahan'])) {
    if(is_array($_POST['fitur_tambahan'])) {
        foreach ($_POST['fitur_tambahan'] as $h) {
            $fitur_list[] = bersihkan($h);
        }
        $fitur_output = implode(", ", $fitur_list);
    } else {
        $fitur_output = bersihkan($_POST['fitur_tambahan']);
    }
} else {
    $fitur_output = "Standar";
}

$cek_nama = validasiNama($nama_pembeli);
$cek_jumlah = validasiJumlah($jumlah_lisensi);

if ($cek_nama !== true) {
    die("<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>Gagal: $cek_nama</h3>
            <a href='tambahDataPenjualan.php'>Kembali ke Form</a>
         </div>");
}

if ($cek_jumlah !== true) {
    die("<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>Gagal: $cek_jumlah</h3>
            <a href='tambahDataPenjualan.php'>Kembali ke Form</a>
         </div>");
}

include 'koneksi.php';

// --- Validasi Username Ganda (Duplikat) ---
$cek_user_sql = "SELECT username FROM table_penjualan WHERE username = ?";
$stmt_cek = $conn->prepare($cek_user_sql);
if (!$stmt_cek) {
    die("Query Cek Username Error: " . $conn->error);
}
$stmt_cek->bind_param("s", $username);
$stmt_cek->execute();
$stmt_cek->store_result();

if ($stmt_cek->num_rows > 0) {
    die("<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>Gagal: Username $username sudah terdaftar!</h3>
            <a href='tambahDataPenjualan.php'>Kembali ke Form</a>
         </div>");
}
$stmt_cek->close();

// --- Validasi Username di table_user ---
$cek_user_tbl = "SELECT id FROM table_user WHERE username = '$username'";
$res_user_tbl = $conn->query($cek_user_tbl);
if ($res_user_tbl->num_rows > 0) {
    die("<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>Gagal: Username $username sudah digunakan oleh user lain (Admin/Staff)!</h3>
            <a href='tambahDataPenjualan.php'>Kembali ke Form</a>
         </div>");
}

// --- Cek Stok Barang ---
$cek_stok_sql = "SELECT jumlah_stok FROM table_stok WHERE nama_software = ?";
$stmt_stok = $conn->prepare($cek_stok_sql);
if ($stmt_stok) {
    $stmt_stok->bind_param("s", $nama_software);
    $stmt_stok->execute();
    $res_stok = $stmt_stok->get_result();
    if ($res_stok->num_rows > 0) {
        $row_stok = $res_stok->fetch_assoc();
        if ($row_stok['jumlah_stok'] < $jumlah_lisensi) {
             die("<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
                    <h3>Gagal: Stok tidak mencukupi! Stok saat ini: " . $row_stok['jumlah_stok'] . "</h3>
                    <a href='tambahDataPenjualan.php'>Kembali ke Form</a>
                 </div>");
        }
    }
    $stmt_stok->close();
}

// Insert ke table_penjualan
$sql = "INSERT INTO table_penjualan (username, nama_pembeli, jumlah_lisensi, nama_software, tanggal_transaksi, harga, alamat, metode_pembayaran, no_hp, tipe_lisensi, status_pembayaran, fitur_tambahan, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query Error: " . $conn->error);
}
// Binding parameters
$stmt->bind_param("ssississssssss", $username, $nama_pembeli, $jumlah_lisensi, $nama_software, $tanggal_transaksi, $harga, $alamat, $metode_pembayaran, $no_hp, $tipe_lisensi, $status_pembayaran, $fitur_output, $email, $password);

if ($stmt->execute()) {
    // Insert juga ke table_user agar bisa login
    $sql_user_insert = "INSERT INTO table_user (username, password, email, level) VALUES ('$username', '$password', '$email', 'user')";
    $conn->query($sql_user_insert);

    // Catat Log
    $logger = isset($_SESSION['username']) ? $_SESSION['username'] : 'System';
    catatLog($conn, $logger, 'Input Penjualan', "Menambahkan transaksi: $nama_pembeli - $nama_software");

    // Kurangi Stok hanya jika status pembayaran Lunas
    if ($status_pembayaran == 'Lunas') {
        $update_stok_sql = "UPDATE table_stok SET jumlah_stok = jumlah_stok - ? WHERE nama_software = ?";
        $stmt_update_stok = $conn->prepare($update_stok_sql);
        if ($stmt_update_stok) {
            $stmt_update_stok->bind_param("is", $jumlah_lisensi, $nama_software);
            $stmt_update_stok->execute();
            $stmt_update_stok->close();
        }
    }
} else {
    die("<div style='color:red; text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>Gagal menyimpan data: " . $stmt->error . "</h3>
            <a href='tambahDataPenjualan.php'>Kembali ke Form</a>
         </div>");
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sukses</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'JetBrains Mono', monospace;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: linear-gradient(145deg, #1e1e2e, #2a2a3e);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid rgba(80, 250, 123, 0.3);
            box-shadow: 0 0 30px rgba(80, 250, 123, 0.2);
        }
        h2 { color: #50fa7b; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #50fa7b;
            color: #0f0f23;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>✅ Data Berhasil Disimpan!</h2>
        <p>Transaksi penjualan telah berhasil dicatat ke dalam sistem.</p>
        <a href="tampilDataPenjualan.php" class="btn">Lihat Data</a>
        <a href="tambahDataPenjualan.php" class="btn" style="background: #bd93f9;">Input Lagi</a>
    </div>
</body>
</html>
<?php
session_start();

// Ambil data dari URL
$nama_software = isset($_GET['software']) ? $_GET['software'] : '';
$harga = isset($_GET['price']) ? (int)$_GET['price'] : 0;

// Validasi sederhana
if (empty($nama_software) || $harga <= 0) {
    header("Location: index.php");
    exit;
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Cek apakah produk sudah ada di keranjang
$found = false;
foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['name'] == $nama_software) {
        // Jika ada, tambahkan jumlahnya
        $_SESSION['cart'][$key]['qty'] += 1;
        $found = true;
        break;
    }
}

// Jika belum ada, tambahkan item baru
if (!$found) {
    $_SESSION['cart'][] = [
        'name' => $nama_software,
        'price' => $harga,
        'qty' => 1
    ];
}

// Redirect kembali ke index dengan pesan sukses (opsional bisa pakai flash message)
header("Location: index.php");
exit;
?>
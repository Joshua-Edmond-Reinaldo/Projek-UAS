<?php
// c:\xampp\htdocs\Projek UAS\simpanKoreksiKupon.php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}
require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $code = strtoupper(trim($_POST['code']));
    $type = $_POST['type'];
    $value = (float)$_POST['value'];
    $limit_per_user = isset($_POST['limit_per_user']) ? 1 : 0;
    $valid_until = !empty($_POST['valid_until']) ? $_POST['valid_until'] : NULL;
    $usage_limit = (int)$_POST['usage_limit'];

    // Cek duplikat kode (kecuali punya sendiri)
    $check = $conn->query("SELECT id FROM table_coupons WHERE code = '$code' AND id != $id");
    if ($check->num_rows > 0) {
        echo "<script>alert('Kode promo sudah digunakan!'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("UPDATE table_coupons SET code=?, type=?, value=?, limit_per_user=?, valid_until=?, usage_limit=? WHERE id=?");
    $stmt->bind_param("ssdisii", $code, $type, $value, $limit_per_user, $valid_until, $usage_limit, $id);

    if ($stmt->execute()) {
        catatLog($conn, $_SESSION['username'], 'Edit Kupon', "Mengubah kupon ID: $id ($code)");
        header("location: tampilDataKupon.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
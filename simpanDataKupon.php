<?php
// c:\xampp\htdocs\Projek UAS\simpanDataKupon.php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}
require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = strtoupper(trim($_POST['code']));
    $type = $_POST['type'];
    $value = (float)$_POST['value'];
    $limit_per_user = isset($_POST['limit_per_user']) ? 1 : 0;
    $apply_rule = isset($_POST['apply_rule']) ? $_POST['apply_rule'] : 'all';
    $valid_until = !empty($_POST['valid_until']) ? $_POST['valid_until'] : NULL;
    $usage_limit = (int)$_POST['usage_limit'];

    // Cek duplikat kode
    $check = $conn->query("SELECT id FROM table_coupons WHERE code = '$code'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Kode promo sudah ada!'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO table_coupons (code, type, value, limit_per_user, apply_rule, valid_until, usage_limit) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdissi", $code, $type, $value, $limit_per_user, $apply_rule, $valid_until, $usage_limit);

    if ($stmt->execute()) {
        $coupon_id = $conn->insert_id;

        // Jika kupon untuk produk spesifik, simpan relasinya
        if ($apply_rule == 'product' && !empty($_POST['products'])) {
            $products = $_POST['products'];
            $stmt_prod = $conn->prepare("INSERT INTO table_coupon_products (coupon_id, product_name) VALUES (?, ?)");
            foreach ($products as $product_name) {
                $stmt_prod->bind_param("is", $coupon_id, $product_name);
                $stmt_prod->execute();
            }
            $stmt_prod->close();
        }

        // Jika kupon untuk kategori spesifik, simpan relasinya
        if ($apply_rule == 'category' && !empty($_POST['categories'])) {
            $categories = $_POST['categories'];
            $stmt_cat = $conn->prepare("INSERT INTO table_coupon_categories (coupon_id, category_name) VALUES (?, ?)");
            foreach ($categories as $category_name) {
                $stmt_cat->bind_param("is", $coupon_id, $category_name);
                $stmt_cat->execute();
            }
            $stmt_cat->close();
        }

        catatLog($conn, $_SESSION['username'], 'Tambah Kupon', "Menambahkan kupon: $code");
        header("location: tampilDataKupon.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
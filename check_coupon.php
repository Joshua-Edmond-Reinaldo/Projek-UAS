<?php
require "koneksi.php";
require "data_produk.php"; // Untuk mendapatkan array $products
session_start();
header('Content-Type: application/json');

if (!isset($_GET['code']) || empty(trim($_GET['code']))) {
    echo json_encode(['valid' => false, 'message' => 'Kode kosong']);
    exit;
}

// Ambil item dari session cart atau dari parameter GET untuk checkout tunggal
$items_to_check = [];
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { // Dari checkout_keranjang.php
    $items_to_check = $_SESSION['cart'];
}

$code = strtoupper(trim($_GET['code']));
$today = date('Y-m-d');

$stmt = $conn->prepare("SELECT * FROM table_coupons WHERE code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $coupon = $result->fetch_assoc();
    
    // Validasi Tanggal
    if ($coupon['valid_until'] && $coupon['valid_until'] < $today) {
        echo json_encode(['valid' => false, 'message' => 'Kupon sudah kadaluarsa']);
        exit;
    }
    
    // Validasi Limit
    if ($coupon['usage_limit'] > 0 && $coupon['used_count'] >= $coupon['usage_limit']) {
        echo json_encode(['valid' => false, 'message' => 'Kuota penggunaan kupon habis']);
        exit;
    }
    
    // Validasi Penggunaan per User
    if ($coupon['limit_per_user'] == 1) {
        if (!isset($_SESSION['username'])) {
            // Jika user belum login, kita tidak bisa validasi.
            echo json_encode(['valid' => false, 'message' => 'Anda harus login untuk menggunakan kupon ini']);
            exit;
        }
        $username = $_SESSION['username'];
        $stmt_usage = $conn->prepare("SELECT id FROM table_coupon_usage WHERE coupon_id = ? AND username = ?");
        $stmt_usage->bind_param("is", $coupon['id'], $username);
        $stmt_usage->execute();
        if ($stmt_usage->get_result()->num_rows > 0) {
            echo json_encode(['valid' => false, 'message' => 'Anda sudah pernah menggunakan kupon ini']);
            exit;
        }
    }
    
    // --- Validasi Produk ---
    $original_total = 0;
    $discountable_total = 0;

    if (empty($items_to_check)) {
        echo json_encode(['valid' => false, 'message' => 'Keranjang Anda kosong.']);
        exit;
    }

    // Hitung total asli
    foreach ($items_to_check as $item) {
        $original_total += $item['price'] * $item['qty'];
    }

    if ($coupon['apply_rule'] == 'all') {
        $discountable_total = $original_total;
    } elseif ($coupon['apply_rule'] == 'product') {
        // Ambil produk yang berlaku untuk kupon ini
        $stmt_prods = $conn->prepare("SELECT product_name FROM table_coupon_products WHERE coupon_id = ?");
        $stmt_prods->bind_param("i", $coupon['id']);
        $stmt_prods->execute();
        $res_prods = $stmt_prods->get_result();
        $applicable_products = [];
        while ($p_row = $res_prods->fetch_assoc()) {
            $applicable_products[] = $p_row['product_name'];
        }

        // Hitung total dari item yang bisa didiskon
        foreach ($items_to_check as $item) {
            if (in_array($item['name'], $applicable_products)) {
                $discountable_total += $item['price'] * $item['qty'];
            }
        }
    } elseif ($coupon['apply_rule'] == 'category') {
        // Ambil kategori yang berlaku untuk kupon ini
        $stmt_cats = $conn->prepare("SELECT category_name FROM table_coupon_categories WHERE coupon_id = ?");
        $stmt_cats->bind_param("i", $coupon['id']);
        $stmt_cats->execute();
        $res_cats = $stmt_cats->get_result();
        $applicable_categories = [];
        while ($c_row = $res_cats->fetch_assoc()) {
            $applicable_categories[] = $c_row['category_name'];
        }

        // Buat map produk -> kategori dari array $products
        $product_to_category_map = array_column($products, 'category', 'name');

        foreach ($items_to_check as $item) {
            $item_category = isset($product_to_category_map[$item['name']]) ? $product_to_category_map[$item['name']] : null;
            if ($item_category && in_array($item_category, $applicable_categories)) {
                $discountable_total += $item['price'] * $item['qty'];
            }
        }
    }

    if ($discountable_total <= 0) {
        echo json_encode(['valid' => false, 'message' => 'Kupon tidak berlaku untuk produk di keranjang.']);
        exit;
    }

    // Hitung jumlah diskon
    $discount_amount = 0;
    if ($coupon['type'] == 'percentage') {
        $discount_amount = $discountable_total * ($coupon['value'] / 100);
    } else { // fixed
        $discount_amount = $coupon['value'];
    }
    if ($discount_amount > $discountable_total) $discount_amount = $discountable_total;

    $final_total = $original_total - $discount_amount;

    echo json_encode([
        'valid' => true,
        'message' => 'Kupon berhasil diterapkan!',
        'new_total' => $final_total
    ]);
} else {
    echo json_encode(['valid' => false, 'message' => 'Kode kupon tidak ditemukan']);
}
?>
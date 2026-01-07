<?php
session_start();
require "koneksi.php";

require "data_produk.php"; // Untuk mendapatkan array $products
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_SESSION['cart'])) {
    // Pastikan user login
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    // Ambil data user
    $username = $_SESSION['username']; // Gunakan session
    $nama_pembeli = trim($_POST['nama_pembeli']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);

    $tanggal_transaksi = date('Y-m-d');
    $status_pembayaran = 'Pending';
    $metode_pembayaran = 'Transfer Bank';
    $tipe_lisensi = 'Personal';
    $fitur_tambahan = 'Standar';

    // Ambil kode kupon
    $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
    $coupon_data = null;

    // Validasi Kupon di Server Side
    if (!empty($coupon_code)) {
        // Tambahkan validasi limit dan tanggal
        $stmt_c = $conn->prepare("SELECT * FROM table_coupons WHERE code = ? AND (valid_until IS NULL OR valid_until >= CURDATE()) AND (usage_limit = 0 OR used_count < usage_limit)");
        $stmt_c->bind_param("s", $coupon_code);
        $stmt_c->execute();
        $res_c = $stmt_c->get_result();
        if ($res_c->num_rows > 0) {
            $coupon_data = $res_c->fetch_assoc();
            // Cek juga apakah user sudah pernah pakai (untuk user yang sudah ada)
            if ($coupon_data['limit_per_user'] == 1) {
                $check_usage = $conn->query("SELECT id FROM table_coupon_usage WHERE coupon_id = " . $coupon_data['id'] . " AND username = '$username'");
                if ($check_usage->num_rows > 0) $coupon_data = null; // Batalkan kupon jika sudah dipakai
            }
        }
        $stmt_c->close();
    }

    // 4. Hitung ulang diskon di server
    $original_total = 0;
    $discountable_total = 0;
    $applicable_products = [];
    $applicable_categories = [];

    // Ubah logika: Cek berdasarkan apply_rule, bukan apply_to_all
    if ($coupon_data && $coupon_data['apply_rule'] != 'all') {
        $stmt_prods = $conn->prepare("SELECT product_name FROM table_coupon_products WHERE coupon_id = ?");
        $stmt_prods->bind_param("i", $coupon_data['id']);
        $stmt_prods->execute();
        $res_prods = $stmt_prods->get_result();
        while ($p_row = $res_prods->fetch_assoc()) {
            $applicable_products[] = $p_row['product_name'];
        }
        $stmt_prods->close();

        // Fix: Ambil juga kategori jika rule-nya category
        if ($coupon_data['apply_rule'] == 'category') {
            $stmt_cats = $conn->prepare("SELECT category_name FROM table_coupon_categories WHERE coupon_id = ?");
            $stmt_cats->bind_param("i", $coupon_data['id']);
            $stmt_cats->execute();
            $res_cats = $stmt_cats->get_result();
            while ($c_row = $res_cats->fetch_assoc()) {
                $applicable_categories[] = $c_row['category_name'];
            }
            $stmt_cats->close();
        }
    }

    foreach ($_SESSION['cart'] as $item) {
        $subtotal = $item['price'] * $item['qty'];
        $original_total += $subtotal;
        if ($coupon_data) {
            if ($coupon_data['apply_rule'] == 'all') {
                $discountable_total += $subtotal;
            } elseif ($coupon_data['apply_rule'] == 'product' && in_array($item['name'], $applicable_products)) {
                $discountable_total += $subtotal;
            } elseif ($coupon_data['apply_rule'] == 'category') {
                $product_to_category_map = array_column($products, 'category', 'name');
                $item_category = isset($product_to_category_map[$item['name']]) ? $product_to_category_map[$item['name']] : '';
                if ($item_category && in_array($item_category, $applicable_categories)) $discountable_total += $subtotal;
            }
        }
    }

    $total_discount = 0;
    if ($coupon_data && $discountable_total > 0) {
        if ($coupon_data['type'] == 'percentage') {
            $total_discount = $discountable_total * ($coupon_data['value'] / 100);
        } else { // fixed
            $total_discount = $coupon_data['value'];
        }
        if ($total_discount > $discountable_total) $total_discount = $discountable_total;
    }

    // 5. Proses Insert Transaksi Loop

    $success_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $nama_software = $item['name'];
        
        $harga_asli = $item['price'] * $item['qty'];
        $harga = $harga_asli;

        // Terapkan diskon proporsional jika item ini berlaku
        if ($total_discount > 0 && $discountable_total > 0) {
            $is_applicable = false;
            if ($coupon_data['apply_rule'] == 'all') {
                $is_applicable = true;
            } elseif ($coupon_data['apply_rule'] == 'product' && in_array($item['name'], $applicable_products)) {
                $is_applicable = true;
            } elseif ($coupon_data['apply_rule'] == 'category') {
                 $product_to_category_map = array_column($products, 'category', 'name');
                 $item_category = isset($product_to_category_map[$item['name']]) ? $product_to_category_map[$item['name']] : '';
                 if ($item_category && in_array($item_category, $applicable_categories)) $is_applicable = true;
            }

            if ($is_applicable) {
                $proporsi = $harga_asli / $discountable_total;
                $potongan_item = $total_discount * $proporsi;
                $harga = $harga_asli - $potongan_item;
            }
        }
        
        if ($harga < 0) $harga = 0;
        
        $jumlah_lisensi = $item['qty'];

        $stmt_transaksi = $conn->prepare("INSERT INTO table_penjualan
                (username, nama_pembeli, jumlah_lisensi, nama_software, tanggal_transaksi, harga, alamat, metode_pembayaran, no_hp, tipe_lisensi, status_pembayaran, fitur_tambahan, email)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_transaksi->bind_param("ssissdsssssss", $username, $nama_pembeli, $jumlah_lisensi, $nama_software, $tanggal_transaksi, $harga, $alamat, $metode_pembayaran, $no_hp, $tipe_lisensi, $status_pembayaran, $fitur_tambahan, $email);

        if ($stmt_transaksi->execute()) {
            $success_count++;
        }
        $stmt_transaksi->close();
    }

    if ($success_count > 0) {
        // Catat Log
        catatLog($conn, $username, 'Cart Checkout', "Membeli $success_count jenis item (Pending)");
        
        // Update penggunaan kupon
        if ($coupon_data) {
            $conn->query("UPDATE table_coupons SET used_count = used_count + 1 WHERE id = " . $coupon_data['id']);
            // Catat penggunaan oleh user jika diaktifkan
            if ($coupon_data['limit_per_user'] == 1) {
                $conn->query("INSERT INTO table_coupon_usage (coupon_id, username) VALUES (" . $coupon_data['id'] . ", '$username')");
            }
        }
        
        // Kosongkan Keranjang
        unset($_SESSION['cart']);
        
        echo "<script>
                alert('Transaksi Berhasil! Silakan login untuk melihat status pesanan.');
                window.location.href = 'dashboardCustomer.php';
              </script>";
    } else {
        die("Gagal memproses transaksi.");
    }

} else {
    header("Location: index.php");
}
?>
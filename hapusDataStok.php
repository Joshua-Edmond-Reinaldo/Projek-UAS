<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql_delete = "DELETE FROM table_stok WHERE id = $id";
    if ($conn->query($sql_delete)) {
        catatLog($conn, $_SESSION['username'], 'Hapus Stok', "Menghapus stok ID: $id");
        header("location: tampilDataStok.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

```

### 7. Pembaruan `dashboard.php`

Saya akan menambahkan kartu statistik baru untuk jumlah produk yang dikelola dan sebuah tombol untuk menuju halaman manajemen stok.

```diff
--- a/d:\A12.2024.07257\Pemrograman Web Dasar\Projek UAS\dashboard.php
+++ b/d:\A12.2024.07257\Pemrograman Web Dasar\Projek UAS\dashboard.php
@@ -40,6 +40,12 @@
 $sql_recent = "SELECT * FROM table_penjualan ORDER BY id DESC LIMIT 5";
 $res_recent = $conn->query($sql_recent);
 
+// 5.1 Total Produk di Stok
+$sql_products = "SELECT COUNT(*) as total FROM table_stok";
+$res_products = $conn->query($sql_products);
+$row_products = $res_products->fetch_assoc();
+$total_products = $row_products['total'];
+
 // 6. Data Grafik Penjualan
 $range = isset($_GET['range']) && $_GET['range'] == '30' ? 30 : 7;
 $sql_chart = "SELECT tanggal_transaksi, SUM(harga) as total 
@@ -348,6 +354,12 @@
             <div class="stat-label">Lisensi Terjual</div>
             <div class="stat-number"><?= $total_license ?></div>
         </div>
+        <div class="stat-card">
+            <span class="icon">📦</span>
+            <div class="stat-label">Total Produk Dikelola</div>
+            <div class="stat-number" style="color: #8be9fd;"><?= $total_products ?></div>
+        </div>
     </div>
 
     <div class="charts-row">
@@ -391,6 +403,7 @@
         <div class="btn-group">
             <a href="tampilDataPenjualan.php" class="btn btn-primary">Lihat Semua Data</a>
             <a href="tambahDataPenjualan.php" class="btn btn-secondary">Input Transaksi</a>
+            <a href="tampilDataStok.php" class="btn btn-secondary" style="background: linear-gradient(135deg, #ffb86c, #ff79c6); color: #0f0f23;">Kelola Stok</a>
             <a href="settings.php" class="btn btn-secondary" style="background: linear-gradient(135deg, #6272a4, #44475a); color: #f8f8f2;">⚙️ Settings</a>
             <a href="logout.php" class="btn btn-danger">Logout</a>
         </div>

```

Dengan file-file baru ini, Anda sekarang memiliki sistem manajemen stok yang fungsional dan terintegrasi dengan baik ke dalam dashboard admin Anda.

<!--
[PROMPT_SUGGESTION]Bisakah Anda menambahkan fitur untuk mengelola data pembelian dari supplier (CRUD table_pembelian)?[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Saya ingin menambahkan notifikasi email ke admin saat stok menipis, bisakah Anda membantu?[/PROMPT_SUGGESTION]
-->
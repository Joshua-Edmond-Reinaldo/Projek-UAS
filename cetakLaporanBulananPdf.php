<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require 'fpdf.php';
require 'koneksi.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$nama_bulan = date('F', mktime(0, 0, 0, $bulan, 10));

class PDF extends FPDF {
    function Header() {
        global $nama_bulan, $tahun;
        $this->SetFont('Courier', 'B', 16);
        $this->Cell(0, 10, "Laporan Keuangan Bulanan: $nama_bulan $tahun", 0, 1, 'C');
        $this->SetLineWidth(0.5);
        $this->Line(10, 20, 200, 20);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Courier', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// --- Bagian Penjualan ---
$pdf->SetFont('Courier', 'B', 12);
$pdf->Cell(0, 10, 'A. Pemasukan (Penjualan Software)', 0, 1);

$pdf->SetFont('Courier', 'B', 10);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Tanggal', 1, 0, 'C', true);
$pdf->Cell(80, 8, 'Nama Software', 1, 0, 'L', true);
$pdf->Cell(20, 8, 'Jml', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Total (Rp)', 1, 1, 'R', true);

$pdf->SetFont('Courier', '', 10);
$sql_jual = "SELECT * FROM table_penjualan WHERE MONTH(tanggal_transaksi) = '$bulan' AND YEAR(tanggal_transaksi) = '$tahun' AND status_pembayaran = 'Lunas'";
$res_jual = $conn->query($sql_jual);
$total_jual = 0;
$no = 1;

while ($row = $res_jual->fetch_assoc()) {
    $pdf->Cell(10, 7, $no++, 1, 0, 'C');
    $pdf->Cell(30, 7, $row['tanggal_transaksi'], 1, 0, 'C');
    $pdf->Cell(80, 7, substr($row['nama_software'], 0, 35), 1, 0, 'L');
    $pdf->Cell(20, 7, $row['jumlah_lisensi'], 1, 0, 'C');
    $pdf->Cell(50, 7, number_format($row['harga'], 0, ',', '.'), 1, 1, 'R');
    $total_jual += $row['harga'];
}
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(140, 8, 'Total Pemasukan', 1, 0, 'R');
$pdf->Cell(50, 8, number_format($total_jual, 0, ',', '.'), 1, 1, 'R');

$pdf->Output();
?>
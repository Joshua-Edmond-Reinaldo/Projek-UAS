<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

require 'fpdf.php';
require 'koneksi.php';

class PDF extends FPDF {
    // Header halaman
    function Header() {
        $this->SetFont('Courier', 'B', 16);
        $this->Cell(0, 10, 'Laporan Penjualan Software', 0, 1, 'C');
        $this->SetLineWidth(0.5);
        $this->Line(10, 20, 287, 20);
        $this->Ln(10);
    }

    // Footer halaman
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Courier', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Membuat objek PDF (Landscape, mm, A4)
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// --- Styling Header Tabel ---
$pdf->SetFont('Courier', 'B', 11);
$pdf->SetFillColor(45, 45, 66);
$pdf->SetTextColor(80, 250, 123);
$pdf->SetDrawColor(80, 250, 123);
$pdf->SetLineWidth(0.3);

// Header Kolom
$header = array('No', 'Username', 'Nama Software', 'Tgl Transaksi', 'Harga', 'Status');
$w = array(15, 35, 60, 45, 35, 75);

foreach($header as $i => $col) {
    $pdf->Cell($w[$i], 12, $col, 1, 0, 'C', true);
}
$pdf->Ln();

// --- Styling Data Tabel ---
$pdf->SetFont('Courier', '', 11);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);

$sql = "SELECT * FROM table_penjualan";
$result = $conn->query($sql);

$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell($w[0], 10, $no++, 1, 0, 'C');
    $pdf->Cell($w[1], 10, $row['username'], 1, 0, 'C');
    $pdf->Cell($w[2], 10, substr($row['nama_software'], 0, 30), 1, 0, 'L');
    $pdf->Cell($w[3], 10, $row['tanggal_transaksi'], 1, 0, 'C');
    $pdf->Cell($w[4], 10, number_format($row['harga']), 1, 0, 'R');
    $pdf->Cell($w[5], 10, $row['status_pembayaran'], 1, 0, 'C');
    $pdf->Ln();
}
$pdf->Output();
?>
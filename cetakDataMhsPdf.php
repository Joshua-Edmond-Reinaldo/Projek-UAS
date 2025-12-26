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
        // Set font Courier Bold 16 (Monospace style)
        $this->SetFont('Courier', 'B', 16);
        // Judul
        $this->Cell(0, 10, 'Laporan Daftar Mahasiswa', 0, 1, 'C');
        // Garis bawah judul
        $this->SetLineWidth(0.5);
        $this->Line(10, 20, 287, 20);
        $this->Ln(10);
    }

    // Footer halaman
    function Footer() {
        // Posisi 1.5 cm dari bawah
        $this->SetY(-15);
        // Font Courier Italic 8
        $this->SetFont('Courier', 'I', 8);
        // Nomor halaman
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Membuat objek PDF (Landscape, mm, A4)
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// --- Styling Header Tabel (Menyesuaikan Tema Neon) ---
$pdf->SetFont('Courier', 'B', 11);
$pdf->SetFillColor(45, 45, 66);    // Background Gelap (#2d2d42)
$pdf->SetTextColor(80, 250, 123);  // Teks Hijau Neon (#50fa7b)
$pdf->SetDrawColor(80, 250, 123);  // Border Hijau Neon
$pdf->SetLineWidth(0.3);

// Header Kolom
$header = array('No', 'NIM', 'Nama', 'Tempat Lahir', 'Tanggal Lahir', 'Email');
$w = array(15, 35, 60, 45, 35, 75); // Lebar kolom

foreach($header as $i => $col) {
    $pdf->Cell($w[$i], 12, $col, 1, 0, 'C', true);
}
$pdf->Ln();

// --- Styling Data Tabel ---
$pdf->SetFont('Courier', '', 11);
$pdf->SetTextColor(0, 0, 0);       // Teks Hitam (agar mudah dibaca saat dicetak)
$pdf->SetDrawColor(0, 0, 0);       // Border Hitam standar

// Query Data
$sql = "SELECT * FROM table_mhs";
$result = $conn->query($sql);
if (!$result) {
    die("Query Error: " . $conn->error);
}

$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell($w[0], 10, $no++, 1, 0, 'C');
    $pdf->Cell($w[1], 10, $row['nim'], 1, 0, 'C');
    $pdf->Cell($w[2], 10, $row['nama'], 1, 0, 'L');
    $pdf->Cell($w[3], 10, $row['tempatLahir'], 1, 0, 'L');
    $pdf->Cell($w[4], 10, $row['tanggalLahir'], 1, 0, 'C');
    $pdf->Cell($w[5], 10, $row['email'], 1, 0, 'L');
    $pdf->Ln();
}

// Output PDF
$pdf->Output();
?>
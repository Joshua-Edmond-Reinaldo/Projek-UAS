<?php
session_start();
require 'koneksi.php';
require 'fpdf.php'; // Pastikan file fpdf.php ada di direktori yang sama

// 1. Security Check: User harus login
if (!isset($_SESSION['username'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// 2. Ambil ID Invoice dari URL
if (!isset($_GET['id'])) {
    die("Error: ID Invoice tidak ditemukan.");
}

$id = (int)$_GET['id'];
$username = $_SESSION['username'];

// 3. Security Check: Ambil invoice dan verifikasi kepemilikan
$stmt = $conn->prepare("SELECT * FROM table_penjualan WHERE id = ? AND username = ?");
$stmt->bind_param("is", $id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Akses ditolak. Invoice ini bukan milik Anda atau tidak ditemukan.");
}
$invoice = $result->fetch_assoc();
$stmt->close();

// 4. Mulai Membuat PDF
class PDF_Invoice extends FPDF
{
    // Header halaman
    function Header()
    {
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(45, 45, 66);
        $this->Cell(0, 10, 'INVOICE', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(150);
        $this->Cell(0, 5, 'CyberSoft Store', 0, 1, 'C');
        $this->Ln(10);
    }

    // Footer halaman
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, 'Terima kasih telah berbelanja di CyberSoft Store!', 0, 0, 'C');
    }
}

$pdf = new PDF_Invoice();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(0);

// Info Perusahaan & Pelanggan
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(95, 6, 'Ditagihkan Kepada:', 0, 0, 'L');
$pdf->Cell(95, 6, 'Detail Invoice:', 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(95, 6, htmlspecialchars_decode($invoice['nama_pembeli']), 0, 0, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(95, 6, 'Invoice #: ' . str_pad($invoice['id'], 6, '0', STR_PAD_LEFT), 0, 1, 'R');

$pdf->Cell(95, 6, htmlspecialchars_decode($invoice['alamat']), 0, 0, 'L');
$pdf->Cell(95, 6, 'Tanggal: ' . date('d F Y', strtotime($invoice['tanggal_transaksi'])), 0, 1, 'R');

$pdf->Cell(95, 6, htmlspecialchars_decode($invoice['email']), 0, 0, 'L');
$pdf->Cell(95, 6, 'Status: ' . $invoice['status_pembayaran'], 0, 1, 'R');

$pdf->Ln(15);

// Header Tabel
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(230, 230, 250);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(210, 210, 255);
$pdf->SetLineWidth(.3);
$pdf->Cell(110, 8, 'Deskripsi Produk', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Jumlah', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Harga', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Subtotal', 1, 1, 'C', true);

// Data Tabel
$pdf->SetFont('Arial', '', 11);
$pdf->SetFillColor(255);
$pdf->Cell(110, 10, htmlspecialchars_decode($invoice['nama_software']), 'LR', 0, 'L');
$pdf->Cell(20, 10, $invoice['jumlah_lisensi'], 'LR', 0, 'C');
$pdf->Cell(30, 10, 'Rp ' . number_format($invoice['harga'] / $invoice['jumlah_lisensi'], 0, ',', '.'), 'LR', 0, 'R');
$pdf->Cell(30, 10, 'Rp ' . number_format($invoice['harga'], 0, ',', '.'), 'LR', 1, 'R');

// Garis bawah tabel
$pdf->Cell(190, 0, '', 'T');
$pdf->Ln(10);

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 8, '', 0, 0);
$pdf->Cell(30, 8, 'TOTAL', 1, 0, 'C');
$pdf->SetFillColor(230, 230, 250);
$pdf->Cell(30, 8, 'Rp ' . number_format($invoice['harga'], 0, ',', '.'), 1, 1, 'R', true);

$pdf->Output('D', 'Invoice-'.$invoice['id'].'.pdf');

?>
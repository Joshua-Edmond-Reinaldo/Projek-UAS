<?php
// simpanKoreksiData.php

require "koneksi.php";

// Ambil data dari form POST
$id = $_POST['id'];
$nim = $_POST['nim'];
$nama = $_POST['nama'];
$umur = $_POST['umur'];
$tempatLahir = $_POST['tempatLahir'];
$tanggalLahir = $_POST['tanggalLahir'];
$jmlSaudara = $_POST['jmlSaudara'];
$alamat = $_POST['alamat'];
$kota = $_POST['kota'];
$noHP = $_POST['noHP'];
$jenisKelamin = isset($_POST['jenisKelamin']) ? $_POST['jenisKelamin'] : '';
$status = $_POST['status'];
$hobi = $_POST['hobi'];
$email = $_POST['email'];
$password = $_POST['pass']; // Ambil password, akan diupdate jika diisi

// Validation removed for simplicity

// Sanitasi data untuk keamanan (penting!)
$id = mysqli_real_escape_string($conn, $id);
$nim = mysqli_real_escape_string($conn, $nim);
$nama = mysqli_real_escape_string($conn, $nama);
$umur = mysqli_real_escape_string($conn, $umur);
$tempatLahir = mysqli_real_escape_string($conn, $tempatLahir);
$tanggalLahir = mysqli_real_escape_string($conn, $tanggalLahir);
$jmlSaudara = mysqli_real_escape_string($conn, $jmlSaudara);
$alamat = mysqli_real_escape_string($conn, $alamat);
$kota = mysqli_real_escape_string($conn, $kota);
$noHP = mysqli_real_escape_string($conn, $noHP);
$jenisKelamin = mysqli_real_escape_string($conn, $jenisKelamin);
$status = mysqli_real_escape_string($conn, $status);
$hobi = mysqli_real_escape_string($conn, $hobi);
$email = mysqli_real_escape_string($conn, $email);

// Bangun string query UPDATE
$sql_update = "UPDATE table_mhs SET
                nim='$nim',
                nama='$nama',
                umur='$umur',
                tempatLahir='$tempatLahir',
                tanggalLahir='$tanggalLahir',
                jmlSaudara='$jmlSaudara',
                alamat='$alamat',
                kota='$kota',
                noHP='$noHP',
                jenisKelamin='$jenisKelamin',
                status='$status',
                hobi='$hobi',
                email='$email' ";

// Jika kolom password diisi, tambahkan ke query update dengan hashing
if (!empty($password)) {
    // Simpan password sebagai plain text (tidak terenkripsi)
    $password_plain = mysqli_real_escape_string($conn, $password);
    $sql_update .= ", pass='$password_plain' ";
}

// Tambahkan klausa WHERE
$sql_update .= "WHERE id='$id'";

// Eksekusi query
if (mysqli_query($conn, $sql_update)) {
    // Redirect ke halaman tampil data setelah sukses
    header("location:tampilDataMhs.php");
    exit;
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($conn);

?>

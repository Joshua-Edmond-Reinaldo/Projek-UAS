<?php
$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "penjualan_software"; // Pastikan nama database ini sudah dibuat di phpMyAdmin

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error); 
}

// Fungsi Catat Log Aktivitas
function catatLog($conn, $username, $action, $details) {
    $username = mysqli_real_escape_string($conn, $username);
    $action = mysqli_real_escape_string($conn, $action);
    $details = mysqli_real_escape_string($conn, $details);
    $sql = "INSERT INTO table_logs (username, action, details) VALUES ('$username', '$action', '$details')";
    $conn->query($sql);
}
?>

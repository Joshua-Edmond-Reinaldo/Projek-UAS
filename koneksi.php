<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database_toko"; // Pastikan nama database ini sudah dibuat di phpMyAdmin

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Fungsi Catat Log Aktivitas
function catatLog($conn, $username, $action, $details) {
    $username = mysqli_real_escape_string($conn, $username);
    $action = mysqli_real_escape_string($conn, $action);
    $details = mysqli_real_escape_string($conn, $details);
    $sql = "INSERT INTO table_logs (username, action, details) VALUES ('$username', '$action', '$details')";
    $conn->query($sql);
}
?>

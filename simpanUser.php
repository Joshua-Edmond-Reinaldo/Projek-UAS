<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    $level = mysqli_real_escape_string($conn, $_POST['level']);

    // Cek username duplikat
    $check = $conn->query("SELECT id FROM table_user WHERE username = '$username'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.location.href='registrasiUser.php';</script>";
        exit;
    }

    // Insert data user baru
    $sql = "INSERT INTO table_user (username, email, password, level) VALUES ('$username', '$email', '$password', '$level')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
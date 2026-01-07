<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    header("location:login.php");
    exit;
}

require "koneksi.php";

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Ambil username sebelum dihapus untuk log
    $res = $conn->query("SELECT username FROM table_user WHERE id=$id");
    if ($res->num_rows > 0) {
        $user_to_delete = $res->fetch_assoc()['username'];

        // Hapus dari table_user
        $sql = "DELETE FROM table_user WHERE id = $id";
        if ($conn->query($sql)) {
            catatLog($conn, $_SESSION['username'], 'Hapus User', "Menghapus user: $user_to_delete");
            header("location: tampilDataUser.php");
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}
?>
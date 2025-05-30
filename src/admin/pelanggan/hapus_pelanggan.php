<?php
include '../../user/config.php';

session_start();
    $loggedIn = isset($_SESSION['nama']); 

// Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['nama'])) {
        header("Location: ../user/login.php");
        exit();
    }

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: ../dashboard_admin.php");
    exit();
}

$id_pelanggan = $_GET['id'];

// Cek apakah pelanggan masih memiliki pesanan
$check_orders = $conn->query("SELECT * FROM pesanan_dekas WHERE id_pelanggan = $id_pelanggan");

if($check_orders->num_rows > 0) {
    echo "<script>alert('Tidak dapat menghapus pelanggan yang masih memiliki pesanan'); window.location='../pelanggan_crud.php';</script>";
    exit();
}

// Query untuk menghapus data
$sql = "DELETE FROM pelanggan_dekas WHERE id_pelanggan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pelanggan);

if($stmt->execute()) {
    echo "<script>alert('Data pelanggan berhasil dihapus'); window.location='../pelanggan_crud.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data pelanggan: " . $conn->error . "'); window.location='../pelanggan_crud.php';</script>";
}

$stmt->close();
?>
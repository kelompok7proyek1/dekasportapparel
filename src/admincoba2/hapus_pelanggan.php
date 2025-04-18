<?php
include '../user/config.php';

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id_pelanggan = $_GET['id'];

// Cek apakah pelanggan masih memiliki pesanan
$check_orders = $conn->query("SELECT * FROM pesanan_dekas WHERE id_pelanggan = $id_pelanggan");

if($check_orders->num_rows > 0) {
    echo "<script>alert('Tidak dapat menghapus pelanggan yang masih memiliki pesanan'); window.location='admin_dashboard.php';</script>";
    exit();
}

// Query untuk menghapus data
$sql = "DELETE FROM pelanggan_dekas WHERE id_pelanggan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pelanggan);

if($stmt->execute()) {
    echo "<script>alert('Data pelanggan berhasil dihapus'); window.location='admin_dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data pelanggan: " . $conn->error . "'); window.location='admin_dashboard.php';</script>";
}

$stmt->close();
?>
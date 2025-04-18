<?php
include '../user/config.php';

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id_pesanan = $_GET['id'];

// Ambil id_pelanggan sebelum menghapus untuk update jumlah pesanan
$result = $conn->query("SELECT id_pelanggan FROM pesanan_dekas WHERE id_pesanan = $id_pesanan");

if($result->num_rows == 0) {
    echo "<script>alert('Data pesanan tidak ditemukan'); window.location='admin_dashboard.php';</script>";
    exit();
}

$pesanan = $result->fetch_assoc();
$id_pelanggan = $pesanan['id_pelanggan'];

// Query untuk menghapus data
$sql = "DELETE FROM pesanan_dekas WHERE id_pesanan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pesanan);

if($stmt->execute()) {
    // Update jumlah pesanan pada tabel pelanggan
    $conn->query("UPDATE pelanggan_dekas SET jumlah_pesanan = jumlah_pesanan - 1 WHERE id_pelanggan = $id_pelanggan AND jumlah_pesanan > 0");
    
    echo "<script>alert('Data pesanan berhasil dihapus'); window.location='admin_dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data pesanan: " . $conn->error . "'); window.location='admin_dashboard.php';</script>";
}

$stmt->close();
?>
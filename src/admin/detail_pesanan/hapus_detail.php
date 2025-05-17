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
    header("Location: ../dashboard_coba2.php");
    exit();
}

$id_detail = $_GET['id'];

// Ambil id_pesanan sebelum menghapus untuk update jumlah pesanan
$result = $conn->query("SELECT * FROM detail_pesanan WHERE id_detail = $id_detail");

if($result->num_rows == 0) {
    echo "<script>alert('Detail pesanan tidak ditemukan'); window.location='../detail_pesanan.php';</script>";
    exit();
}

$detail_pesanan = $result->fetch_assoc();
$id_detail = $detail_pesanan['id_detail']; // <- ini pembetulannya

// Query untuk menghapus data
$sql = "DELETE FROM detail_pesanan WHERE id_detail = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_detail);

if($stmt->execute()) {
    // (Opsional) Update jumlah pesanan pada tabel pesanan/pelanggan, jika perlu
    // Misalnya: $conn->query("UPDATE pesanan_dekas SET jumlah_detail = jumlah_detail - 1 WHERE id_pesanan = $id_pesanan");

    echo "<script>alert('Data pesanan berhasil dihapus'); window.location='../detail_pesanan.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data pesanan: " . $conn->error . "'); window.location='../detail_pesanan.php';</script>";
}

$stmt->close();
?>

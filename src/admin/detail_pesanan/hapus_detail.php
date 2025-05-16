<?php
include '../../user/config.php';

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: ../dashboard_coba2.php");
    exit();
}

$id_jersey = $_GET['id'];

// Ambil id_pesanan sebelum menghapus untuk update jumlah pesanan
$result = $conn->query("SELECT id_pesanan FROM detail_pesanan WHERE id_jersey = $id_jersey");

if($result->num_rows == 0) {
    echo "<script>alert('Detail pesanan tidak ditemukan'); window.location='../detail_pesanan.php';</script>";
    exit();
}

$detail_pesanan = $result->fetch_assoc();
$id_pesanan = $detail_pesanan['id_pesanan']; // <- ini pembetulannya

// Query untuk menghapus data
$sql = "DELETE FROM detail_pesanan WHERE id_jersey = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_jersey);

if($stmt->execute()) {
    // (Opsional) Update jumlah pesanan pada tabel pesanan/pelanggan, jika perlu
    // Misalnya: $conn->query("UPDATE pesanan_dekas SET jumlah_detail = jumlah_detail - 1 WHERE id_pesanan = $id_pesanan");

    echo "<script>alert('Data pesanan berhasil dihapus'); window.location='../detail_pesanan.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data pesanan: " . $conn->error . "'); window.location='../detail_pesanan.php';</script>";
}

$stmt->close();
?>

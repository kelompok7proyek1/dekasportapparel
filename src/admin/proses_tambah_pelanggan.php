<?php
include '../config.php';

$nama = $_POST['nama'];
$no_hp = $_POST['no_hp'];
$alamat = $_POST['alamat'];
$jumlah = $_POST['jumlah_pesanan'];

$query = "INSERT INTO pelanggan_dekas (nama, no_hp, alamat, jumlah_pesanan) 
        VALUES ('$nama', '$no_hp', '$alamat', '$jumlah')";

if ($conn->query($query)) {
    header("Location: admin_dashboard.php");
} else {
    echo "Gagal menambah data pelanggan: " . $conn->error;
}
?>

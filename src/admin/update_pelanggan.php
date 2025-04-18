<?php
include '../user/config.php';

$id = $_POST['id_pelanggan'];
$nama = $_POST['nama'];
$no_hp = $_POST['no_hp'];
$alamat = $_POST['alamat'];
$jumlah = $_POST['jumlah_pesanan'];

$query = "UPDATE pelanggan_dekas 
        SET nama='$nama', no_hp='$no_hp', alamat='$alamat', jumlah_pesanan='$jumlah' 
        WHERE id_pelanggan=$id";

if ($conn->query($query)) {
    header("Location: admin_dashboard.php");
} else {
    echo "Gagal update data pelanggan: " . $conn->error;
}
?>

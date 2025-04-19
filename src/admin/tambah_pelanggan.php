<?php
include '../user/config.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

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

}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pelanggan</title>
</head>
<body>
    <h2>Tambah Data Pelanggan</h2>
    <form method="post">
        <label>Nama:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>No HP:</label><br>
        <input type="text" name="no_hp" required><br><br>

        <label>Alamat:</label><br>
        <input type="text" name="alamat" required><br><br>

        <label>Jumlah Pesanan:</label><br>
        <input type="number" name="jumlah_pesanan" required><br><br>

        <input type="submit" value="Tambah">
    </form>
</body>
</html>


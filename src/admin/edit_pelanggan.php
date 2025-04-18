<?php
include '../user/config.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM pelanggan_dekas WHERE id_pelanggan = $id");
$data = $result->fetch_assoc();
?>

<form action="update_pelanggan.php" method="POST">
    <input type="hidden" name="id_pelanggan" value="<?= $data['id_pelanggan'] ?>">

    <label>Nama:</label><br>
    <input type="text" name="nama" value="<?= $data['nama'] ?>" required><br>

    <label>No HP:</label><br>
    <input type="text" name="no_hp" value="<?= $data['no_hp'] ?>" required><br>

    <label>Alamat:</label><br>
    <textarea name="alamat" required><?= $data['alamat'] ?></textarea><br>

    <label>Jumlah Pesanan:</label><br>
    <input type="number" name="jumlah_pesanan" value="<?= $data['jumlah_pesanan'] ?>" required><br><br>

    <input type="submit" value="Update">
</form>

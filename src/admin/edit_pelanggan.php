<?php
include '../user/config.php';

if (!isset($_GET['id'])) {
    echo "ID pelanggan tidak ditemukan di URL.";
    exit;
}
    $id = intval($_GET['id']); // biar aman (hindari SQL injection)
    $result = $conn->query("SELECT * FROM pelanggan_dekas WHERE id_pelanggan = $id");
    $data = $result->fetch_assoc();

    if (!$data) {
        echo "Data pelanggan tidak ditemukan.";
        exit;
    }


if($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_pelanggan'];
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $jumlah = $_POST['jumlah_pesanan'];

        $query = "UPDATE pelanggan_dekas 
        SET nama='$nama', 
        no_hp='$no_hp', 
        alamat='$alamat', 
        jumlah_pesanan='$jumlah' 
        WHERE id_pelanggan=$id";

if ($conn->query($query)) {
    header("Location: admin_dashboard.php");
} else {
    echo "Gagal update data pelanggan: " . $conn->error;
}   }
?>



<form method="POST">
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

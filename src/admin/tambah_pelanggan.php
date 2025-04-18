<?php include '../user/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pelanggan</title>
</head>
<body>
    <h2>Tambah Data Pelanggan</h2>
    <form method="post" action="proses_tambah_pelanggan.php">
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


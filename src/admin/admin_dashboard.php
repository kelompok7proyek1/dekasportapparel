<?php
    include '../user/config.php';
    $resultpelanggan = $conn->query("SELECT * FROM pelanggan_dekas");
    $resultpesanan = $conn->query("SELECT * FROM pesanan_dekas");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <h2>Dashboard Admin</h2>
    <h3>Tabel Pelanggan</h3>
    <a href="tambah_pelanggan.php">Tambah Pelanggan</a>
    <table border="1" cellpadding="8" style="border-collapse: collapse; margin-top: 20px;">
        <tr>
            <th>ID Pelanggan</th>
            <th>Nama Pelanggan</th>
            <th>No Hp</th>
            <th>Alamat</th>
            <th>Jumlah Pesanan</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $resultpelanggan->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_pelanggan'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['no_hp'] ?></td>
                <td><?= $row['alamat'] ?></td>
                <td><?= $row['jumlah_pesanan'] ?></td>
                <td>
                    <a href="edit_pesanan.php?id=<?= $row['id_pelanggan'] ?>">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <h3>Tabel Pesanan</h3>
    <a href="tambah_pesanan.php">Tambah Pesanan</a>
    <table border="1" cellpadding="8" style="border-collapse: collapse; margin-top: 20px;">
        <tr>
            <th>ID Pelanggan</th>
            <th>ID Pesanan</th>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Status</th>
            <th>Total Order</th>
            <th>In Progress</th>
            <th>Completed</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = $resultpesanan->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_pelanggan'] ?></td>
                <td><?= $row['id_pesanan'] ?></td>
                <td><?= $row['tanggal_pemesanan'] ?></td>
                <td><?= $row['total_harga'] ?></td>
                <td><?= $row['status_pemesanan'] ?></td>
                <td><?= $row['total_order'] ?></td>
                <td><?= $row['in_progres'] ?></td>
                <td><?= $row['completed'] ?></td>
                <td>
                    <a href="edit_pesanan.php?id=<?= $row['id_pesanan'] ?>">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

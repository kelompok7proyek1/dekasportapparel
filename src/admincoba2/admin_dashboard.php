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
    <style>
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            margin-right: 5px;
            border-radius: 3px;
            display: inline-block;
            color: white;
        }
        .btn-edit {
            background-color: #4CAF50;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .tambah-btn {
            display: inline-block;
            background-color: #2196F3;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .container {
            width: 95%;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard Admin</h2>
        
        <h3>Tabel Pelanggan</h3>
        <a href="tambah_pelanggan.php" class="tambah-btn">Tambah Pelanggan</a>
        <table>
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
                        <a href="edit_pelanggan.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-edit">Edit</a>
                        <a href="hapus_pelanggan.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus data pelanggan ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <h3>Tabel Pesanan</h3>
        <a href="tambah_pesanan.php" class="tambah-btn">Tambah Pesanan</a>
        <table>
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
                        <a href="edit_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-edit">Edit</a>
                        <a href="hapus_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus pesanan ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
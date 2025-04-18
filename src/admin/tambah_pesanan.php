<?php
include '../user/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal_pemesanan'];
    $harga = $_POST['total_harga'];
    $status = $_POST['status_pemesanan'];
    $total = $_POST['total_order'];
    $progress = $_POST['in_progres'];
    $complete = $_POST['completed'];

    $sql = "INSERT INTO pesanan_dekas (tanggal_pemesanan, total_harga, status_pemesanan, total_order, in_progres, completed)
            VALUES ('$tanggal', '$harga', '$status', '$total', '$progress', '$complete')";

    if ($conn->query($sql)) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Gagal menambahkan data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pesanan</title>
</head>
<body>
    <h2>Tambah Pesanan</h2>
    <form method="POST">
        <label>Tanggal:</label><br>
        <input type="date" name="tanggal_pemesanan" required><br>
        <label>Total Harga:</label><br>
        <input type="text" name="total_harga" required><br>
        <label>Status:</label><br>
        <input type="text" name="status_pemesanan" required><br>
        <label>Total Order:</label><br>
        <input type="number" name="total_order" required><br>
        <label>In Progress:</label><br>
        <input type="number" name="in_progres"><br>
        <label>Completed:</label><br>
        <input type="number" name="completed"><br><br>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>

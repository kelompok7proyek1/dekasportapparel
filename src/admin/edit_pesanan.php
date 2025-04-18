<?php
include '../user/config.php';

$id = $_POST['id'];
$result = $conn->query("SELECT * FROM pesanan_dekas WHERE id_pesanan = $id");
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal_pemesanan'];
    $harga = $_POST['total_harga'];
    $status = $_POST['status_pemesanan'];
    $total = $_POST['total_order'];
    $progress = $_POST['in_progres'];
    $complete = $_POST['completed'];

    $sql = "UPDATE pesanan_dekas SET 
            tanggal_pemesanan = '$tanggal',
            total_harga = '$harga',
            status_pemesanan = '$status',
            total_order = '$total',
            in_progres = '$progress',
            completed = '$complete'
            WHERE id_pesanan = $id";

    if ($conn->query($sql)) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Gagal mengupdate data: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pesanan</title>
</head>
<body>
    <h2>Edit Pesanan</h2>
    <form method="POST">
        <label>Tanggal:</label><br>
        <input type="date" name="tanggal_pemesanan" value="<?= $data['tanggal_pemesanan'] ?>"><br>
        <label>Total Harga:</label><br>
        <input type="text" name="total_harga" value="<?= $data['total_harga'] ?>"><br>
        <label>Status:</label><br>
        <input type="text" name="status_pemesanan" value="<?= $data['status_pemesanan'] ?>"><br>
        <label>Total Order:</label><br>
        <input type="number" name="total_order" value="<?= $data['total_order'] ?>"><br>
        <label>In Progress:</label><br>
        <input type="number" name="in_progres" value="<?= $data['in_progres'] ?>"><br>
        <label>Completed:</label><br>
        <input type="number" name="completed" value="<?= $data['completed'] ?>"><br><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>

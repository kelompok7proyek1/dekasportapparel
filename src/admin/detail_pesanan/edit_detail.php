<?php
include '../../user/config.php';

// Cek apakah ada parameter ID
if (!isset($_GET['id'])) {
    header("Location: ../dashboard_coba2.php");
    exit();
}

$id_detail = $_GET['id'];

// Ambil data detail_pesanan berdasarkan ID
$result = $conn->query("SELECT * FROM detail_pesanan WHERE id_detail = $id_detail");

if ($result->num_rows == 0) {
    echo "<script>alert('Data tidak ditemukan'); window.location='../dashboard_coba2.php';</script>";
    exit();
}

$detail = $result->fetch_assoc();

// Proses form jika disubmit
if (isset($_POST['submit'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $jenis_jersey = $_POST['jenis_jersey'];
    $bahan_jersey = $_POST['bahan_jersey'];
    $paket_jersey = $_POST['paket_jersey'];
    $nama_pemain = $_POST['nama_pemain'];
    $nomor_punggung = $_POST['nomor_punggung'];
    $logo = $_POST['logo'];
    $ukuran = $_POST['ukuran'];
    $motif = $_POST['motif'];
    $total_jersey = $_POST['total_jersey'];
    $kode_jersey = $_POST['kode_jersey'];

    // Query update detail_pesanan
    $sql = "UPDATE detail_pesanan 
            SET id_pesanan=?, jenis_jersey=?, bahan_jersey=?, paket_jersey=?, nama_pemain=?, nomor_punggung=?, logo=?, ukuran=?, motif=?, total_jersey=?, kode_jersey=? 
            WHERE id_detail=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssisi", $id_pesanan, $jenis_jersey, $bahan_jersey, $paket_jersey, $nama_pemain, $nomor_punggung, $logo, $ukuran, $motif, $total_jersey, $kode_jersey, $id_detail);

    if ($stmt->execute()) {
        echo "<script>alert('Detail pesanan berhasil diupdate'); window.location='../dashboard_coba2.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate: " . $conn->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Detail Pesanan</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .btn-cancel {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<h2>Edit Detail Pesanan</h2>

<div class="form-container">
    <form method="POST">
        <div class="form-group">
            <label for="id_pesanan">ID Pesanan:</label>
            <input type="number" name="id_pesanan" value="<?= $detail['id_pelanggan'] ?>" required>
        </div>
        <div class="form-group">
            <label for="jenis_jersey">Jenis Jersey:</label>
            <input type="text" name="jenis_jersey" value="<?= $detail['jenis_jersey'] ?>" required>
        </div>
        <div class="form-group">
            <label for="bahan_jersey">Bahan Jersey:</label>
            <input type="text" name="bahan_jersey" value="<?= $detail['bahan_jersey'] ?>" required>
        </div>
        <div class="form-group">
            <label for="paket_jersey">Paket Jersey:</label>
            <input type="text" name="paket_jersey" value="<?= $detail['paket_jersey'] ?>" required>
        </div>
        <div class="form-group">
            <label for="nama_pemain">Nama Pemain:</label>
            <input type="text" name="nama_pemain" value="<?= $detail['nama_pemain'] ?>" required>
        </div>
        <div class="form-group">
            <label for="nomor_punggung">Nomor Punggung:</label>
            <input type="text" name="nomor_punggung" value="<?= $detail['nomor_punggung'] ?>" required>
        </div>
        <div class="form-group">
            <label for="logo">Logo:</label>
            <input type="text" name="logo" value="<?= $detail['logo'] ?>" required>
        </div>
        <div class="form-group">
            <label for="ukuran">Ukuran:</label>
            <input type="text" name="ukuran" value="<?= $detail['ukuran'] ?>" required>
        </div>
        <div class="form-group">
            <label for="motif">Motif:</label>
            <input type="text" name="motif" value="<?= $detail['motif'] ?>" required>
        </div>
        <div class="form-group">
            <label for="total_jersey">Total Jersey:</label>
            <input type="number" name="total_jersey" value="<?= $detail['total_jersey'] ?>" required>
        </div>
        <div class="form-group">
            <label for="kode_jersey">Kode Jersey:</label>
            <input type="text" name="kode_jersey" value="<?= $detail['kode_jersey'] ?>" required>
        </div>

        <div class="form-group">
            <button type="submit" name="submit" class="btn-submit">Update</button>
            <a href="../dashboard_coba2.php" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>

</body>
</html>

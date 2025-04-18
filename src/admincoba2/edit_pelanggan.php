<?php
include '../user/config.php';

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id_pelanggan = $_GET['id'];

// Ambil data pelanggan berdasarkan ID
$result = $conn->query("SELECT * FROM pelanggan_dekas WHERE id_pelanggan = $id_pelanggan");

if($result->num_rows == 0) {
    echo "<script>alert('Data pelanggan tidak ditemukan'); window.location='admin_dashboard.php';</script>";
    exit();
}

$pelanggan = $result->fetch_assoc();

// Proses form jika ada submit
if(isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $jumlah_pesanan = $_POST['jumlah_pesanan'];
    
    // Query untuk update data
    $sql = "UPDATE pelanggan_dekas SET nama = ?, no_hp = ?, alamat = ?, jumlah_pesanan = ? WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $nama, $no_hp, $alamat, $jumlah_pesanan, $id_pelanggan);
    
    if($stmt->execute()) {
        echo "<script>alert('Data pelanggan berhasil diupdate'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data pelanggan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pelanggan</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            height: 100px;
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
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h2>Edit Data Pelanggan</h2>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Pelanggan:</label>
                <input type="text" id="nama" name="nama" value="<?= $pelanggan['nama'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="no_hp">No HP:</label>
                <input type="text" id="no_hp" name="no_hp" value="<?= $pelanggan['no_hp'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" required><?= $pelanggan['alamat'] ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="jumlah_pesanan">Jumlah Pesanan:</label>
                <input type="number" id="jumlah_pesanan" name="jumlah_pesanan" value="<?= $pelanggan['jumlah_pesanan'] ?>" required>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Update</button>
                <a href="admin_dashboard.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
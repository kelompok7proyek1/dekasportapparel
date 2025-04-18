<?php
include '../user/config.php';

// Proses form jika ada submit
if(isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    
    // Jumlah pesanan awal adalah 0
    $jumlah_pesanan = 0;
    
    // Query untuk insert data
    $sql = "INSERT INTO pelanggan_dekas (nama, no_hp, alamat, jumlah_pesanan) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $no_hp, $alamat, $jumlah_pesanan);
    
    if($stmt->execute()) {
        echo "<script>alert('Data pelanggan berhasil ditambahkan'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data pelanggan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pelanggan</title>
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
    <h2>Tambah Pelanggan Baru</h2>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Pelanggan:</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            
            <div class="form-group">
                <label for="no_hp">No HP:</label>
                <input type="text" id="no_hp" name="no_hp" required>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" required></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Simpan</button>
                <a href="admin_dashboard.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
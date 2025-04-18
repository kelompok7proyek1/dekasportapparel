<?php
include '../user/config.php';

// Ambil data pelanggan untuk dropdown
$pelanggan_result = $conn->query("SELECT * FROM pelanggan_dekas ORDER BY nama");

// Proses form jika ada submit
if(isset($_POST['submit'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $tanggal_pemesanan = $_POST['tanggal_pemesanan'];
    $total_harga = $_POST['total_harga'];
    $status_pemesanan = $_POST['status_pemesanan'];
    $total_order = $_POST['total_order'];
    $in_progres = $_POST['in_progres'];
    $completed = $_POST['completed'];
    
    // Query untuk insert data
    $sql = "INSERT INTO pesanan_dekas (id_pelanggan, tanggal_pemesanan, total_harga, status_pemesanan, total_order, in_progres, completed) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issiiis", $id_pelanggan, $tanggal_pemesanan, $total_harga, $status_pemesanan, $total_order, $in_progres, $completed);
    
    if($stmt->execute()) {
        // Update jumlah pesanan di tabel pelanggan
        $update_pelanggan = $conn->query("UPDATE pelanggan_dekas SET jumlah_pesanan = jumlah_pesanan + 1 WHERE id_pelanggan = $id_pelanggan");
        
        echo "<script>alert('Data pesanan berhasil ditambahkan'); window.location='dashboard_coba2.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data pesanan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pesanan</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
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
        .form-group input, .form-group select {
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
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>

    <h2>Tambah Pesanan Baru</h2>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_pelanggan">Pelanggan:</label>
                <select id="id_pelanggan" name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while($row = $pelanggan_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_pelanggan'] ?>"><?= $row['nama'] ?> (<?= $row['no_hp'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="tanggal_pemesanan">Tanggal Pemesanan:</label>
                <input type="date" id="tanggal_pemesanan" name="tanggal_pemesanan" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="total_harga">Total Harga:</label>
                <input type="number" id="total_harga" name="total_harga" required>
            </div>
            
            <div class="form-group">
                <label for="status_pemesanan">Status Pemesanan:</label>
                <select id="status_pemesanan" name="status_pemesanan" required>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="total_order">Total Order:</label>
                <input type="number" id="total_order" name="total_order" value="0" required>
            </div>
            
            <div class="form-group">
                <label for="in_progres">In Progress:</label>
                <input type="number" id="in_progres" name="in_progres" value="0" required>
            </div>
            
            <div class="form-group">
                <label for="completed">Completed:</label>
                <input type="text" id="completed" name="completed" value="0" required>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Simpan</button>
                <a href="dashboard_coba2.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php
include '../user/config.php';

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id_pesanan = $_GET['id'];

// Ambil data pesanan berdasarkan ID
$result = $conn->query("SELECT * FROM pesanan_dekas WHERE id_pesanan = $id_pesanan");

if($result->num_rows == 0) {
    echo "<script>alert('Data pesanan tidak ditemukan'); window.location='admin_dashboard.php';</script>";
    exit();
}

$pesanan = $result->fetch_assoc();

// Ambil data pelanggan untuk dropdown
$pelanggan_result = $conn->query("SELECT * FROM pelanggan_dekas ORDER BY nama");

// Proses form jika ada submit
if(isset($_POST['submit'])) {
    $id_pelanggan_lama = $pesanan['id_pelanggan'];
    $id_pelanggan_baru = $_POST['id_pelanggan'];
    $tanggal_pemesanan = $_POST['tanggal_pemesanan'];
    $total_harga = $_POST['total_harga'];
    $status_pemesanan = $_POST['status_pemesanan'];
    $total_order = $_POST['total_order'];
    $in_progres = $_POST['in_progres'];
    $completed = $_POST['completed'];
    
    // Query untuk update data
    $sql = "UPDATE pesanan_dekas SET id_pelanggan = ?, tanggal_pemesanan = ?, total_harga = ?, 
            status_pemesanan = ?, total_order = ?, in_progres = ?, completed = ? 
            WHERE id_pesanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issiiisi", $id_pelanggan_baru, $tanggal_pemesanan, $total_harga, 
                    $status_pemesanan, $total_order, $in_progres, $completed, $id_pesanan);
    
    if($stmt->execute()) {
        // Jika pelanggan berubah, update jumlah pesanan untuk pelanggan lama dan baru
        if($id_pelanggan_lama != $id_pelanggan_baru) {
            $conn->query("UPDATE pelanggan_dekas SET jumlah_pesanan = jumlah_pesanan - 1 WHERE id_pelanggan = $id_pelanggan_lama");
            $conn->query("UPDATE pelanggan_dekas SET jumlah_pesanan = jumlah_pesanan + 1 WHERE id_pelanggan = $id_pelanggan_baru");
        }
        
        echo "<script>alert('Data pesanan berhasil diupdate'); window.location='dashboard_coba2.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data pesanan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pesanan</title>
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


    <h2>Edit Data Pesanan</h2>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_pelanggan">Pelanggan:</label>
                <select id="id_pelanggan" name="id_pelanggan" required>
                    <?php while($row = $pelanggan_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_pelanggan'] ?>" <?= ($row['id_pelanggan'] == $pesanan['id_pelanggan']) ? 'selected' : '' ?>>
                            <?= $row['nama'] ?> (<?= $row['no_hp'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="tanggal_pemesanan">Tanggal Pemesanan:</label>
                <input type="date" id="tanggal_pemesanan" name="tanggal_pemesanan" value="<?= $pesanan['tanggal_pemesanan'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="total_harga">Total Harga:</label>
                <input type="number" id="total_harga" name="total_harga" value="<?= $pesanan['total_harga'] ?>" required>
            </div>
            
            <div class="form-group">
            <label for="status_pemesanan">Status Pemesanan:</label>
            <select id="status_pemesanan" name="status_pemesanan" required>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <script>
                document.getElementById('status_pemesanan').value = "<?php echo $pesanan['status_pemesanan']; ?>";
            </script>
            </div>
            
            <div class="form-group">
                <label for="total_order">Total Order:</label>
                <input type="number" id="total_order" name="total_order" value="<?= $pesanan['total_order'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="in_progres">In Progress:</label>
                <input type="number" id="in_progres" name="in_progres" value="<?= $pesanan['in_progres'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="completed">Completed:</label>
                <input type="text" id="completed" name="completed" value="<?= $pesanan['completed'] ?>" required>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Update</button>
                <a href="dashboard_coba2.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
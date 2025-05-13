<?php
include '../../user/config.php';

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: ../dashboard_coba2.php");
    exit();
}

$id_pesanan = $_GET['id'];

// Ambil data pesanan berdasarkan ID
$result = $conn->query("SELECT * FROM pesanan_dekas WHERE id_pesanan = $id_pesanan");

if($result->num_rows == 0) {
    echo "<script>alert('Data pesanan tidak ditemukan'); window.location='../dashboard_coba2.php';</script>";
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
    $harga_satuan = $_POST['harga_satuan'];
    $total_harga = $_POST['total_harga'];
    $status_produksi = $_POST['status_produksi'];
    $dalam_proses = $_POST['dalam_proses'];
    $selesai = $_POST['selesai'];
    
    // Query untuk update data
    $sql = "UPDATE pesanan_dekas SET id_pelanggan = ?, tanggal_pemesanan = ?, harga_satuan = ?, total_harga = ?, 
            status_produksi = ?, dalam_proses = ?, selesai = ? 
            WHERE id_pesanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssiii", $id_pelanggan_baru, $tanggal_pemesanan, $harga_satuan, $total_harga, 
                    $status_produksi, $dalam_proses, $selesai, $id_pesanan);
    
    if($stmt->execute()) {
        // Jika pelanggan berubah, update jumlah pesanan untuk pelanggan lama dan baru
        // if($id_pelanggan_lama != $id_pelanggan_baru) {
        //     $conn->query("UPDATE pelanggan_dekas SET jumlah_pesanan = jumlah_pesanan - 1 WHERE id_pelanggan = $id_pelanggan_lama");
        //     $conn->query("UPDATE pelanggan_dekas SET jumlah_pesanan = jumlah_pesanan + 1 WHERE id_pelanggan = $id_pelanggan_baru");
        // }
        
        echo "<script>alert('Data pesanan berhasil diupdate'); window.location='../dashboard_coba2.php';</script>";
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
                <label for="harga_satuan">Harga satuan:</label>
                <input type="date" id="harga_satuan" name="harga_satuan" value="<?= $pesanan['harga_satuan'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="total_harga">Total Harga:</label>
                <input type="number" id="total_harga" name="total_harga" value="<?= $pesanan['total_harga'] ?>" required>
            </div>
            
            <div class="form-group">
            <label for="status_produksi">Status Pemesanan:</label>
            <select id="status_produksi" name="status_produksi" required>
                <option value="pending" <?= ($pesanan['status_produksi'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="processing" <?= ($pesanan['status_produksi'] == 'processing') ? 'selected' : '' ?>>Processing</option>
                <option value="selesai" <?= ($pesanan['status_produksi'] == 'selesai') ? 'selected' : '' ?>>selesai</option>
                <option value="cancelled" <?= ($pesanan['status_produksi'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
            </select>
            </div>
            
            <div class="form-group">
                <label for="harga_satuan">Total Order:</label>
                <input type="number" id="harga_satuan" name="harga_satuan" value="<?= $pesanan['harga_satuan'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="dalam_proses">Dalam Progress:</label>
                <input type="number" id="dalam_proses" name="dalam_proses" value="<?= $pesanan['dalam_proses'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="selesai">selesai:</label>
                <input type="text" id="selesai" name="selesai" value="<?= $pesanan['selesai'] ?>" required>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Update</button>
                <a href="../dashboard_coba2.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
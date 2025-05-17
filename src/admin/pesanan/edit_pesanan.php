<?php
include '../../user/config.php';
session_start();
    $loggedIn = isset($_SESSION['nama']); 

// Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['nama'])) {
        header("Location: ../user/login.php");
        exit();
    }

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: ../dashboard_coba2.php");
    exit();
}

$id_pesanan = $_GET['id'];

// Ambil data pesanan berdasarkan ID
$result = $conn->query("SELECT * FROM pesanan_dekas WHERE id_pesanan = $id_pesanan");

if($result->num_rows == 0) {
    echo "<script>alert('Data pesanan tidak ditemukan'); window.location='../pesanan_crud.php';</script>";
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
        
        echo "<script>alert('Data pesanan berhasil diupdate'); window.location='../pesanan_crud.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data pesanan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Pesanan</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/dashboard.css">
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-pencil-square me-2"></i>Edit Data Pesanan
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_pelanggan" class="form-label">Pelanggan</label>
                                    <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                                        <?php while($row = $pelanggan_result->fetch_assoc()): ?>
                                            <option value="<?= $row['id_pelanggan'] ?>" <?= ($row['id_pelanggan'] == $pesanan['id_pelanggan']) ? 'selected' : '' ?>>
                                                <?= $row['nama'] ?> (<?= $row['no_hp'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pemesanan" class="form-label">Tanggal Pemesanan</label>
                                    <input type="date" class="form-control" id="tanggal_pemesanan" name="tanggal_pemesanan" value="<?= $pesanan['tanggal_pemesanan'] ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" value="<?= $pesanan['harga_satuan'] ?>" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="total_harga" class="form-label">Total Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="total_harga" name="total_harga" value="<?= $pesanan['total_harga'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status_produksi" class="form-label">Status Pemesanan</label>
                                <select class="form-select" id="status_produksi" name="status_produksi" required>
                                    <option value="pending" <?= ($pesanan['status_produksi'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= ($pesanan['status_produksi'] == 'processing') ? 'selected' : '' ?>>Processing</option>
                                    <option value="selesai" <?= ($pesanan['status_produksi'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                                    <option value="cancelled" <?= ($pesanan['status_produksi'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="total_order" class="form-label">Total Order</label>
                                    <input type="number" class="form-control" id="total_order" name="harga_satuan" value="<?= $pesanan['harga_satuan'] ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="dalam_proses" class="form-label">Dalam Progress</label>
                                    <input type="number" class="form-control" id="dalam_proses" name="dalam_proses" value="<?= $pesanan['dalam_proses'] ?>" required>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="selesai" class="form-label">Selesai</label>
                                    <input type="number" class="form-control" id="selesai" name="selesai" value="<?= $pesanan['selesai'] ?>" required>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <a href="../pesanan_crud.php" class="btn btn-secondary me-2">
                                    <i class="bi bi-x-circle me-1"></i>Batal
                                </a>
                                <button type="submit" name="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
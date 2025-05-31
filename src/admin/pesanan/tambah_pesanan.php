<?php
include '../../user/config.php';
session_start();
    $loggedIn = isset($_SESSION['nama']); 

// Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['nama'])) {
        header("Location: ../user/login.php");
        exit();
    }
    
$id = $_GET['id'] ?? null;

if (isset($_POST['submit'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $id_detail = $_POST['id_detail'];
    $tanggal_pemesanan = $_POST['tanggal_pemesanan'];
    $harga_satuan = $_POST['harga_satuan'];
    $total_harga = $_POST['total_harga'];
    $status_produksi = $_POST['status_produksi'];
    $dalam_proses = $_POST['dalam_proses'];
    $selesai = $_POST['selesai'];
    
    $stmt = $conn->prepare("SELECT * FROM pesanan_dekas WHERE id_detail = ?");
    $stmt->bind_param("i", $id_detail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        echo "<script>alert('Data pesanan sudah ada!'); window.location.href='../detail_pesanan.php';</script>";
    } else {
        $sql = "INSERT INTO pesanan_dekas 
                (id_pelanggan, id_detail, tanggal_pemesanan, harga_satuan, total_harga, status_produksi, dalam_proses, selesai) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssii", $id_pelanggan, $id_detail, $tanggal_pemesanan, $harga_satuan, $total_harga, $status_produksi, $dalam_proses, $selesai);
        
        if($stmt->execute()) {
            echo "<script>alert('Data pesanan berhasil ditambahkan'); window.location='../pesanan_crud.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data pesanan: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

$pelanggan_result = $conn->query("SELECT * FROM pelanggan_dekas ORDER BY nama");
$detail_result = $conn->query("
SELECT dp.*, pl.nama AS nama_pelanggan
FROM detail_pesanan dp
    JOIN pelanggan_dekas pl ON dp.id_pelanggan = pl.id_pelanggan
    ORDER BY dp.id_detail
    ");

// Fix untuk total pesanan - hanya ambil jika $id ada dan valid
$current_total_jersey = 0;
$nama_pelanggan = '';
if (!empty($id) && is_numeric($id)) {
    $detail_total = $conn->prepare("
        SELECT dp.total_jersey, pl.nama 
        FROM detail_pesanan dp 
        JOIN pelanggan_dekas pl ON dp.id_pelanggan = pl.id_pelanggan 
        WHERE dp.id_detail = ?
    ");
    // $detail_total = $conn->prepare("SELECT total_jersey FROM detail_pesanan WHERE id_detail = ?");
    $detail_total->bind_param("i", $id);
    $detail_total->execute();
    $result_total = $detail_total->get_result();
    if ($result_total->num_rows > 0) {
        $row_total = $result_total->fetch_assoc();
        $current_total_jersey = $row_total['total_jersey'] ?? 0;
        $nama_pelanggan = $row_total['nama'] ?? '';
    }
    $detail_total->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesanan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .form-floating > .form-select {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
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
                            <i class="bi bi-plus-circle me-2"></i>Tambah Pesanan Baru
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_pelanggan" class="form-label">Pelanggan</label>
                                    <select id="id_pelanggan" name="id_pelanggan" class="form-select" required>
                                        <option value="">-- Pilih Pelanggan --</option>
                                        <?php while($row = $pelanggan_result->fetch_assoc()): ?>
                                            <option value="<?= $row['id_pelanggan'] ?>" <?= (!empty($nama_pelanggan) && $row['nama'] == $nama_pelanggan) ? 'selected' : '' ?>>
                                                <?= $row['id_pelanggan'] ?> - <?= $row['nama'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Silahkan pilih pelanggan
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="id_detail" class="form-label">Detail Pesanan</label>
                                    <select id="id_detail" name="id_detail" class="form-select" required onchange="updateTotalPesanan()">
                                        <option value="">-- Pilih Pesanan --</option>
                                        <?php while($row = $detail_result->fetch_assoc()): ?>
                                            <option value="<?= $row['id_detail'] ?>" data-total="<?= $row['total_jersey'] ?? 0 ?>" <?= (!empty($id) && $row['id_detail'] == $id) ? 'selected' : '' ?>>
                                                <?= $row['id_detail'] ?> - <?= $row['nama_pelanggan'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Silahkan pilih detail pesanan
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pemesanan" class="form-label">Tanggal Pemesanan</label>
                                    <input type="date" id="tanggal_pemesanan" name="tanggal_pemesanan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    <div class="invalid-feedback">
                                        Harap isi tanggal pemesanan
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status_produksi" class="form-label">Status Produksi</label>
                                    <select id="status_produksi" name="status_produksi" class="form-select" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="pending">Pending</option>
                                        <option value="processing" selected>Processing</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Harap pilih status produksi
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="harga_satuan" class="form-label">Harga Satuan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="harga_satuan" name="harga_satuan" class="form-control" value="0" required min="0" oninput="hitungTotal()">
                                    </div>
                                    <div class="invalid-feedback">
                                        Harap isi harga satuan yang valid
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="total_harga" class="form-label">Total Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="total_harga" name="total_harga" class="form-control" value="0" required min="0">
                                    </div>
                                    <div class="invalid-feedback">
                                        Total harga harus lebih dari 0
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="dalam_proses" class="form-label">Jumlah Dalam Proses</label>
                                    <input type="number" id="dalam_proses" name="dalam_proses" class="form-control" value="0" required min="0" oninput="updateProgress()">
                                    <div class="invalid-feedback">
                                        Harap isi jumlah dalam proses (min: 0)
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="selesai" class="form-label">Jumlah Selesai</label>
                                    <input type="number" id="selesai" name="selesai" class="form-control" value="0" required min="0" oninput="updateProgress()">
                                    <div class="invalid-feedback">
                                        Harap isi jumlah selesai (min: 0)
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="total_pesanan" class="form-label">Total Pesanan</label>
                                    <input type="number" id="total_pesanan" class="form-control" value="<?= $current_total_jersey ?>" readonly>
                                    <small class="text-muted">Otomatis terisi saat memilih detail pesanan</small>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="col-12">
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div id="progress_bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                    </div>
                                </div>
                            </div> -->~

                            <div class="d-flex justify-content-end mt-4">
                                <a href="../pesanan_crud.php" class="btn btn-danger me-2">
                                    <i class="bi bi-x-circle me-1"></i>Batal
                                </a>
                                <button type="submit" name="submit" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Update total pesanan ketika memilih detail pesanan
        function updateTotalPesanan() {
            const selectDetail = document.getElementById('id_detail');
            const totalPesananInput = document.getElementById('total_pesanan');
            
            if (selectDetail.value) {
                const selectedOption = selectDetail.options[selectDetail.selectedIndex];
                const totalJersey = selectedOption.getAttribute('data-total') || 0;
                totalPesananInput.value = totalJersey;
            } else {
                totalPesananInput.value = 0;
            }
            
            updateProgress();
        }
        
        // Hitung total harga
        function hitungTotal() {
            const hargaSatuan = parseInt(document.getElementById('harga_satuan').value) || 0;
            const totalPesanan = parseInt(document.getElementById('total_pesanan').value) || 0;
            const totalHarga = hargaSatuan * totalPesanan;
            
            document.getElementById('total_harga').value = totalHarga;
        }
        
        // Update progress bar
        function updateProgress() {
            const totalPesanan = parseInt(document.getElementById('total_pesanan').value) || 0;
            const dalamProses = parseInt(document.getElementById('dalam_proses').value) || 0;
            const selesai = parseInt(document.getElementById('selesai').value) || 0;
            
            if (totalPesanan > 0) {
                const totalProgress = dalamProses + selesai;
                const progressPercentage = Math.min((totalProgress / totalPesanan) * 100, 100);
                
                const progressBar = document.getElementById('progress_bar');
                progressBar.style.width = progressPercentage + '%';
                progressBar.setAttribute('aria-valuenow', progressPercentage);
                progressBar.textContent = Math.round(progressPercentage) + '%';
                
                // Change color based on progress
                progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
                if (progressPercentage < 30) {
                    progressBar.classList.add('bg-danger');
                } else if (progressPercentage < 70) {
                    progressBar.classList.add('bg-warning');
                } else {
                    progressBar.classList.add('bg-success');
                }
            } else {
                const progressBar = document.getElementById('progress_bar');
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
                progressBar.textContent = '0%';
                progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotalPesanan();
            hitungTotal();
            updateProgress();
            
            // Add event listeners
            document.getElementById('harga_satuan').addEventListener('input', hitungTotal);
            document.getElementById('dalam_proses').addEventListener('input', updateProgress);
            document.getElementById('selesai').addEventListener('input', updateProgress);
        });
        
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>
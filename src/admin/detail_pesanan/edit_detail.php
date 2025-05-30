<?php
include '../../user/config.php';

session_start();
    $loggedIn = isset($_SESSION['nama']); 

// Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['nama'])) {
        header("Location: ../user/login.php");
        exit();
    }

$detail_result = $conn->query("SELECT * FROM pelanggan_dekas ORDER BY id_pelanggan");

if(isset($_POST['submit'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
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

    $stmt = $conn->prepare("SELECT * FROM detail_pesanan WHERE id_pelanggan = ?");
    $stmt->bind_param("i", $id_pelanggan);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo "<script>alert('Data pesanan sudah ada!'); window.location.href='../detail_pesanan.php';</script>";
    } else {
        $sql = "INSERT INTO detail_pesanan(
            id_pelanggan, jenis_jersey, bahan_jersey, paket_jersey, 
            nama_pemain, nomor_punggung, logo, ukuran, motif, total_jersey, kode_jersey
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssssis", $id_pelanggan, $jenis_jersey, $bahan_jersey, $paket_jersey,
            $nama_pemain, $nomor_punggung, $logo, $ukuran, $motif, $total_jersey, $kode_jersey);

        if($stmt->execute()) {
            echo "<script>alert('Data pesanan berhasil ditambahkan'); window.location='../detail_pesanan.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data pesanan: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Detail Pesanan</title>
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
        .form-control:focus, .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .btn-success {
            background-color: #1cc88a;
            border-color: #1cc88a;
        }
        .btn-success:hover {
            background-color: #17a673;
            border-color: #169b6b;
        }
        .btn-danger {
            background-color: #e74a3b;
            border-color: #e74a3b;
        }
        .btn-danger:hover {
            background-color: #be3c30;
            border-color: #be3c30;
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
                            <i class="bi bi-clipboard-plus me-2"></i>Tambah Detail Pesanan
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_pelanggan" class="form-label">Pesanan</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <select id="id_pelanggan" name="id_pelanggan" class="form-select" required>
                                            <option value="">-- Pilih Pelanggan --</option>
                                            <?php while($row = $detail_result->fetch_assoc()): ?>
                                                <option value="<?= $row['id_pelanggan'] ?>"><?= $row['id_pelanggan'] ?> (<?= $row['nama'] ?>)</option>
                                            <?php endwhile; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Silahkan pilih pelanggan
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="jenis_jersey" class="form-label">Jenis Jersey</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <select id="jenis_jersey" name="jenis_jersey" class="form-select" required>
                                            <option value="">-- Pilih Jenis Jersey --</option>
                                            <option value="Jersey Bola">Jersey Bola</option>
                                            <option value="Jersey Basket">Jersey Basket</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Silahkan pilih jenis jersey
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bahan_jersey" class="form-label">Bahan Jersey</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-layers"></i></span>
                                        <select id="bahan_jersey" name="bahan_jersey" class="form-select" required>
                                            <option value="">-- Pilih Bahan Jersey --</option>
                                            <option value="milano">Milano</option>
                                            <option value="Dryfit Puma">Dryfit Puma</option>
                                            <option value="Embos Batik">Embos Batik</option>
                                            <option value="Dryfit Lite">Dryfit Lite</option>
                                            <option value="Jacquard Camo">Jacquard Camo</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Silahkan pilih bahan jersey
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="paket_jersey" class="form-label">Paket Jersey</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-box"></i></span>
                                        <select id="paket_jersey" name="paket_jersey" class="form-select" required>
                                            <option value="">-- Pilih Paket Jersey --</option>
                                            <option value="Paket Basic">Paket Basic</option>
                                            <option value="Paket Front Print">Paket Frontprint</option>
                                            <option value="Paket Halfprint">Paket Halfprint</option>
                                            <option value="Paket Fullprint">Paket Fullprint</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Silahkan pilih paket jersey
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_pemain" class="form-label">Nama Pemain</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                        <textarea class="form-control" id="nama_pemain" name="nama_pemain" rows="3" required></textarea>
                                        <div class="invalid-feedback">
                                            Silahkan masukkan nama pemain
                                        </div>
                                    </div>
                                    <div class="form-text text-muted">
                                        Pisahkan tiap nama dengan baris baru
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="nomor_punggung" class="form-label">Nomor Punggung</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-123"></i></span>
                                        <textarea class="form-control" id="nomor_punggung" name="nomor_punggung" rows="3" required></textarea>
                                        <div class="invalid-feedback">
                                            Silahkan masukkan nomor punggung
                                        </div>
                                    </div>
                                    <div class="form-text text-muted">
                                        Pisahkan tiap nomor dengan baris baru
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-image"></i></span>
                                        <input type="file" class="form-control" id="logo" name="logo" required>
                                        <div class="invalid-feedback">
                                            Silahkan unggah logo
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="motif" class="form-label">Motif</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-palette"></i></span>
                                        <input type="file" class="form-control" id="motif" name="motif" required>
                                        <div class="invalid-feedback">
                                            Silahkan unggah motif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ukuran" class="form-label">Ukuran</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                        <textarea class="form-control" id="ukuran" name="ukuran" rows="3" required></textarea>
                                        <div class="invalid-feedback">
                                            Silahkan masukkan ukuran
                                        </div>
                                    </div>
                                    <div class="form-text text-muted">
                                        Contoh: S, M, L, XL
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="total_jersey" class="form-label">Total Jersey</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-calculator"></i></span>
                                                <input type="number" class="form-control" id="total_jersey" name="total_jersey" required>
                                                <div class="invalid-feedback">
                                                    Silahkan masukkan total jersey
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <label for="kode_jersey" class="form-label">Kode Jersey</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-upc"></i></span>
                                                <input type="text" class="form-control" id="kode_jersey" name="kode_jersey" required>
                                                <div class="invalid-feedback">
                                                    Silahkan masukkan kode jersey
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <a href="../detail_pesanan.php" class="btn btn-danger me-2">
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
</body>
</html>
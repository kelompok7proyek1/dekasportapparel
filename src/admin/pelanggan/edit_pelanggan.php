<?php include '../../user/config.php'; 

// Periksa apakah ada parameter id
if(!isset($_GET['id'])) {
    header("Location: ../dashboard_coba2.php");
    exit();
}

$id_pelanggan = $_GET['id'];

// Ambil data pelanggan berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM pelanggan_dekas WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    echo "<script>alert('Data pelanggan tidak ditemukan'); window.location='../pelanggan_crud.php';</script>";
    exit();
}

$pelanggan = $result->fetch_assoc();

// Proses form jika ada submit
if(isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    
    $sql = "UPDATE pelanggan_dekas SET nama = ?, no_hp = ?, alamat = ? WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $no_hp, $alamat, $id_pelanggan);
    
    if($stmt->execute()) {
        echo "<script>alert('Data pelanggan berhasil diupdate'); window.location='../pelanggan_crud.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data pelanggan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan</title>
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
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-person-gear me-2"></i>Edit Data Pelanggan
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Pelanggan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?= $pelanggan['nama'] ?>" required>
                                    <div class="invalid-feedback">
                                        Harap isi nama pelanggan
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= $pelanggan['no_hp'] ?>" required pattern="[0-9]+" 
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <div class="invalid-feedback">
                                        Harap isi nomor HP dengan benar
                                    </div>
                                </div>
                                <div class="form-text text-muted">
                                    Contoh format: 081234567890
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= $pelanggan['alamat'] ?></textarea>
                                    <div class="invalid-feedback">
                                        Harap isi alamat pelanggan
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <a href="../pelanggan_crud.php" class="btn btn-danger me-2">
                                    <i class="bi bi-x-circle me-1"></i>Batal
                                </a>
                                <button type="submit" name="submit" class="btn btn-success">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
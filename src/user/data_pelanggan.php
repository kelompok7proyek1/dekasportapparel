<?php
session_start();
include 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_pelanggan'])) {
    // Belum login
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.html';</script>";
    exit;
}

$id_cek = $_SESSION['id_pelanggan'];

// Sudah login, cek apakah data pelanggan sudah ada
$stmt = $conn->prepare("SELECT * FROM pelanggan_dekas WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id_cek);
$stmt->execute();
$result = $stmt->get_result();

// Kalau sudah ada data pelanggan
if ($result->num_rows > 0) {
    header("Location: custom.php"); // langsung ke halaman custom
    exit;
}

// Proses form jika ada submit
if(isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    // $jumlah_pesanan = $_POST['jumlah_pesanan'];
    
    // Query untuk insert data
    $sql = "INSERT INTO pelanggan_dekas (id_pelanggan, nama, no_hp, alamat) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $id_cek, $nama, $no_hp, $alamat);
    
    if($stmt->execute()) {
        echo "<script>alert('Data pelanggan berhasil disimpan'); window.location='custom.php';</script>";
    } else {
        echo "<script>alert('Gagal mengisi data pelanggan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 90px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .page-title {
            margin-bottom: 30px;
            color: #343a40;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="page-title"></h2>
            
            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Pelanggan: <?php echo " ", $id_cek ?></label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                    <div class="invalid-feedback">
                        Nama tidak boleh kosong
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="no_hp" class="form-label">No HP:</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                    <div class="invalid-feedback">
                        Nomor HP tidak boleh kosong
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat:</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                    <div class="invalid-feedback">
                        Alamat tidak boleh kosong
                    </div>
                </div>
                
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="home.php" class="btn btn-secondary me-md-2">Batal</a>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
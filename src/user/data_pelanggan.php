<?php
session_start();
include 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_pelanggan'])) {
    // Belum login
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.html';</script>";
    exit;
}

// Sudah login, cek apakah data pelanggan sudah ada
$id_pelanggan = $_SESSION['id_pelanggan'];

$stmt = $conn->prepare("SELECT * FROM pelanggan_dekas WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

// Kalau sudah ada data pelanggan
if ($result->num_rows > 0) {
    header("Location: custom.php"); // langsung ke halaman custom
    exit;
} else {
    header("Location: data_pelanggan.php"); // suruh isi data pelanggan dulu
    exit;
}
?>

<?php
include 'config.php';

// Proses form jika ada submit
if(isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $jumlah_pesanan = $_POST['jumlah_pesanan'];
    
    // Jumlah pesanan awal adalah 0
    // $jumlah_pesanan = 0;
    
    // Query untuk insert data
    $sql = "INSERT INTO pelanggan_dekas (nama, no_hp, alamat, jumlah_pesanan) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $no_hp, $alamat, $jumlah_pesanan);
    
    if($stmt->execute()) {
        echo "<script>alert('Data pelanggan berhasil disimpan'); window.location='custom.php';</script>";
    } else {
        echo "<script>alert('Gagal mengisi data pelanggan: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pelanggan</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    <style>
        
    </style>
</head>
<header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">DekaSport<span>Apparel</span></a>
            </nav>
        </div>
    </header>


<body>

    
    
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
                <label for="alamat">Jumlah Pesanan:</label>
                <textarea id="jumlah_pesanan" name="jumlah_pesanan" required></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Simpan</button>
                <a href="index.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php
require 'config.php';
// Start session for user authentication
$loggedIn = isset($_SESSION['id_pelanggan']); 
session_start();

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: login.php");
    exit();
}


// Get user data
$id_pelanggan = $_SESSION['id_pelanggan'];
$query = "SELECT * FROM pelanggan_dekas WHERE id_pelanggan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $paket_jersey = $_POST['paket'] ?? '';
    $nama_pemain = $_POST['nama_pemain'] ?? '';
    $nomor_punggung = $_POST['nomor_punggung'] ?? '';
    $ukuran = $_POST['ukuran'] ?? '';
    $kode_jersey = $_POST['kode_jersey'] ?? '';
    $bahan_jersey = $_POST['bahan_jersey'] ?? '';
    $jenis_jersey = $_POST['jenis_jersey'] ?? 'Jerser Bola'; // Default value
    $total_jersey = 1; // Default value, can be changed if you add quantity field
    
    // Handle file uploads - Logo
    $logo_filename = "";
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo_filename = $kode_jersey . "_logo." . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo_destination = "uploads/" . $logo_filename;
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo_destination);
    }
    
    // Handle file uploads - Motif
    $motif_filename = "";
    if(isset($_FILES['motif']) && $_FILES['motif']['error'] == 0) {
        $motif_filename = $kode_jersey . "_motif." . pathinfo($_FILES['motif']['name'], PATHINFO_EXTENSION);
        $motif_destination = "uploads/" . $motif_filename;
        move_uploaded_file($_FILES['motif']['tmp_name'], $motif_destination);
    }
    
    // Set price based on package
    $harga_satuan = 0;
    switch ($paket_jersey) {
        case 'Paket Basic':
            $harga_satuan = 110000;
            break;
        case 'Paket Front Print':
            $harga_satuan = 125000;
            break;
        case 'Paket Halfprint':
            $harga_satuan = 135000;
            break;
        case 'Paket Fullprint':
            $harga_satuan = 145000;
            break;
        default:
            $harga_satuan = 110000;
    }
    
    $total_harga = $harga_satuan * $total_jersey;
    
    // Insert into detail_pesanan table
    $query_detail = "INSERT INTO detail_pesanan (id_pelanggan, jenis_jersey, bahan_jersey, paket_jersey, nama_pemain, nomor_punggung, logo, ukuran, motif, total_jersey, kode_jersey) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_detail = $conn->prepare($query_detail);
    $stmt_detail->bind_param("issssssssss", $id_pelanggan, $jenis_jersey, $bahan_jersey, $paket_jersey, $nama_pemain, $nomor_punggung, $logo_filename, $ukuran, $motif_filename, $total_jersey, $kode_jersey);
    
    if ($stmt_detail->execute()) {
        $id_detail = $stmt_detail->insert_id;
        
        // Insert into pesanan_dekas table
        $tanggal_pemesanan = date('Y-m-d');
        $status_produksi = 'pending';
        $dalam_proses = 1;
        $selesai = 0;
        
        $query_pesanan = "INSERT INTO pesanan_dekas (id_pelanggan, id_detail, tanggal_pemesanan, harga_satuan, total_harga, status_produksi, dalam_proses, selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_pesanan = $conn->prepare($query_pesanan);
        $harga_satuan_str = strval($harga_satuan);
        $total_harga_str = strval($total_harga);
        
        $stmt_pesanan->bind_param("iisssiii", $id_pelanggan, $id_detail, $tanggal_pemesanan, $harga_satuan_str, $total_harga_str, $status_produksi, $dalam_proses, $selesai);
        
        if ($stmt_pesanan->execute()) {
            // Order successfully placed
            $success_message = "Pesanan berhasil dibuat!";
        } else {
            // Order failed
            $error_message = "Gagal membuat pesanan: " . $stmt_pesanan->error;
            
            // Rollback by deleting the detail_pesanan entry
            $delete_detail = "DELETE FROM detail_pesanan WHERE id_detail = ?";
            $stmt_delete = $conn->prepare($delete_detail);
            $stmt_delete->bind_param("i", $id_detail);
            $stmt_delete->execute();
        }
    } else {
        // Error in inserting detail_pesanan
        $error_message = "Gagal menyimpan detail pesanan: " . $stmt_detail->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Jersey - Deka Sport Apparel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/custom.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
        <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">DekaSport<span>Apparel</span></a>
                <ul class="nav-links">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="data_pelanggan.php">Custom</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if($loggedIn) : ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                        <!-- <li><a href="logout.php">Logout</a></li> -->
                            <?php else : ?>
                                <li><a href="login.html" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                                <!-- <li><a href="login.html">login</a></li>
                                <li><a href="registrasi.html">Register</a></li>  -->
                    <?php endif; ?>
                </ul>
                <!-- Auth buttons (Login/Register) -->
            <div class="auth-buttons">
                <?php if($loggedIn) : ?>
                    <a href="profile.php?id=<?= $_SESSION['id_pelanggan'] ?>" class="login-btn">My Account</a>
                    <a href="logout.php" class="register-btn">Logout</a>
                <?php else : ?>
                    <a href="login.html" class="login-btn">Login</a>
                    <a href="register.html" class="register-btn">Register</a>
                <?php endif; ?>
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </a>
            </div>
            
            <div class="menu-toggle" id="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            </nav>
        </div>
    </header>

    
    <!-- Product Showcase -->
    <img src ="../../image/custom.png" alt="Banner" style="width: 100%; height: auto;">
    <!-- Order Form -->
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
                                            <option value="Jerser Bola">Jersey Bola</option>
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
                                        Contoh: S=5, M=10, L=3, XL=2
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

    <!-- Footer -->
    <footer>
        <div class="container">
                <div class="footer-content">
                    <section class="footer-column">
                    <div class="footer-column1">
                        <h3>Deka<span> Sport Apparel</span></h3>
                        <p>Pakaian  custom premium dari Deka Sport Apparel <br> untuk tim dan individu. berkualitas, desain luar <br> biasa, dan layanan terbaik.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    </section>
                    <section class="footer-column">
                        <div class="footer-column4">
                            <h3>Contact Us</h3>
                            <ul class="footer-links">
                                <li><i class="fas fa-phone"></i> 0818-0469-8724</li>
                                <li><i class="fas fa-envelope"></i> dekasport4@gmail.com</li>
                                <li><i class="fas fa-map-marker-alt"></i> Jl. DI Panjaitan No. 90 Indramayu</li>
                            </ul>
                    </div>
                </section>
                <section class="footer-column">
                <div class="footer-column3">
                    <h3>Location</h3>
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3162.920543176329!2d-122.08424968469354!3d37.42206597982502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb5dd1e1bb9ab%3A0x724f7426e861d5a4!2sGoogleplex!5e0!3m2!1sen!2sus!4v1615195204701!5m2!1sen!2sus" 
                            referrerpolicy="no-referrer-when-downgrade"
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy"
                            >
                        </iframe>
                </div>
                </section>
                </div>

            <div class="footer-bottom">
                <p>&copy; 2025 Deka Sport Apparel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
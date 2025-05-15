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
    <link rel="stylesheet" >
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
    <section class="order-form">
        <div class="container">
            <h2 class="form-title">Form Detail Pesanan</h2>
            
            <?php if(isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="paket">Pilih Paket :</label>
                    <select id="paket" name="paket" class="form-control" required>
                        <option value="">Pilih paket jersey...</option>
                        <option value="Paket Basic">Paket Basic - Rp. 110.000/set</option>
                        <option value="Paket Front Print">Paket Front Print - Rp. 125.000/set</option>
                        <option value="Paket Halfprint">Paket Halfprint - Rp. 135.000/set</option>
                        <option value="Paket Fullprint">Paket Fullprint - Rp. 145.000/set</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="jenis_jersey">Jenis Jersey :</label>
                    <select id="jenis_jersey" name="jenis_jersey" class="form-control" required>
                        <option value="Jerser Bola">Jersey Bola</option>
                        <option value="Jersey Basket">Jersey Basket</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nama_pemain">Nama Pemain :</label>
                    <input type="text" id="nama_pemain" name="nama_pemain" class="form-control" placeholder="Masukkan semua nama team anda disini..." required>
                </div>
                
                <div class="form-group">
                    <label for="nomor_punggung">Nomor Punggung :</label>
                    <input type="text" id="nomor_punggung" name="nomor_punggung" class="form-control" placeholder="Masukkan semua nomor punggung team anda disini..." required>
                </div>
                
                <div class="form-group">
                    <label for="logo">Logo :</label>
                    <div class="file-upload">
                        <label for="logo" class="file-upload-label">Pilih File</label>
                        <div class="file-name" id="logo-file-name">Tidak ada file yang dipilih</div>
                        <input type="file" id="logo" name="logo" style="display: none;" accept="image/*" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="ukuran">Ukuran :</label>
                    <input type="text" id="ukuran" name="ukuran" class="form-control" placeholder="Masukkan semua ukuran jersey team anda disini..." required>
                </div>
                
                <div class="form-group">
                    <label for="motif">Motif :</label>
                    <div class="file-upload">
                        <label for="motif" class="file-upload-label">Pilih File</label>
                        <div class="file-name" id="motif-file-name">Tidak ada file yang dipilih</div>
                        <input type="file" id="motif" name="motif" style="display: none;" accept="image/*" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="kode_jersey">Kode Jersey :</label>
                    <input type="text" id="kode_jersey" name="kode_jersey" class="form-control" placeholder="Masukkan kode jersey jika memilih motif dari google drive..." required>
                </div>
                
                <div class="form-group">
                    <label for="bahan_jersey">Bahan Jersey :</label>
                    <input type="text" id="bahan_jersey" name="bahan_jersey" class="form-control" placeholder="Masukkan kode jersey jika memilih motif dari google drive..." required>
                </div>
                
                <button type="submit" class="btn-submit">KIRIM</button>
            </form>
        </div>
    </section>
    
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
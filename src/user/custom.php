<?php
require 'config.php';
// Start session for user authentication
session_start();
$loggedIn = isset($_SESSION['id_pelanggan']); 

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: login.php");
    exit();
}


// Get user data
$id_pelanggan = $_SESSION['id_pelanggan'];
$query = "SELECT * FROM pelanggan_dekas WHERE id_pelanggan = ?"; //untuk menampilkan data pelanggan di form 
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $paket_jersey = $_POST['paket_jersey'] ?? '';
    $nama_pemain = $_POST['nama_pemain'] ?? '';
    $nomor_punggung = $_POST['nomor_punggung'] ?? '';
    $ukuran = $_POST['ukuran'] ?? '';
    $kode_jersey = $_POST['kode_jersey'] ?? '';
    $bahan_jersey = $_POST['bahan_jersey'] ?? '';
    $jenis_jersey = $_POST['jenis_jersey'] ?? 'Jerser Bola'; // Default value
    $total_jersey = 0; // Default value, can be changed if you add quantity field

    $nama_arr = array_filter(array_map('trim', explode("\n", $nama_pemain)));
    $nomor_arr = array_filter(array_map('trim', explode("\n", $nomor_punggung)));
    $ukuran_arr = array_filter(array_map('trim', explode("\n", $ukuran)));

    if (count($nama_arr) !== count($nomor_arr) || count($nama_arr) !== count($ukuran_arr)) {
        $error_message = "Jumlah nama, nomor punggung, dan ukuran harus sama.";
    }
    $total_jersey = count($nama_arr);
    
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
    <!-- <link rel="stylesheet" href="../../css/home.css"> -->
    <link rel="stylesheet" href="../../css/custom.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container-atas {
        max-width: 95%;
        margin: 0 auto;
        padding: 5px;
        text-align: center;
        background-color: #f9f9f9;
        border-radius: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .product-showcase h2 {
            font-size: 2.5em;
            color: #2c3e50;
            /* margin-bottom: 15px; */
        }

        .product-showcase p {
            font-size: 1.1em;
            color: #555;
            /* line-height: 1.6; */
        }
        /* Tambahan CSS untuk layout form dan gambar berdampingan */
        .jersey-image-container {
            padding: 15px;
            transition: transform 0.3s ease;
        }

        .jersey-image-container:hover {
            transform: scale(1.02);
        }

        .jersey-image-container img {
            border: 5px solid #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        @media (max-width: 991px) {
            /* Responsif untuk layar kecil */
            .jersey-image-container {
                margin-top: 30px;
                margin-bottom: 20px;
            }
        }

        /* Mempercantik form dan card */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .form-control, .form-select {
            border-radius: 8px;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 15px;
        }

        /* Container khusus untuk judul */
        .container-atas {
            max-width: 100%;
            margin: 0 auto 20px;
            padding: 15px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .product-showcase h2 {
            font-size: 2.2em;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .product-showcase p {
            font-size: 1.1em;
            color: #555;
            max-width: 800px;
            margin: 0 auto;
        }


    </style>
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
                                <li><a href="login.php" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                                <!-- <li><a href="login.php">login</a></li>
                                <li><a href="registrasi.html">Register</a></li>  -->
                    <?php endif; ?>
                </ul>
                <!-- Auth buttons (Login/Register) -->
            <div class="auth-buttons">
                <?php if($loggedIn) : ?>
                    <a href="profile.php?id=<?= $_SESSION['id_pelanggan'] ?>" class="login-btn">My Account</a>
                    <a href="logout.php" class="register-btn">Logout</a>
                <?php else : ?>
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
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
<!-- Product Showcase -->
<div class="container-atas">
    <div class="product-showcase">
        <h2>Jersey Custom Premium</h2>
        <p>
            Tampilkan identitas tim atau gaya pribadi Anda dengan jersey custom dari <strong>Deka Sport Apparel</strong>.
            Desain eksklusif, bahan berkualitas tinggi, dan layanan profesional untuk hasil terbaik!
        </p>
    </div>
</div>

<!-- Order Form dengan Layout Baru -->
<div class="bg-light">
    <div class="container py-5">
        <div class="row">
            <!-- Form di sebelah kiri -->
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-clipboard-plus me-2"></i>silahkan isi form pesanan
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_pelanggan" class="form-label">Nama Pelanggan</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <select id="id_pelanggan" name="id_pelanggan" class="form-select" required>
                                            <option value="<?= $user_data['id_pelanggan'] ?>"><?= $user_data['nama'] ?></option>
                                        </select>
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
                                        <h5>Tinggi, Lebar, Tangan</h5>
                                        <h6>Pisahkan tiap ukuran dengan baris baru..</h6>
                                        S = 69  x 48 x 21, M = 72 x 50 x 22, L = 74 x 53 x 23, XL = 77 x 56 x 25, XXL = 79 x 58 x 27, XXXL = 81 x 60 x 29.
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
                                    <i class="bi bi-save me-1"></i>Buat jersey
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Gambar di sebelah kanan -->
            <div class="col-lg-5 d-flex align-items-center justify-content-center">
                <div class="jersey-image-container">
                    <img src="../../image/custom.png" alt="Custom Jersey" class="img-fluid rounded shadow-lg" style="max-height: 600px;">
                    <div class="mt-3 text-center">
                        <h4 class="text-primary">Jersey Custom Premium</h4>
                        <p class="text-muted">Desain eksklusif untuk tim Anda</p>
                    </div>
                    <div class="mt-3 p-3 border rounded bg-light">
                                            <a href="https://drive.google.com/drive/folders/1t8OPDnRuWHXHJuwyFc3_Wl7EWsKJSLV7?usp=sharing&fbclid=PAZXh0bgNhZW0CMTEAAaaNvFhvyyis7_oJrz7MFlFbstO76ADeTFD5uFZwnpZEbOfdSMCg8M2pmZM_aem_FAjyaWkgb35JmVHDlccwGA"
                                            class="btn btn-outline-primary btn-sm me-2 mb-2" target="_blank">
                                                📂 Lihat Pilihan Kode Jersey
                                            </a>
                                            <a href="https://wa.me/6281234567890" class="btn btn-success btn-sm mb-2" target="_blank">
                                                💬 Konsultasi via WhatsApp
                                            </a>
                                        </div>
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
                            <a href=""><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com/dekasportapparel/"><i class="fab fa-instagram"></i></a>
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
<?php
require 'config.php';
// Start session for user authentication
session_start();
$loggedIn = isset($_SESSION['id_pelanggan']); 

// Check if user is logged in, otherwise redirect to login page with a message
if (!isset($_SESSION['id_pelanggan'])) {
    $_SESSION['redirect_message'] = "Silakan login terlebih dahulu untuk melakukan custom jersey.";
    header("Location: login.php");
    exit();
}

// Initialize variables
$success_message = '';
$error_message = '';

// Get user data
$id_pelanggan = $_SESSION['id_pelanggan'];
$query = "SELECT * FROM pelanggan_dekas WHERE id_pelanggan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Function to validate input
function validate_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // try {
        // Get and validate form data
        $paket_jersey = validate_input($_POST['paket_jersey'] ?? '');
        $nama_pemain = validate_input($_POST['nama_pemain'] ?? '');
        $nomor_punggung = validate_input($_POST['nomor_punggung'] ?? '');
        $ukuran = validate_input($_POST['ukuran'] ?? '');
        $kode_jersey = validate_input($_POST['kode_jersey'] ?? '');
        $bahan_jersey = validate_input($_POST['bahan_jersey'] ?? '');
        $jenis_jersey = validate_input($_POST['jenis_jersey'] ?? 'Jersey Bola'); // Default value
        
        // Validate required fields
        if (empty($paket_jersey) || empty($bahan_jersey) || empty($jenis_jersey) || 
            empty($nama_pemain) || empty($nomor_punggung) || empty($ukuran)) {
            throw new Exception("Semua field harus diisi.");
        }

        // Split and validate player details
        $nama_arr = array_filter(array_map('trim', explode("\n", $nama_pemain)));
        $nomor_arr = array_filter(array_map('trim', explode("\n", $nomor_punggung)));
        $ukuran_arr = array_filter(array_map('trim', explode("\n", $ukuran)));

        if (count($nama_arr) !== count($nomor_arr) || count($nama_arr) !== count($ukuran_arr)) {
            throw new Exception("Jumlah nama, nomor punggung, dan ukuran harus sama.");
        }
        
        if (count($nama_arr) < 1) {
            throw new Exception("Minimal satu set data pemain diperlukan.");
        }
        
        $total_jersey = count($nama_arr);
        
        // Handle file uploads - Logo
        $logo_filename = "";
        if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            // Validate file type
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'svg');
            $fileName = $_FILES['logo']['name'];
            $fileTmp = $_FILES['logo']['tmp_name'];
            $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_extension, $allowed_types)) {
                throw new Exception("Format file logo tidak valid. Format yang diperbolehkan: JPG, JPEG, PNG, GIF, SVG.");
            }
            
            // Generate unique filename
            $target = "uploads/";
            $logo_filename = basename($_FILES['logo']['name']);
            $ekstensi = pathinfo($logo_filename, PATHINFO_EXTENSION);
            $namaUnik = "id_" . $id_pelanggan . "_kode_". $kode_jersey . "_logo_" . time() . "." . $ekstensi;
            $logo_destination = $target . $namaUnik;
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logo_destination)) {
                throw new Exception("Gagal mengunggah file logo.");

            }
        } else {
            throw new Exception("File logo harus diunggah.");
        }
        
        // Handle file uploads - Motif
        $motif_filename = "";
        if(isset($_FILES['motif']) && $_FILES['motif']['error'] == 0) {
            // Validate file type
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'svg');
            $file_extension = strtolower(pathinfo($_FILES['motif']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_extension, $allowed_types)) {
                throw new Exception("Format file motif tidak valid. Format yang diperbolehkan: JPG, JPEG, PNG, GIF, SVG.");
            }
            
            // Generate unique filename
            $target = "uploads/";
            $logo_filename = basename($_FILES['motif']['name']);
            $ekstensi = pathinfo($logo_filename, PATHINFO_EXTENSION);
            $namaUnik = "id_" . $id_pelanggan . "_kode_". $kode_jersey . "_motif_" . time() . "." . $ekstensi;
            $motif_destination = $target . $namaUnik;
        
            // Move uploaded file
            if (!move_uploaded_file($_FILES['motif']['tmp_name'], $motif_destination)) {
                throw new Exception("Gagal mengunggah file logo.");
            }
            
        } else {
            throw new Exception("File motif harus diunggah.");
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
        
        // Begin transaction
        $conn->begin_transaction();
        
        // Insert into detail_pesanan table
        $query_detail = "INSERT INTO detail_pesanan (id_pelanggan, jenis_jersey, bahan_jersey, paket_jersey, nama_pemain, nomor_punggung, logo, ukuran, motif, total_jersey, kode_jersey) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_detail = $conn->prepare($query_detail);
        $stmt_detail->bind_param("issssssssss", $id_pelanggan, $jenis_jersey, $bahan_jersey, $paket_jersey, $nama_pemain, $nomor_punggung, $logo_filename, $ukuran, $motif_filename, $total_jersey, $kode_jersey);
        
        if (!$stmt_detail->execute()) {
            throw new Exception("Gagal menyimpan detail pesanan: " . $stmt_detail->error);
        }
        
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
        
        if (!$stmt_pesanan->execute()) {
            throw new Exception("Gagal membuat pesanan: " . $stmt_pesanan->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        // Set success message and redirect
        $success_message = "Pesanan berhasil dibuat! Silakan cek status pesanan di Dashboard Anda.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Jersey - Deka Sport Apparel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/custom.css">
    <script rel="stylesheet" href="../../js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .menu-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 6px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.menu-toggle:hover {
    background-color: rgba(0,0,0,0.05);
}

.menu-toggle span {
    width: 20px;
    height: 2px;
    background-color: #333;
    margin: 2px 0;
    transition: all 0.3s ease;
    border-radius: 1px;
}

/* Hamburger Animation */
.menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(4px, 4px);
}

.menu-toggle.active span:nth-child(2) {
    opacity: 0;
    transform: translateX(-10px);
}

.menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(4px, -4px);
}

/* Mobile Responsive - Compact Design */
@media (max-width: 768px) {
    .menu-toggle {
        display: flex;
        order: 3;
    }
    
    .navbar {
        position: relative;
    }
    
    .nav-links {
        position: absolute;
        top: 100%;
        left: 300px;
        width: 150px; /* Ukuran lebih kecil dari sebelumnya */
        background-color: white;
        flex-direction: column;
        padding: 10px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border-radius: 12px 0px 12px 12px; /* Rounded corner kanan */
        /* transform: translateY(20px); */
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        max-height: 0;
        overflow: hidden;
    }
    
    .nav-links.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
        max-height: 400px;
        padding: 20px 0;
    }
    
    .nav-links li {
        margin: 8px 0;
        text-align: left; /* Align kiri untuk tampilan compact */
        opacity: 0;
        transform: translateY(-10px);
        animation: slideInDown 0.4s ease forwards;
    }
    
    .nav-links.active li:nth-child(1) { animation-delay: 0.1s; }
    .nav-links.active li:nth-child(2) { animation-delay: 0.2s; }
    .nav-links.active li:nth-child(3) { animation-delay: 0.3s; }
    .nav-links.active li:nth-child(4) { animation-delay: 0.4s; }
    .nav-links.active li:nth-child(5) { animation-delay: 0.5s; }
    
    .nav-links a {
        display: block;
        padding: 10px 20px; /* Padding lebih kecil */
        margin: 0 10px;
        border-radius: 8px;
        transition: all 0.2s ease;
        font-weight: 500;
        text-align: left;
        font-size: 14px; /* Font size lebih kecil */
    }
    
    .nav-links a:hover {
        background-color: #f8f9fa;
        color: #007bff;
        transform: translateX(5px);
    }
    
    /* Auth buttons tetap di posisi asli */
    .auth-buttons {
        display: none; /* Hide pada mobile untuk tampilan yang lebih clean */
    }
}

/* Smooth slide animation */
@keyframes slideInDown {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Tablet responsive adjustments */
@media (max-width: 992px) and (min-width: 769px) {
    .nav-links {
        gap: 20px;
    }
}

/* Tampilkan auth buttons di ukuran yang lebih besar */
@media (min-width: 769px) {
    .auth-buttons {
        display: flex !important;
    }
}
    </style>
</head>
<body>
    <!-- Header -->
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
                    <a href="profile.php?id=<?= $_SESSION['id_pelanggan'] ?>" class="login-btn">Profile</a>
                    <a href="logout.php" class="register-btn">Logout</a>
                <?php else : ?>
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                <?php endif; ?>
            </div>
            
            <div class="menu-toggle" id="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            </nav>
        </div>
    </header>

    <!-- Success or Error Messages -->
    <?php if(!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show mx-auto my-3" style="max-width: 90%;" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if(!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show mx-auto my-3" style="max-width: 90%;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    

    <!-- Package Display -->
    <div class="container my-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h5 class="mb-0">Paket Basic</h5>
                        <h4 class="mb-0">Rp 110.000/pcs</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">✅ Tanpa Motif Printing</li>
 
                            <li class="list-group-item">✅ Logo Sponsor dan <br>Namaset Pakai DTP/Polyflex</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h5 class="mb-0">Paket Front Print</h5>
                        <h4 class="mb-0">Rp 125.000/pcs</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">✅ Bagian Depan Printing</li>
                            <li class="list-group-item">✅ Bagian Belakang Non Print</li>
                            <li class="list-group-item">✅ Namaset Pakai DTP/Polyflex</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    <div class="card-header bg-info text-white text-center py-3">
                        <h5 class="mb-0">Paket Halfprint</h5>
                        <h4 class="mb-0">Rp 135.000/pcs</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">✅ Baju Fullprint</li>
                            <li class="list-group-item">✅ Celana Non Print</li>
                            <li class="list-group-item">DTP/Polyflex</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    <div class="card-header bg-danger text-white text-center py-3">
                        <h5 class="mb-0">Paket Fullprint</h5>
                        <h4 class="mb-0">Rp 155.000/pcs</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">✅ Baju Fullprint</li>
                            <li class="list-group-item">✅ Celana Fullprint</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Form dengan Layout Baru -->
    <div class="bg-light">
        <div class="container py-5">
            <div class="row">
                <!-- Form di sebelah kiri -->
                <div class="col-lg-7">
                    <div class="card mb-4 shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-dark text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="bi bi-clipboard-plus me-2"></i>FORM PEMESANAN JERSEY CUSTOM
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="id_pelanggan" class="form-label">Nama Pelanggan</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($user_data['nama']) ?>" readonly>
                                            <input type="hidden" name="id_pelanggan" value="<?= $user_data['id_pelanggan'] ?>">
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
                                                <option value="Milano">Milano</option>
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
                                                <option value="Paket Basic">Paket Basic - Rp 110.000/pcs</option>
                                                <option value="Paket Front Print">Paket Frontprint - Rp 125.000/pcs</option>
                                                <option value="Paket Halfprint">Paket Halfprint - Rp 135.000/pcs</option>
                                                <option value="Paket Fullprint">Paket Fullprint - Rp 145.000/pcs</option>
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
                                            <textarea class="form-control" id="nama_pemain" name="nama_pemain" rows="3" placeholder="Contoh:&#10;John&#10;Sarah" required></textarea>
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
                                            <textarea class="form-control" id="nomor_punggung" name="nomor_punggung" rows="3" placeholder="Contoh:&#10;7&#10;10" required></textarea>
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
                                    <div class="col-md-12 mb-3">
                                        <label for="ukuran" class="form-label">Ukuran Jersey</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                            <textarea class="form-control" id="ukuran" name="ukuran" rows="3" placeholder="Contoh:&#10;M&#10;L" required></textarea>
                                            <div class="invalid-feedback">
                                                Silahkan masukkan ukuran
                                            </div>
                                        </div>
                                        <div class="form-text text-muted">
                                            <p class="mb-1">Panduan Ukuran (Tinggi x Lebar x Tangan):</p>
                                            <p class="mb-0">S = 69cm x 48cm x 21cm | M = 72cm x 50cm x 22cm | L = 74cm x 53cm x 23cm</p>
                                            <p class="mb-0">XL = 77cm x 56cm x 25cm | XXL = 79cm x 58cm x 27cm | XXXL = 81cm x 60cm x 29cm</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="logo" class="form-label">Logo Tim (JPG, PNG, SVG)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-image"></i></span>
                                            <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif,.svg" required>
                                            <div class="invalid-feedback">
                                                Silahkan unggah logo tim
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="motif" class="form-label">Motif Jersey (JPG, PNG, SVG)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-palette"></i></span>
                                            <input type="file" class="form-control" id="motif" name="motif" accept=".jpg,.jpeg,.png,.gif,.svg" required>
                                            <div class="invalid-feedback">
                                                Silahkan unggah motif jersey
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="kode_jersey" class="form-label">Kode Jersey (opsional)</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-upc"></i></span>
                                            <input type="text" class="form-control" id="kode_jersey" name="kode_jersey" placeholder="Contoh: DSA-01">
                                            <div class="invalid-feedback">
                                                Silahkan masukkan kode jersey
                                            </div>
                                        </div>
                                        <div class="form-text">
                                            <a href="https://drive.google.com/drive/folders/1t8OPDnRuWHXHJuwyFc3_Wl7EWsKJSLV7" target="_blank" class="text-decoration-none btn btn-link btn-sm text-white">
                                                <i class="bi bi-folder me-1"></i>Lihat Pilihan Kode Jersey disini!
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <strong>Pastikan semua data sudah benar!</strong> Setelah pesanan diproses, perubahan tidak dapat dilakukan.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="dashboard.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i>Kembali
                                    </a>
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i>Kirim Pesanan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Gambar dan info di sebelah kanan -->
                <div class="col-lg-5 d-flex flex-column">
                    <div class="card mb-4 shadow">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Jersey Custom</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="../../image/custom.png" alt="Custom Jersey" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                            </div>
                            <div class="mt-3">
                                <h5 class="text-primary mb-3">Fitur Jersey Custom Kami:</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Bahan premium berkualitas tinggi
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Desain custom sesuai permintaan
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Nama dan nomor punggung sesuai keinginan
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Logo tim berkualitas tinggi
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        Berbagai pilihan ukuran lengkap
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="mt-4">
                                <h5 class="text-primary mb-3">Proses Pemesanan:</h5>
                                <ol class="list-group">
                                    <li class="list-group-item d-flex align-items-center">
                                        <div>
                                            <strong>Isi Form</strong>
                                            <p class="mb-0 text-muted small">Lengkapi semua data pemesanan dengan teliti</p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <div>
                                            <strong>Konfirmasi Pesanan</strong>
                                            <p class="mb-0 text-muted small">Cek detail pesanan di dashboard Anda</p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <div>
                                            <strong>Lakukan konfirmasi Pembayaran</strong>
                                            <p class="mb-0 text-muted small">Konfirmasi lewat dashboard anda yang nanti akan diarahkan ke whatsapp</p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <div>
                                            <strong>Proses Produksi</strong>
                                            <p class="mb-0 text-muted small">Estimasi 7-14 hari kerja</p>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <i class="bi bi-question-circle-fill me-2"></i>
                                <strong>Butuh bantuan?</strong> Hubungi customer service kami di 
                                <a href="https://wa.me/6281234567890" class="alert-link">WhatsApp</a> atau 
                                <a href="mailto:info@dekasport.com" class="alert-link">email</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Pertanyaan Umum (FAQ)</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Berapa lama waktu pengerjaan jersey custom?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Proses produksi jersey custom membutuhkan waktu sekitar 7-14 hari kerja tergantung tingkat kesulitan desain dan jumlah pesanan. Kami akan selalu memberikan update progres pengerjaan melalui dashboard pelanggan.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Apakah ada minimal order untuk pemesanan jersey custom?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Untuk pemesanan jersey custom, minimal order adalah 1 pcs. Namun, untuk mendapatkan harga yang lebih baik, kami menyarankan pemesanan minimal 12 pcs.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Bagaimana cara melakukan pembayaran?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Pembayaran dapat dilakukan melalui transfer bank ke rekening resmi Deka Sport Apparel. Setelah melakukan pembayaran, Anda dapat mengupload bukti transfer melalui dashboard pelanggan untuk verifikasi.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Apakah saya bisa melihat contoh desain terlebih dahulu?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, setelah Anda melakukan pemesanan, tim desain kami akan membuat mockup desain berdasarkan kode jersey dan motif yang Anda pilih. Mockup ini akan dikirimkan melalui dashboard pelanggan untuk persetujuan sebelum masuk tahap produksi.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    Bisakah saya mengubah pesanan setelah konfirmasi?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Perubahan pesanan hanya dapat dilakukan sebelum status produksi berubah menjadi "Dalam Proses". Setelah status berubah, kami tidak dapat menerima perubahan apapun pada pesanan Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
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

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
(function () {
    'use strict';

    const forms = document.querySelectorAll('.needs-validation');

    function handleSubmit(event) {
        const form = event.target;

        const playerNames = document.getElementById('nama_pemain').value.split('\n').filter(name => name.trim() !== '');
        const playerNumbers = document.getElementById('nomor_punggung').value.split('\n').filter(num => num.trim() !== '');
        const playerSizes = document.getElementById('ukuran').value.split('\n').filter(size => size.trim() !== '');

        if (playerNames.length !== playerNumbers.length || playerNames.length !== playerSizes.length) {
            event.preventDefault();
            event.stopPropagation();
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Sesuai',
                text: 'Jumlah nama pemain, nomor punggung, dan ukuran harus sama!',
                confirmButtonColor: '#d33'
            });
            return false;
        }

        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        event.preventDefault(); // stop default submit, lanjut jika confirmed

        const paket = document.getElementById('paket_jersey').options[document.getElementById('paket_jersey').selectedIndex].text;
        const totalJersey = playerNames.length;
        let hargaSatuan = 0;

        if (paket.includes('Basic')) hargaSatuan = 110000;
        else if (paket.includes('Frontprint')) hargaSatuan = 125000;
        else if (paket.includes('Halfprint')) hargaSatuan = 135000;
        else if (paket.includes('Fullprint')) hargaSatuan = 145000;

        const totalHarga = hargaSatuan * totalJersey;

        Swal.fire({
            title: 'Konfirmasi Pesanan',
            html: `
                <div class="text-start">
                    <p><strong>Paket:</strong> ${paket}</p>
                    <p><strong>Jumlah Jersey:</strong> ${totalJersey} pcs</p>
                    <p><strong>Harga Satuan:</strong> Rp ${hargaSatuan.toLocaleString('id-ID')}</p>
                    <p><strong>Total Harga:</strong> Rp ${totalHarga.toLocaleString('id-ID')}</p>
                </div>
                <div class="alert alert-warning mt-3">
                    Dengan melanjutkan, Anda setuju dengan persyaratan dan ketentuan kami.
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan Pesanan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.removeEventListener('submit', handleSubmit);
                form.submit(); // kirim beneran sekarang
            }
        });

        form.classList.add('was-validated');
    }

    forms.forEach(function (form) {
        form.addEventListener('submit', handleSubmit);
    });
})();

</script>
<!-- JavaScript for Menu Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            // const authButtons = document.querySelector('.auth-buttons');
            const navbar = document.querySelector('.navbar');

            // Toggle menu when hamburger is clicked
            menuToggle.addEventListener('click', function() {
                // Toggle active class on menu toggle for animation
                menuToggle.classList.toggle('active');
                
                // Toggle active class on navbar to show/hide menu
                navbar.classList.toggle('active');
                
                // Toggle active class on nav links
                navLinks.classList.toggle('active');
                
                // Toggle active class on auth buttons
                // authButtons.classList.toggle('active');
            });

            // Close menu when clicking on nav links (for mobile)
            const navLinkItems = document.querySelectorAll('.nav-links a');
            navLinkItems.forEach(link => {
                link.addEventListener('click', function() {
                    // Remove active classes to close menu
                    menuToggle.classList.remove('active');
                    navbar.classList.remove('active');
                    navLinks.classList.remove('active');
                    // authButtons.classList.remove('active');
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInsideNav = navbar.contains(event.target);
                
                if (!isClickInsideNav && navbar.classList.contains('active')) {
                    menuToggle.classList.remove('active');
                    navbar.classList.remove('active');
                    navLinks.classList.remove('active');
                    // authButtons.classList.remove('active');
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    // Remove active classes when screen is large
                    menuToggle.classList.remove('active');
                    navbar.classList.remove('active');
                    navLinks.classList.remove('active');
                    // authButtons.classList.remove('active');
                }
            });
        });
    </script>

</body>
</html>
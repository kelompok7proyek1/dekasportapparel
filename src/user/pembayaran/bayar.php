<?php
session_start();
include '../config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil ID pesanan dari parameter URL
$id_pesanan = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_pesanan) {
    header("Location: ../dashboard.php");
    exit();
}

// Query untuk mengambil data pesanan dengan join ke tabel pelanggan_dekas
$query = "SELECT p.*, pd.nama, pd.no_hp 
          FROM pesanan_dekas p 
          JOIN pelanggan_dekas pd ON p.id_pelanggan = pd.id_pelanggan 
          WHERE p.id_pesanan = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: ../dashboard.php");
    exit();
}

$pesanan = $result->fetch_assoc();

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $tanggal_upload = $_POST['tanggal_upload'];
    $id_bayar = $_POST['id_bayar'];
    $bukti_foto = "";
    $id_pelanggan = $_SESSION['id_pelanggan'];
    
    // Debug: Tampilkan data yang diterima
    error_log("POST Data: " . print_r($_POST, true));
    error_log("FILES Data: " . print_r($_FILES, true));
    
    // Handle file upload
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $target_dir = "../uploads/bukti_pembayaran/";
        
        // Buat direktori jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION);
        $new_filename = "bukti_" . $id_pesanan . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validasi file
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $target_file)) {
                $bukti_foto = $new_filename;
                error_log("File uploaded successfully: " . $bukti_foto);
            } else {
                $error_message = "Gagal mengupload file.";
                error_log("File upload failed");
            }
        } else {
            $error_message = "Format file tidak diizinkan. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }
    } else {
        $error_message = "File tidak valid atau tidak ada file yang diupload.";
        if (isset($_FILES['bukti_pembayaran'])) {
            error_log("File error code: " . $_FILES['bukti_pembayaran']['error']);
        }
    }
    
    // Insert ke tabel pembayaran jika upload berhasil
    if (!isset($error_message) && $bukti_foto) {
        // Debug: Tampilkan data sebelum insert
        error_log("Attempting to insert: ID Bayar=$id_bayar, ID Pelanggan=$id_pelanggan, Bukti=$bukti_foto, Tanggal=$tanggal_upload");
        
        $insert_query = "INSERT INTO pembayaran (id_bayar, id_pelanggan, bukti_pembayaran, tanggal_upload) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        
        if ($insert_stmt) {
            $insert_stmt->bind_param("siss", $id_bayar, $id_pelanggan, $bukti_foto, $tanggal_upload);
            
            if ($insert_stmt->execute()) {
                // Update status pesanan
                $update_query = "UPDATE pesanan_dekas SET status_pembayaran = 'pending_verifikasi' WHERE id_pesanan = ? AND id_pelanggan = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ii", $id_pesanan, $_SESSION['id_pelanggan']);
                
                if ($update_stmt->execute()) {
                    $success_message = "Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.";
                    error_log("Payment and order status updated successfully");
                } else {
                    $error_message = "Bukti pembayaran berhasil diupload, tapi gagal update status pesanan.";
                    error_log("Failed to update order status: " . $conn->error);
                }
            } else {
                $error_message = "Gagal menyimpan data pembayaran. Error: " . $insert_stmt->error;
                error_log("Failed to insert payment: " . $insert_stmt->error);
            }
        } else {
            $error_message = "Gagal mempersiapkan query: " . $conn->error;
            error_log("Failed to prepare statement: " . $conn->error);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Pesanan #<?php echo $id_pesanan; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .payment-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
        }
        .form-section {
            padding: 30px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .btn-payment {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="payment-card">
                    <!-- Header Section -->
                    <div class="header-section text-center">
                        <h2><i class="fas fa-credit-card me-3"></i>Pembayaran Pesanan</h2>
                        <p class="mb-0">Pesanan #<?php echo $id_pesanan; ?></p>
                    </div>

                    <!-- Form Section -->
                    <div class="form-section">
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Info Pelanggan -->
                        <div class="info-card">
                            <h5 class="mb-3"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($pesanan['nama']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>No. HP:</strong> <?php echo htmlspecialchars($pesanan['no_hp']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Pesanan -->
                        <div class="info-card">
                            <h5 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Informasi Pesanan</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID Pesanan:</strong> #<?php echo $pesanan['id_pesanan']; ?></p>
                                    <p><strong>Tanggal:</strong> <?php echo date('d/m/Y', strtotime($pesanan['tanggal_pemesanan'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total:</strong> Rp <?php echo number_format($pesanan['total_harga'] ?? 0, 0, ',', '.'); ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-warning"><?php echo ucfirst($pesanan['status_pembayaran'] ?? 'pending'); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Upload Bukti Pembayaran -->
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="tanggal_upload" class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Tanggal Pembayaran
                                </label>
                                <input type="datetime-local" class="form-control" id="tanggal_upload" name="tanggal_upload" 
                                       value="<?php echo date('Y-m-d\TH:i'); ?>" required> 
                            </div>
                            
                            <div class="mb-4">
                                <label for="id_bayar" class="form-label">
                                    <i class="fas fa-hashtag me-2"></i>ID Pembayaran
                                </label>
                                <input type="text" class="form-control" id="id_bayar" name="id_bayar" 
                                       value="PAY<?php echo $id_pesanan . time(); ?>" readonly>
                                <small class="form-text text-muted">ID ini akan digunakan sebagai referensi pembayaran</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                                </label>
                                <div class="upload-area">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5>Pilih File Bukti Pembayaran</h5>
                                    <p class="text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF (Max: 5MB)</p>
                                    <input type="file" class="form-control" name="bukti_pembayaran" 
                                           accept=".jpg,.jpeg,.png,.gif" required>
                                </div>
                            </div>

                            <!-- Informasi Pembayaran -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Informasi Pembayaran</h6>
                                <p class="mb-2"><strong>Bank:</strong> BCA</p>
                                <p class="mb-2"><strong>No. Rekening:</strong> 111111111111</p>
                                <p class="mb-2"><strong>Atas Nama:</strong> Dekas Sport Apparel</p>
                                <p class="mb-0"><strong>Jumlah:</strong> Rp <?php echo number_format($pesanan['total_harga'] ?? 0, 0, ',', '.'); ?></p>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="submit" class="btn btn-payment btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Upload Bukti Pembayaran
                                </button>
                                <a href="../dashboard.php" class="btn btn-secondary btn-lg ms-3">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview file upload
        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const uploadArea = document.querySelector('.upload-area');
                uploadArea.innerHTML = `
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">File Terpilih</h5>
                    <p class="text-muted">${file.name}</p>
                    <small class="text-muted">Ukuran: ${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                    <input type="file" class="form-control d-none" name="bukti_pembayaran" 
                           accept=".jpg,.jpeg,.png,.gif" required>
                `;
                // Keep the file in the hidden input
                uploadArea.querySelector('input[type="file"]').files = e.target.files;
            }
        });
    </script>
</body>
</html>
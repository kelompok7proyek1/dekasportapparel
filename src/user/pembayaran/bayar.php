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
            background: linear-gradient(135deg, #2c2c2c 0%, #3c3c3c 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .payment-container {
            margin: 20px auto;
            max-width: 1200px;
        }
        
        .payment-header {
            background: rgba(44, 44, 44, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .payment-content {
            display: flex;
            gap: 30px;
            align-items: stretch;
        }
        
        .info-panel {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }
        
        .form-panel {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #dc3545;
            transition: transform 0.3s ease;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .info-card h5 {
            color: #dc3545;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .info-value {
            color: #333;
            font-weight: 500;
        }
        
        .bank-info {
            background: linear-gradient(135deg, #2c2c2c 0%, #3c3c3c 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .bank-info h5 {
            color: white;
            margin-bottom: 20px;
        }
        
        .upload-area {
            border: 3px dashed #dee2e6;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin-bottom: 25px;
        }
        
        .upload-area:hover {
            border-color: #dc3545;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe6e6 100%);
            transform: translateY(-2px);
        }
        
        .upload-area.file-selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        
        .btn-payment {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .btn-payment:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4);
            color: white;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #2c2c2c 0%, #3c3c3c 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(44, 44, 44, 0.4);
            color: white;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .status-badge {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .total-amount {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .payment-content {
                flex-direction: column;
            }
            
            .payment-header {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .info-panel,
            .form-panel {
                padding: 20px;
            }
            
            .payment-container {
                margin: 10px;
            }
        }
        
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .upload-area {
                padding: 25px 15px;
            }
            
            .btn-payment,
            .btn-back {
                padding: 12px 30px;
                font-size: 14px;
            }
            
            .payment-header h2 {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .payment-container {
                margin: 5px;
            }
            
            .info-panel,
            .form-panel {
                padding: 15px;
            }
            
            .info-card {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .bank-info {
                padding: 15px;
            }
            
            .total-amount {
                font-size: 20px;
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="payment-container">
            <!-- Header -->
            <div class="payment-header">
                <h2><i class="fas fa-credit-card me-3"></i>Pembayaran Pesanan</h2>
                <p class="mb-0 fs-5">Pesanan #<?php echo $id_pesanan; ?></p>
            </div>

            <!-- Alerts -->
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

            <!-- Main Content -->
            <div class="payment-content">
                <!-- Info Panel -->
                <div class="info-panel">
                    <!-- Info Pelanggan -->
                    <div class="info-card">
                        <h5><i class="fas fa-user me-2"></i>Informasi Pelanggan</h5>
                        <div class="info-row">
                            <span class="info-label">Nama:</span>
                            <span class="info-value"><?php echo htmlspecialchars($pesanan['nama']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. HP:</span>
                            <span class="info-value"><?php echo htmlspecialchars($pesanan['no_hp']); ?></span>
                        </div>
                    </div>

                    <!-- Info Pesanan -->
                    <div class="info-card">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Informasi Pesanan</h5>
                        <div class="info-row">
                            <span class="info-label">ID Pesanan:</span>
                            <span class="info-value">#<?php echo $pesanan['id_pesanan']; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal:</span>
                            <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($pesanan['tanggal_pemesanan'])); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="status-badge"><?php echo ucfirst($pesanan['status_pembayaran'] ?? 'pending'); ?></span>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="total-amount">
                        <i class="fas fa-money-bill-wave me-2"></i>
                        Total: Rp <?php echo number_format($pesanan['total_harga'] ?? 0, 0, ',', '.'); ?>
                    </div>

                    <!-- Bank Info -->
                    <div class="bank-info">
                        <h5><i class="fas fa-university me-2"></i>Informasi Rekening</h5>
                        <div class="info-row">
                            <span class="info-label">Bank:</span>
                            <span class="info-value">BCA</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Rekening:</span>
                            <span class="info-value">111111111111</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Atas Nama:</span>
                            <span class="info-value">Dekas Sport Apparel</span>
                        </div>
                    </div>
                </div>

                <!-- Form Panel -->
                <div class="form-panel">
                    <h4 class="mb-4"><i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran</h4>
                    
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
                                <i class="fas fa-camera me-2"></i>Bukti Pembayaran
                            </label>
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Pilih File Bukti Pembayaran</h5>
                                <p class="text-muted mb-3">Format: JPG, JPEG, PNG, GIF (Max: 5MB)</p>
                                <input type="file" class="form-control" name="bukti_pembayaran" id="fileInput"
                                       accept=".jpg,.jpeg,.png,.gif" required>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" name="submit" class="btn btn-payment">
                                <i class="fas fa-paper-plane me-2"></i>Upload Bukti Pembayaran
                            </button>
                            <a href="../dashboard.php" class="btn-back">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload preview
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const uploadArea = document.getElementById('uploadArea');
            
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                uploadArea.classList.add('file-selected');
                uploadArea.innerHTML = `
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">File Terpilih</h5>
                    <p class="text-dark fw-bold">${file.name}</p>
                    <small class="text-muted">Ukuran: ${fileSize} MB</small>
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="changeFile()">
                            <i class="fas fa-edit me-1"></i>Ganti File
                        </button>
                    </div>
                    <input type="file" class="d-none" name="bukti_pembayaran" id="hiddenFileInput"
                           accept=".jpg,.jpeg,.png,.gif" required>
                `;
                
                // Transfer file to hidden input
                const hiddenInput = uploadArea.querySelector('#hiddenFileInput');
                const dt = new DataTransfer();
                dt.items.add(file);
                hiddenInput.files = dt.files;
            }
        });
        
        function changeFile() {
            const uploadArea = document.getElementById('uploadArea');
            uploadArea.classList.remove('file-selected');
            uploadArea.innerHTML = `
                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                <h5>Pilih File Bukti Pembayaran</h5>
                <p class="text-muted mb-3">Format: JPG, JPEG, PNG, GIF (Max: 5MB)</p>
                <input type="file" class="form-control" name="bukti_pembayaran" id="fileInput"
                       accept=".jpg,.jpeg,.png,.gif" required>
            `;
            
            // Re-attach event listener
            document.getElementById('fileInput').addEventListener('change', arguments.callee.caller);
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.querySelector('input[name="bukti_pembayaran"]');
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Silakan pilih file bukti pembayaran terlebih dahulu.');
                return false;
            }
            
            // Check file size (5MB limit)
            if (fileInput.files[0].size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                return false;
            }
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 5000);
    </script>
</body>
</html>
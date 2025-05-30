<?php
include "../user/config.php";
    session_start();
    $loggedIn = isset($_SESSION['nama']); 

// Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['nama'])) {
        header("Location: ../user/login.php");
        exit();
    }

// Query data pelanggan dan pesanan berdasarkan struktur database aktual
$resultpelanggan = $conn->query("SELECT * FROM pelanggan_dekas");//Mengambil semua data dari tabel pelanggan_dekas.

$resultpesanan = $conn->query( //jalankan metode query() dari objek(->) $conn (yaitu koneksi database).
    "SELECT p.*, 
    pl.nama AS nama_pelanggan, 
    dp.total_jersey AS jumlah_jersey
        FROM pesanan_dekas p
        JOIN pelanggan_dekas pl ON p.id_pelanggan = pl.id_pelanggan 
        JOIN detail_pesanan dp ON p.id_detail = dp.id_detail");//menggabungkan tabel berdasarkan id_pesanan dan id_pelanggan.

// $resultdetail = $conn->query("SELECT * FROM detail_pesanan");

// Count data for statistics
$total_pelanggan = $resultpelanggan->num_rows;//menghitung jumlah baris
$total_pesanan = $resultpesanan->num_rows;//Menghitung berapa jumlah total pelanggan dan total pesanan 

// Hitung pesanan yang sedang dalam proses (in_progres > 0)
$in_progress_orders = $conn->query("SELECT COUNT(*) as dalam_proses FROM pesanan_dekas WHERE dalam_proses > 0")->fetch_assoc()['dalam_proses'] ?? 0; //menghitung jumlah pesanan yang sedang dalam proses.

// Hitung pesanan yang sudah selesai (selesai = total_order)
// $selesai_orders = $conn->query("SELECT COUNT(*) as selesai FROM pesanan_dekas WHERE selesai = total_order")->fetch_assoc()['selesai'] ?? 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --sidebar-bg: #313143;
            --sidebar-hover: #334155;
            --sidebar-text: #cbd5e1;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--sidebar-bg);
            padding: 1.5rem 1rem;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
        }
        
        .nav-link {
            color: var(--sidebar-text);
            border-radius: 5px;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }
        
        .nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Main content area */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg,rgba(49, 49, 67, 1)0%, rgba(49, 49, 67, 1)100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .icon {
            width: 48px;
            height: 48px;
            background-color: rgba(49, 49, 67, 1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .stat-card .icon i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .stat-card h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-card p {
            color: var(--secondary-color);
            margin-bottom: 0;
        }
        
        .data-section {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .data-section h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-weight: 600;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
        }
        
        .logout-btn {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .logout-btn:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }
        
        .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }
            
            .sidebar.show {
                width: 250px;
                padding: 1.5rem 1rem;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.sidebar-open {
                margin-left: 250px;
            }
            
            .toggle-sidebar {
                display: block;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 1100;
            }
        }
        
        @media (min-width: 992px) {
            .toggle-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar Toggle Button (visible on mobile) -->
<button class="btn btn-primary toggle-sidebar d-lg-none" type="button" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            <i class="fas fa-tshirt me-2"></i>
            Admin
        </a>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="dashboard_admin.php">
                <i class="fas fa-table"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="pelanggan_crud.php">
                <i class="fas fa-users"></i>
                Kelola Pelanggan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="detail_pesanan.php">
                <i class="fas fa-table"></i>
                Detail Pesanan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="pesanan_crud.php">
                <i class="fas fa-shopping-cart"></i>
                Kelola Pesanan
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link logout-btn" href="logout-admin.php">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">Selamat Datang di Dashboard Admin!</h1>
                <p class="mb-0">Kelola pelanggan dan pesanan Deka Sport Apparel dengan mudah.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-light" id="refreshData">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Data
                </button>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="stat-card bg-white">
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo $total_pelanggan; ?></h3>
                <p>Total Pelanggan</p>
                <a href="pelanggan_crud.php" class="btn btn-sm btn-outline-primary mt-3">Kelola Pelanggan</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-white">
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3><?php echo $total_pesanan; ?></h3>
                <p>Total Pesanan</p>
                <a href="pesanan_crud.php" class="btn btn-sm btn-outline-primary mt-3">Kelola Pesanan</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-white">
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?php echo $in_progress_orders; ?></h3>
                <p>Pesanan Dalam Proses</p>
                <a href="pesanan_crud.php" class="btn btn-sm btn-outline-primary mt-3">Lihat Pesanan</a>
            </div>
        </div>
    </div>
    
    <!-- Recent Customers Section -->
    <div class="data-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-users me-2"></i>Data Pelanggan</h4>
            <!-- <a href="pelanggan_crud.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pelanggan
            </a> -->
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <!-- <th>Jumlah Pesanan</th> -->
                        <!-- <th>tambah pesanan</th> -->
                        <!-- <th>lihat detail pesanan</th> -->
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset the pointer to the beginning of the result set
                    $resultpelanggan->data_seek(0);
                    
                    // Display up to 5 most recent customers
                    $count = 0;
                    while ($row = $resultpelanggan->fetch_assoc()) {
                        if ($count < 5) {
                            echo "<tr>";
                                echo "<td>" . $row['id_pelanggan'] . "</td>";
                                echo "<td>" . $row['nama'] . "</td>";
                                echo "<td>" . $row['no_hp'] . "</td>";
                                echo "<td>" . $row['alamat'] . "</td>";
                            // echo "<td>" . $row['jumlah_pesanan'] . "</td>";
                                // echo "<td class='action-buttons'>
                                //         <a href='pesanan/tambah_pesanan.php?id=" . $row['id_pelanggan'] . "' </i>+pesanan</a> 
                                //         </td>";        
                                // echo "<td class='action-buttons'>
                                //         <a href='detail_pesanan.php?id=" . $row['id_pelanggan'] . "' </i>Detail Pesanan</a> 
                                //         </td>";
                                echo "<td class='action-buttons'>
                                        <a href='pelanggan/edit_pelanggan.php?id=" . $row['id_pelanggan'] . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></a>
                                        <a href='pelanggan/hapus_pelanggan.php?id=" . $row['id_pelanggan'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\");'><i class='fas fa-trash'></i></a>
                                        </td>";
                            echo "</tr>";
                            $count++;
                        } else {
                            break;
                        }
                    }
                    
                    if ($count == 0) {
                        echo "<tr><td colspan='6' class='text-center'>Tidak ada data pelanggan</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if ($count >= 5): ?>
            <div class="text-center mt-3">
                <a href="pelanggan_crud.php" class="btn btn-outline-primary">Lihat Semua Pelanggan</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Recent Orders Section -->
    <div class="data-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-shopping-cart me-2"></i>Data Pesanan</h4>
            <!-- <a href="pesanan_crud.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pesanan
            </a> -->
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>ID Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Harga satuan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset the pointer to the beginning of the result set
                    $resultpesanan->data_seek(0);
                    
                    // Display up to 5 most recent orders
                    $count = 0;
                    while ($row = $resultpesanan->fetch_assoc()) {
                        if ($count < 5) {
                            // Determine status badge class
                            $statusClass = '';
                            switch ($row['status_produksi']) {
                                case 'selesai':
                                    $statusClass = 'badge bg-success';
                                    break;
                                case 'processed':
                                    $statusClass = 'badge bg-primary';
                                    break;
                                default:
                                    $statusClass = 'badge bg-warning text-dark';
                            }
                            
                            // Calculate progress percentage
                            $total = $row['jumlah_jersey'] > 0 ? $row['jumlah_jersey'] : 1; // Avoid division by zero
                            $selesai = $row['selesai'];
                            $progressPercent = ($selesai / $total) * 100;
                            
                            echo "<tr>";
                            echo "<td>" . $row['id_pesanan'] . "</td>";
                            echo "<td>" . $row['id_pelanggan'] . "</td>";
                            echo "<td>" . $row['tanggal_pemesanan'] . "</td>";
                            echo "<td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>";
                            echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                            echo "<td><span class='" . $statusClass . "'>" . $row['status_produksi'] . "</span></td>";
                            echo "<td>
                                    <div class='progress' style='height: 10px;'>
                                        <div class='progress-bar bg-success' role='progressbar' style='width: " . $progressPercent . "%;' 
                                            aria-valuenow='" . $progressPercent . "' aria-valuemin='0' aria-valuemax='100'></div>
                                    </div>
                                    <small>" . $row['selesai'] . " / " . $row['jumlah_jersey'] . "</small>
                                </td>";
                            echo "<td class='action-buttons'>
                                    <a href='pesanan/edit_pesanan.php?id=" . $row['id_pesanan'] . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></a>
                                    <a href='pesanan/hapus_pesanan.php?id=" . $row['id_pesanan'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\");'><i class='fas fa-trash'></i></a>
                                </td>";
                            echo "</tr>";
                            $count++;
                        } else {
                            break;
                        }
                    }
                    
                    if ($count == 0) {
                        echo "<tr><td colspan='7' class='text-center'>Tidak ada data pesanan</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php if ($count >= 5): ?>
            <div class="text-center mt-3">
                <a href="pesanan_crud.php" class="btn btn-outline-primary">Lihat Semua Pesanan</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Order Progress Summary -->
    <!-- <div class="data-section">
        <h2><i class="fas fa-chart-line me-2"></i>Ringkasan Progres Pesanan</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <div class="display-4 text-primary"><?php echo $total_pesanan; ?></div>
                    <div class="text-muted">Total Pesanan</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <div class="display-4 text-warning"><?php echo $in_progress_orders; ?></div>
                    <div class="text-muted">Sedang dalam_proses</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-3">
                    <div class="display-4 text-success"><?php echo $selesai_orders; ?></div>
                    <div class="text-muted">Pesanan Selesai</div>
                </div>
            </div>
        </div>
    </div> -->
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple refresh function
    document.getElementById('refreshData').addEventListener('click', function() {
        location.reload();
    });
    
    // Mobile sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('sidebar-open');
    });
</script>
</body>
</html>

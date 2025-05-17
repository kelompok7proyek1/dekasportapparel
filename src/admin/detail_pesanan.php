<?php
include "../user/config.php";

session_start();
    $loggedIn = isset($_SESSION['nama']); 

// Check if user is logged in, otherwise redirect to login page
    if (!isset($_SESSION['nama'])) {
        header("Location: ../user/login.php");
        exit();
    }

// Query data pesanan
// $resultpesanan = $conn->query("SELECT p.*, pl.nama as nama_pelanggan 
//                               FROM pesanan_dekas p 
//                               JOIN pelanggan_dekas pl ON p.id_pelanggan = pl.id_pelanggan");


// $resultpesanan = $conn->query(
//     "SELECT * FROM detail_pesanan"
// );

$resultpesanan = $conn->query("
    SELECT dp.*, pl.nama AS nama_pelanggan, pl.id_pelanggan
    FROM detail_pesanan dp
    JOIN pelanggan_dekas pl ON dp.id_pelanggan = pl.id_pelanggan
");

// Tambahkan pengecekan error  
if (!$resultpesanan) {
    die("Error dalam query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan</title>
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
        
        .page-header {
            background: linear-gradient(135deg,rgba(49, 49, 67, 1) 0%, rgba(49, 49, 67, 1) 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            <a class="nav-link" href="dashboard_coba2.php">
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
            <a class="nav-link active" href="detail_pesanan.php">
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
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2"><i class="fas fa-shopping-cart me-2"></i>Manajemen Detail Pesanan</h4>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="detail_pesanan/tambah_detail.php" class="btn btn-light">
                    <i class="fas fa-plus-circle me-2"></i>Tambah detail pesanan
                </a>
                <!-- <button class="btn btn-light ms-2" id="refreshData">
                    <i class="fas fa-sync-alt"></i>
                </button> -->
            </div>
        </div>
    </div>
    
    <!-- Pesanan Data Section -->
    <div class="data-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-list me-2"></i>Daftar Detail Pesanan</h2>
            <div class="d-flex">
                <div class="input-group me-2" style="width: 250px;">
                    <input type="text" class="form-control" placeholder="Cari pesanan..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <!-- <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#">Semua Status</a></li>
                        <li><a class="dropdown-item" href="#">Pending</a></li>
                        <li><a class="dropdown-item" href="#">Processed</a></li>
                        <li><a class="dropdown-item" href="#">selesai</a></li>
                        <li><a class="dropdown-item" href="#">Cancelled</a></li>
                    </ul>
                </div> -->
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Detail</th>
                        <th>Nama Pelanggan</th>
                        <th>Buat Pesanan</th>
                        <th>Jenis</th>
                        <th>Bahan</th>
                        <th>Paket</th>
                        <th>Nama Pemain</th>
                        <th>Nomor</th>
                        <th>Logo</th>
                        <th>Ukuran</th>
                        <th>Motif</th>
                        <th>Kode Jersey</th>
                        <th>Jumlah</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                            <tbody>
                        <?php if($resultpesanan && $resultpesanan->num_rows > 0): ?>
                            <?php while($row = $resultpesanan->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['id_detail'] ?></td>
                                    <!-- <td>#<?= $row['id_pelanggan'] ?></td> -->
                                    <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder bg-primary bg-opacity-10 rounded-circle text-primary me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium"><?= $row['nama_pelanggan'] ?></span>
                                            <div class="small text-secondary">ID: <?= $row['id_pelanggan'] ?></div>
                                        </div>
                                    </div>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="pesanan/tambah_pesanan.php?id=<?= $row['id_detail'] ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus-circle"></i>Pesanan
                                        </a>
                                    </td>
                                    <td><?= $row['jenis_jersey'] ?></td>
                                    <td><?= $row['bahan_jersey'] ?></td>
                                    <td><?= $row['paket_jersey'] ?></td>
                                    <td><?= $row['nama_pemain'] ?></td>
                                    <td><?= $row['nomor_punggung'] ?></td>
                                    <td><?= $row['logo'] ?></td>
                                    <td><?= $row['ukuran'] ?></td>
                                    <td><?= $row['motif'] ?></td>
                                    <td><?= $row['kode_jersey'] ?></td>
                                    <td><?= $row['total_jersey'] ?></td>
                                    <td class="text-center">
                                        <a href="detail_pesanan/edit_detail.php?id=<?= $row['id_detail'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="detail_pesanan/hapus_detail.php?id=<?= $row['id_detail'] ?>" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Yakin ingin menghapus pesanan #<?= $row['id_detail'] ?>?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-shopping-cart fa-3x text-secondary mb-3"></i>
                                        <h5>Tidak ada data pesanan</h5>
                                        <p class="text-muted">Belum ada pesanan yang dibuat</p>
                                        <a href="pesanan/tambah_pesanan.php" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus-circle me-2"></i>Tambah Pesanan Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
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
    
    // Simple search function
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if(text.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
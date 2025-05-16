<?php
include "../user/config.php";
session_start();

// Query data pelanggan
$resultpelanggan = $conn->query("SELECT * FROM pelanggan_dekas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Pelanggan</title>
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
            background: linear-gradient(135deg, rgba(49, 49, 67, 1) 0%, rgba(49, 49, 67, 1) 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
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
        
        .customer-avatar {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
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
            <a class="nav-link active" href="pelanggan_crud.php">
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
            <a class="nav-link logout-btn" href="logout.php">
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
                <h4 class="mb-2"><i class="fas fa-users me-2"></i>Manajemen Data Pelanggan</h4>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-light" id="refreshData">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Data
                </button>
            </div>
        </div>
    </div>
    
    <!-- Customers Data Section -->
    <div class="data-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Pelanggan</h2>
            <a href="pelanggan/tambah_pelanggan.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pelanggan
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                        <!-- <th>Jumlah Pesanan</th> -->
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($resultpelanggan && $resultpelanggan->num_rows > 0): ?>
                        <?php while($row = $resultpelanggan->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id_pelanggan'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="customer-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fw-medium"><?= $row['nama'] ?></span>
                                    </div>
                                </td>
                                <td><?= $row['no_hp'] ?></td>
                                <td><?= $row['alamat'] ?></td>

                                <!-- <td>
                                    <span class="badge bg-info"><?= $row['jumlah_pesanan'] ?> pesanan</span>
                                </td> -->

                                <td class="text-center action-buttons">
                                    <a href="pelanggan/edit_pelanggan.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="pelanggan/hapus_pelanggan.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus data pelanggan ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                <p class="text-muted">Tidak ada data pelanggan</p>
                                <a href="pelanggan/tambah_pelanggan.php" class="btn btn-sm btn-outline-primary">Tambah Pelanggan Baru</a>
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
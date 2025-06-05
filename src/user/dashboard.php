<?php
    session_start();
    $loggedIn = isset($_SESSION['id_pelanggan']); 
?>
<?php
    include 'config.php'; // koneksi ke database

        $sql = "SELECT 
            p.id_detail,
            p.id_pesanan,
            p.tanggal_pemesanan, 
            p.harga_satuan, 
            p.total_harga, 
            p.status_produksi, 
            p.dalam_proses, 
            p.selesai,
            p.status_pembayaran,
            dp.total_jersey,
            pd.nama AS nama_pelanggan
        FROM 
            pesanan_dekas p
        JOIN 
            detail_pesanan dp ON p.id_detail = dp.id_detail
        JOIN 
            pelanggan_dekas pd ON p.id_pelanggan = pd.id_pelanggan
        WHERE 
            p.id_pelanggan = ?";

        // -- WHERE p.id_detail = dp.id_detail AND p.id_pelanggan = ?";

    $stmt = $conn->prepare($sql);    
    $stmt->bind_param("i", $_SESSION['id_pelanggan']);
    $stmt->execute();    
    $resultpesanan = $stmt->get_result();

    $rows = [];

    if ($resultpesanan && $resultpesanan->num_rows > 0) {
        while ($r = $resultpesanan->fetch_assoc()) {
            $rows[] = $r;
        }

        $firstRow = $rows[0];
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>deka sport apparel | Premium Custom Jerseys</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    <script src="script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <style>
        .status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    display: inline-block;
        }

        /* Status-specific styles */
        /* .status-badge.processing, */
        .status-badge.processing {
            background-color: #0d6efd; /* Biru */
        }

        .status-badge.selesai {
            background-color: #198754; /* Hijau */
        }

        .status-badge.pending {
            background-color: #ffc107; /* Kuning */
            color: #000;
        }

        .status-badge.cancelled {
            background-color: #dc3545; /* Merah */
        }

        .status-badge.unknown {
            background-color: #6c757d; /* Abu-abu */
        }
        /* Wrapper untuk posisi dan align */
        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Container progress */
        .progress-bar-wrapper {
            flex-grow: 1;
            background-color: #e0e0e0;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }

        /* Fill-nya (warna yang bergerak) */
        .progress-bar-fill {
            height: 100%;
            transition: width 0.4s ease;
        }

        /* Progress warna berdasarkan status */
        .progress-bar-fill.selesai {
            background-color: #28a745;
        }

        .progress-bar-fill.processing {
            background-color: #007bff;
        }

        .progress-bar-fill.pending {
            background-color: #ffc107;
        }

        .progress-bar-fill.cancelled {
            background-color: #dc3545;
        }

        .progress-text {
            font-size: 0.9rem;
            font-weight: 500;
        }
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
                <?php else : ?>
                    <li><a href="login.php" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li> 
                <?php endif; ?>
            </ul>

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

<section class="dashboard-section" id="dashboard">
    <div class="container-table">
        <div class="dashboard-header">
            <div class="dashboard-actions">
                <h3>Berikut adalah riwayat pesanan anda [<?= $firstRow['nama_pelanggan'] ?? '—' ?>]</h3>
            </div>
            <!-- <div class="dashboard-title"><h3>Riwayat Pemesanan</h3></div> -->
        </div>


        <div class="table-responsive">
        <div class="container-table">
            <table border="1" class="orders-table">
                <thead>
                    <tr>
                        <th>Confirm Pemesanan</th>
                        <th>Tanggal Pemesanan</th>
                        <th>Harga satuan</th>
                        <th>Total harga</th>
                        <th>Progres</th>
                        <th>Status Pemesanan</th>
                        <th>Status Pembayaran</th>
                        <!-- <th>In Progress</th> -->
                        <!-- <th>selesai</th> -->
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($rows as $row): ?>
                    <?php
                    $total = $row['total_jersey'];
                    $selesai = $row['selesai'];
                    $progress = ($total > 0) ? ($selesai / $total) * 100 : 0;

                    // $statusColor = '';
                    // switch(strtolower($row['status_produksi'])) {
                    //     case 'procesed':
                    //     case 'processed':
                    //         $statusColor = 'primary';
                    //         $icon = 'fas fa-spinner fa-spin';
                    //         break;
                    //     case 'selesai':
                    //         $statusColor = 'success';
                    //         $icon = 'fas fa-check-circle';
                    //         break;
                    //     case 'pending':
                    //         $statusColor = 'warning';
                    //         $icon = 'fas fa-clock';
                    //         break;
                    //     case 'cancelled':
                    //         $statusColor = 'danger';
                    //         $icon = 'fas fa-times-circle';
                    //         break;
                    //     default:
                    //         $statusColor = 'secondary';
                    //         $icon = 'fas fa-question-circle';
                    // }
                    ?>
                    <tr>
                        <td data-label="confirm pemesanan">
                            <!-- <a href="https://wa.me/6282119513872?text=Halo%20saya%20ingin%20konfirmasi%20pesanan%20atas%20nama%20<?= $row['nama_pelanggan'] ?>" target="_blank"> -->
                                <a href="pembayaran/bayar.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-primary">
                                    <!-- <i class="fas fa-check"></i> Konfirmasi -->
                                <button class="btn btn-success">Confirm</button>
                            </a>
                        </td>
                        <td data-label="tanggal pemesanan"><?= date('d/m/Y', strtotime($row['tanggal_pemesanan'])) ?></td>
                        <td data-label="harga satuan">Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                        <td data-label="total harga">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <!-- <td data-label="progress" style="width: 18%">
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $statusColor ?>" role="progressbar" style="width: <?= $progress ?>%" 
                                    aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small fw-medium"><?= $selesai ?>/<?= $total ?></span>
                            </div>
                        </td> -->
                        <td data-label="progress" style="width: 18%">
                            <div class="progress-wrapper">
                                <div class="progress-bar-wrapper">
                                    <?php $status = strtolower($row['status_produksi']); ?>
                                    <div class="progress-bar-fill <?= $status ?>" style="width: <?= $progress ?>%;"></div>
                                </div>
                                <span class="progress-text"><?= $selesai ?>/<?= $total ?></span>
                            </div>
                        </td>
                        <td data-label="status pemesanan/produksi">
                            <?php $status = strtolower($row['status_produksi']); ?>
                                <span class="badge status-badge <?= $status ?>">
                                    <i class="fas <?= ($status === 'processing') ? 'fa-spinner fa-spin' : '' ?>"></i>
                                    <?= ucfirst($status) ?>
                                </span>
                        </td>
                         <td class="bold" data-label="status pemesanan/produksi"><?php echo $status = $row['status_pembayaran'] ?></td>
                        
                    </tr>
                <?php endforeach; ?>

                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">Belum ada data pemesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-content">
            <section class="footer-column">
                <div class="footer-column1">
                    <h3>Deka<span> Sport Apparel</span></h3>
                    <p>Pakaian custom premium dari Deka Sport Apparel untuk tim dan individu. Berkualitas, desain luar biasa, dan layanan terbaik.</p>
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
                        loading="lazy">
                    </iframe>
                </div>
            </section>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Deka Sport Apparel. All Rights Reserved.</p>
        </div>
    </div>
</footer>
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

<?php
    session_start();
    $loggedIn = isset($_SESSION['id_pelanggan']); 
?>
<?php
    include 'config.php'; // koneksi ke database

    $sql = "SELECT tanggal_pemesanan, total_harga, status_pemesanan, total_order, in_progres, completed FROM pesanan_dekas WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($sql);    
    $stmt->bind_param("i", $_SESSION['id_pelanggan']);
    $stmt->execute();    
    $result = $stmt->get_result();
    // $result = $conn->query($sql);
    // $row = $result ? $result->fetch_assoc() : null;
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
                    <li><a href="login.html" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                    <li><a href="login.html">Login</a></li>
                    <li><a href="register.html">Register</a></li> 
                <?php endif; ?>
            </ul>

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

<section class="dashboard-section" id="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <div class="dashboard-title"><h3>Riwayat Pemesanan</h3></div>
            <div class="dashboard-actions">
                <!-- <i class="fas fa-bell"></i> -->
                <!-- <i class="fas fa-user-circle" href="profile.php?id=<?= $_SESSION['id_pelanggan'] ?>"></i> -->
            </div>
        </div>

        <!-- <div class="dashboard-content">
            <div class="dashboard-cards">
                <div class="dashboard-card">
                    <h4>Total Orders</h4>
                    <p><?= $row["total_order"] ?? 0 ?></p>
                </div>
                <div class="dashboard-card">
                    <h4>In Progress</h4>
                    <p><?= $row["in_progres"] ?? 0 ?></p>
                </div>
                <div class="dashboard-card">
                    <h4>Completed</h4>
                    <p><?= $row["completed"] ?? 0 ?></p>
                </div>
            </div> -->
            <table border="1" class="orders-table">
                <thead>
                    <tr>
                        <th>Tanggal Pemesanan</th>
                        <th>Total Harga</th>
                        <th>Status Pemesanan</th>
                        <th>Total Order</th>
                        <th>In Progress</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result ? $result->fetch_assoc() : null): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_pemesanan'])) ?></td>
                        <td><span class="fw-medium">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></span></td>
                        <td>
                            <?php
                                $statusColor = '';
                                $icon = '';
                                switch(strtolower($row['status_pemesanan'])) {
                                    case 'processed':
                                        $statusColor = 'primary';
                                        $icon = 'fas fa-spinner fa-spin';
                                        break;
                                    case 'completed':
                                        $statusColor = 'success';
                                        $icon = 'fas fa-check-circle';
                                        break;
                                    case 'pending':
                                        $statusColor = 'warning';
                                        $icon = 'fas fa-clock';
                                        break;
                                    case 'cancelled':
                                        $statusColor = 'danger';
                                        $icon = 'fas fa-times-circle';
                                        break;
                                    default:
                                        $statusColor = 'secondary';
                                        $icon = 'fas fa-question-circle';
                                }
                            ?>
                            <span class="badge bg-<?= $statusColor ?>">
                                <i class="<?= $icon ?> me-1"></i> <?= ucfirst($row['status_pemesanan']) ?>
                            </span>
                        </td>
                        <td><?= $row["total_order"] ?></td>
                        <td><?= $row["in_progres"] ?></td>
                        <td><?= $row["completed"] ?></td>
                    </tr>
                <?php endwhile; ?>
                    <?php if($result->num_rows === 0) : ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">Belum ada data pemesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
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
</body>
</html>

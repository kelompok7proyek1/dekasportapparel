<?php
    session_start();
    $loggedIn = isset($_SESSION['email']);
?>
<?php
    include 'config.php'; // koneksi ke database

    $sql = "SELECT tanggal_pemesanan, total_harga, status_pemesanan, total_order, in_progres, completed FROM pesanan_dekas";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc() 
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="data_pelanggan.php" onclick="return confirm('Silakan isi data-data anda terlebih dahulu!')">Custom</a></li>
                    <?php if($loggedIn) : ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php">Logout</a></li>
                            <?php else : ?>
                                <li><a href="login.html" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                                <li><a href="login.html">login</a></li>
                                <li><a href="registrasi.html">Register</a></li> 
                    <?php endif; ?>
                </ul>
                <!-- <div class="menu-toggle" id="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div> -->
            </nav>
        </div>
    </header>

        <!-- Dashboard Section -->
        <section class="dashboard-section" id="dashboard">
            <div class="container">
                    <div class="dashboard-header">
                        <div class="dashboard-title">My Account</div>
                        <div class="dashboard-actions">
                            <i class="fas fa-bell"></i>
                            <i class="fas fa-user-circle"></i>
                        </div>
                    </div>
                    <!-- <div class="dashboard-nav">
                        <div class="dashboard-nav-item active">Orders</div>
                        <div class="dashboard-nav-item">Designs</div>
                        <div class="dashboard-nav-item">Settings</div>
                    </div> -->
                    <div class="dashboard-content">
                        <div class="dashboard-cards">
                            <div class="dashboard-card">
                                <h4>Total Orders</h4>
                                <p><?= $row["total_order"] ?></p>
                            </div>
                            <!-- <div class="dashboard-card">
                                <h4>Saved Designs</h4>
                                <p>5</p>
                            </div> -->
                            <div class="dashboard-card">
                                <h4>In Progress</h4>
                                <p><?= $row["in_progres"] ?></p>
                            </div>
                            <div class="dashboard-card">
                                <h4>Completed</h4>
                                <p><?= $row["completed"] ?></p>
                            </div>
                        </div>
                        <h3>Riwayat Pemesanan</h3>
                        <table border="1" class="orders-table">
                            <thead>
                                <tr>
                                    <th>Tanggal Pemesanan</th>
                                    <th>Total Harga</th>
                                    <th>Status Pemesanan</th>
                                    <th>Total Order</th>
                                    <th>In Progres</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <td><?= $row["tanggal_pemesanan"] ?></td>
                                <td><?= $row["total_harga"] ?></td>
                                <td><span class="status processing"><?= $row["status_pemesanan"] ?></span></td>
                                <td><?= $row["total_order"] ?></td>
                                <td><?= $row["in_progres"] ?></td>
                                <td><?= $row["completed"] ?></td>
                                <!-- <td><span class="status processing">Processing</span></td> -->
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <footer>
        <div class="container-footer">
                <div class="footer-content">
                    <section class="footer-column">
                    <div class="footer-column1">
                        <h3>Deka<span> Sport Apparel</span></h3>
                        <p>Pakaian  custom premium dari Deka Sport Apparel <br> untuk tim dan individu. berkualitas, desain luar <br> biasa, dan layanan terbaik.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    </section>
                    <section class="footer-column">
                    <div class="footer-column2">
                            <!-- <div class="footer-column2"> -->
                            <h3>Products</h3>
                            <ul class="footer-links">
                                <li><a href="#">Basketball Jerseys</a></li>
                                <li><a href="#">Football Jerseys</a></li>
                                <li><a href="#">Custom Designs</a></li>
                            </ul>
                            <!-- </div> -->
                    </div>
                    </section>
                    <section class="footer-column">
                    <div class="footer-column3">
                        <!-- maps -->
                        <!-- <div class="footer-column3"> -->
                        <h3>Location</h3>
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3162.920543176329!2d-122.08424968469354!3d37.42206597982502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fb5dd1e1bb9ab%3A0x724f7426e861d5a4!2sGoogleplex!5e0!3m2!1sen!2sus!4v1615195204701!5m2!1sen!2sus" 
                                referrerpolicy="no-referrer-when-downgrade"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy"
                                >
                            </iframe>
                        <!-- </div> -->
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
                </div>

            <div class="footer-bottom">
                <p>&copy; 2025 Deka Sport Apparel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
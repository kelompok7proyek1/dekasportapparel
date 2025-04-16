<?php
    session_start();
    $loggedIn = isset($_SESSION['email']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>deka sport apparel | Premium Custom Jerseys</title>
    <link rel="stylesheet" href="../css/dashboard.css">
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
                    <li><a href="custom.php">Custom</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if($loggedIn) : ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                            <?php else : ?>
                                <li><a href="login.html" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
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
        <!-- <section class="dashboard-section" id="dashboard">
            <div class="container">
                    <div class="dashboard-header">
                        <div class="dashboard-title">My Account</div>
                        <div class="dashboard-actions">
                            <i class="fas fa-bell"></i>
                            <i class="fas fa-user-circle"></i>
                        </div>
                    </div>
                    <div class="dashboard-nav">
                        <div class="dashboard-nav-item active">Orders</div>
                        <div class="dashboard-nav-item">Designs</div>
                        <div class="dashboard-nav-item">Settings</div>
                    </div>
                    <div class="dashboard-content">
                        <div class="dashboard-cards">
                            <div class="dashboard-card">
                                <h4>Total Orders</h4>
                                <p>12</p>
                            </div> -->
                            <!-- <div class="dashboard-card">
                                <h4>Saved Designs</h4>
                                <p>5</p>
                            </div> -->
                            <!-- <div class="dashboard-card">
                                <h4>In Progress</h4>
                                <p>2</p>
                            </div>
                            <div class="dashboard-card">
                                <h4>Completed</h4>
                                <p>10</p>
                            </div>
                        </div>
                        <h3>Recent Orders</h3>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#12345</td>
                                    <td>15 Mar 2025</td>
                                    <td>Basketball Jersey x5</td>
                                    <td>$245.00</td>
                                    <td><span class="status processing">Processing</span></td>
                                </tr>
                                <tr>
                                    <td>#12344</td>
                                    <td>10 Mar 2025</td>
                                    <td>Soccer Jersey x3</td>
                                    <td>$147.00</td>
                                    <td><span class="status processing">Processing</span></td>
                                </tr>
                                <tr>
                                    <td>#12343</td>
                                    <td>28 Feb 2025</td>
                                    <td>Baseball Jersey x10</td>
                                    <td>$490.00</td>
                                    <td><span class="status completed">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>#12342</td>
                                    <td>15 Feb 2025</td>
                                    <td>Football Jersey x7</td>
                                    <td>$343.00</td>
                                    <td><span class="status completed">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section> -->
    

        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-column">
                        <h3>Deka<span> Sport Apparel</span></h3>
                        <p>Pakaian  custom premium dari Deka Sport Apparel untuk tim dan individu. Bahan berkualitas, desain luar biasa, dan layanan terbaik.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                    <div class="footer-column">
                        <h3>Contact Us</h3>
                        <ul class="footer-links">
                            <li><i class="fas fa-map-marker-alt"></i> Jl. DI Panjaitan No. 90 Indramayu</li>
                            <li><i class="fas fa-phone"></i> 0818-0469-8724</li>
                            <li><i class="fas fa-envelope"></i> dekasport4@gmail.com</li>
                        </ul>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2025 Deka Sport Apparel. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
</body>
</html>
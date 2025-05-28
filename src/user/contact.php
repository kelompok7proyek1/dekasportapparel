<?php
    session_start();
    $loggedIn = isset($_SESSION['id_pelanggan']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/contact.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</head>
<body>
    <!-- Header Section -->
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
                    <a href="profile.php?id=<?= $_SESSION['id_pelanggan'] ?>" class="login-btn">My Account</a>
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


       <!-- Contact Section -->
       <section class="contact">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-info">
                    <h1>Butuh Konsultasi...?</h1>
                    <h2>Silahkan kontak kami</h2>
                    <p>Kami Siap Membantu</p>
                    
                    <div class="contact-details">
                        <h3>Kontak</h3>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Jl. Di Panjaitan No. 90 Indramayu</p>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <p>0818-0469-8724</p>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <p>dekasport4@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="social-media">
                        <h3>Social Media</h3>
                        <div class="social-icons">
                            <a href="#">
                                <i class="fab fa-facebook"></i>
                                <span>deka sport apparel</span>
                            </a>
                            <a href="#">
                                <i class="fab fa-instagram"></i>
                                <span>deka sport apparel</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h2>ada pertanyaan..?</h2>
                    <form>
                        <div class="form-group">
                            <input type="nama" class="form-control" placeholder="Masukan nama anda disini...">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" placeholder="Pertanyaan Anda..."></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer>
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
    
</body>
</html>
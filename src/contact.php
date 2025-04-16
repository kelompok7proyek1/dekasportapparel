<?php
    session_start();
    $loggedIn = isset($_SESSION['email']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/contact.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">DekaSport<span>Apparel</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="custom.php">Custom</a></li>
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


       <!-- Contact Section -->
       <section class="contact">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-info">
                    <h1>Butuh Konsultasi..?</h1>
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
                            <input type="email" class="form-control" placeholder="Masukan email anda disini...">
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
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
    <link rel="stylesheet" href="../../css/home.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

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

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <div class="hero-content">
            <h1>Custom Jersey Berkualitas untuk Tim Anda!</h1>
            <p>Deka Sport Apparel hadir untuk layanan custom jersey dengan desain eksklusif, bahan premium, dan kualitas terbaik. Cocok untuk tim olahraga</p>
            <a href="data_pelanggan.php" class="btn">Mulai Mendesain</a>
        </div>
    </div>
</section>

    <!-- custom Section -->
    <section class="custom" id="custom">
        <div class="container">
            <h2 class="section-title">KAMI MELAYANI CUSTOM</h2>
            <div class="custom-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3>JERSEY BOLA</h3>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3>JERSEY BASKET</h3>
                </div>
                </div>
            </div>
        </div>
    </section>

    

    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="container">
            <h2 class="section-title">ABOUT US</h2>
            <div class="about-content">
                <div class="about-text">
                    <h2>Deka Sport Apparel</h2>
                    <p>Deka Sport Apparel adalah perusahaan yang bergerak di bidang custom jersey dan perlengkapan olahraga, dengan komitmen untuk menghadirkan produk berkualitas tinggi bagi para atlet, tim, komunitas,
                    dan individu.Berkedudukan di Kabupaten Indramayu, kami terus berkembang dengan membuka cabang serta perwakilan di berbagai lokasi yang dianggap strategis dan diperlukan oleh para persero.</p>
                </div>
                <div class="about-image">
                    <img src="../../image/about.jpeg" alt="about">
                </div>
            </div>
        </div>
    </section>

<!-- custom Section -->
<section class="custom" id="custom">
    <div class="container">
        <h2 class="section-title">KENAPA HARUS DEKA SPORT APPAREL?</h2>
        <div class="custom-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <h3>BAHAN BERKUALITAS</h3>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-brush"></i>
                </div>
                <h3>WARNA CERAH</h3>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>PENGERJAAN CEPAT</h3>
            </div>
        </div>
        </div>
    </div>
</section>
    
    <!-- CTA Section -->
    <section class="cta-section2">
        <div class="container">
            <h1>SEKARANG GILIRAN TIM MU !!!</h1><br>
            <a href="#whatsapp" class="btn">Konsultasi Sekarang</a>
        </div>
    </section>


    <!-- Footer -->
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
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
    <link rel="stylesheet" href="../css/home.css">
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

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <div class="hero-content">
            <h1>Custom Jersey Berkualitas untuk Tim Anda!</h1>
            <p>Deka Sport Apparel hadir untuk layanan custom jersey dengan desain eksklusif, bahan premium, dan kualitas terbaik. Cocok untuk tim olahraga</p>
            <a href="#custom" class="btn">Temukan Desain</a>
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

    <!-- Custom Jersey Section
    <section class="custom-section" id="custom">
        <div class="container">
            <h2 class="section-title">Create Your Custom Jersey</h2>
            <div class="custom-content">
                <div class="custom-image">
                    <img src="image/custom.png" alt="custom">
                </div>
                <div class="custom-text">
                    <h2>Design Your Dream Jersey in Minutes</h2>
                    <p>Our easy-to-use customization platform allows you to create the perfect jersey for your team or personal use. Choose from a wide range of colors, styles, and custom.</p>
                    <div class="custom-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Choose Your Style</h4>
                                <p>Select from our range of jersey styles for various sports.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Add Your Design</h4>
                                <p>Upload logos, choose colors, and add text to personalize your jersey.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Place Your Order</h4>
                                <p>Review your design, choose quantities, and complete your purchase.</p>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn">Start Designing</a>
                </div>
            </div>
        </div>
    </section> -->

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
                    <img src="../image/about.jpeg" alt="about">
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
            <a href="#custom" class="btn">Konsultasi Sekarang</a>
        </div>
    </section>


    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Deka<span> Sport Apparel</span></h3>
                    <p>Pakaian  custom premium dari Deka Sport Apparel <br> untuk tim dan individu. berkualitas, desain luar <br> biasa, dan layanan terbaik.</p>
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
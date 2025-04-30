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
    <link rel="stylesheet" href="../../css/about.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</head>
<body>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">DekaSport<span>Apparel</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="data_pelanggan.php">Custom</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <?php if($loggedIn) : ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                        <!-- <li><a href="logout.php">Logout</a></li> -->
                            <?php else : ?>
                                <li><a href="login.html" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                                <!-- <li><a href="login.html">login</a></li>
                                <li><a href="registrasi.html">Register</a></li>  -->
                    <?php endif; ?>
                </ul>
                <!-- Auth buttons (Login/Register) -->
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


    <!-- About Section -->
    <section class="about-section" id="about">
            <div class="container">
                <!-- <h2 class="section-title">About Us</h2> -->
                <div class="about-content">
                    <div class="about-text">
                        <h2>Deka Sport Apparel</h2>
                        <p>Deka Sport Apparel adalah perusahaan yang bergerak di bidang custom jersey dan perlengkapan olahraga, dengan komitmen untuk menghadirkan produk berkualitas tinggi bagi para atlet, tim, komunitas,
                        dan individu.Berkedudukan di Kabupaten Indramayu, kami terus berkembang dengan membuka cabang serta perwakilan di berbagai lokasi yang dianggap strategis dan diperlukan oleh para persero.</p>
                    </div>
                    <div class="about-image">
                        <img src="../../image/about.jpeg" alt="about.jpeg">
                    </div>
                </div>
            </div>
    </section>

    <section class="maksud-section">
        <h2 class="section-title">Maksud dan Tujuan</h2>
        <div class="container">
            <div class="maksud-content">
            <div class="maksud-image">
                <img src="../../image/1.jpg" alt="1.jpg">
            </div>
            <div class ="maksud-text" >
                <ol>
                    <li>Maksud dan Tujuan Perseroan ialah menjalankan usaha dalam bidang Konstruksi, Perdagangan Barang dan Jasa.</li>
                    <li>Untuk mencapai maksud dan tujuan tersebut di atas, Perseroan dapat melaksanakan kegiatan usaha sebagai berikut:
                        <ol>
                            <li>Industri Pakaian Jadi (Bukan Penjahitan Dan Pembuatan Pakaian), Yang Meliputi: Industri pakaian jadi (konveksi) dari tekstil.</li>
                            <li>Industri Perlengkapan Pakaian Yang Utamanya Terbuat Dari Tekstil Yang Meliputi: Industri perlengkapan pakaian dari tekstil.</li>
                            <li>Industri Pakaian Jadi Rajutan Dan Sulaman/Bordir, Yang Meliputi: Industri pakaian jadi rajutan, industri pakaian jadi sulaman/bordir.</li>
                            <li>Industri Percetakan, Yang Meliputi: Industri percetakan umum.</li>
                            <li>Perdagangan besar tekstil, pakaian dan alas kaki, Yang Meliputi: Perdagangan besar tekstil, perdagangan besar pakaian, perdagangan besar alas kaki.</li>
                            <li>Perdagangan Besar Alat Tulis Dan Hasil Percetakan Dan Penerbitan Yang Meliputi: Perdagangan besar alat tulis dan gambar.</li>
                        </ol>
                    </li>
                </ol>
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

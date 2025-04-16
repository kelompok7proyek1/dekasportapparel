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
    <link rel="stylesheet" href="../css/about.css">
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
                        <img src="../image/about.jpeg" alt="about.jpeg">
                    </div>
                </div>
            </div>
    </section>

    <section class="maksud-section">
        <h2 class="section-title">Maksud dan Tujuan</h2>
        <div class="maksud-content">
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

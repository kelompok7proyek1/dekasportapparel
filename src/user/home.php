<?php
    // include 'config.php';
    session_start();
    $loggedIn = isset($_SESSION['id_pelanggan']); 

    // $resultpelanggan = $conn->query("SELECT * FROM login_dekas");
    // $row = $resultpelanggan->fetch_assoc()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>deka sport apparel | Premium Custom Jerseys</title>
    <link rel="stylesheet" href="../../css/home.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <style>
        /* Menu Toggle Styles - Optimized & Clean */
    
    /* .auth-buttons {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: #f8f9fa;
        padding: 15px 20px;
        display: flex;
        justify-content: center;
        gap: 15px;
        border-radius: 0 0 12px 12px;
        transform: translateY(-10px);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease 0.2s;
        margin-top: -1px;
        border-top: 1px solid #e9ecef;
    } */
    
    /* .auth-buttons.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    } */
    
    /* .auth-buttons .login-btn,
    .auth-buttons .register-btn {
        flex: 1;
        max-width: 120px;
        text-align: center;
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .auth-buttons .login-btn:hover,
    .auth-buttons .register-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    } */

/* Smooth slide animation */
/* Menu Toggle Styles - Optimized & Clean */
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
                        <?php else : ?>
                    <li><a href="login.php" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                        <?php endif; ?>
                </ul>
            
            <!-- Auth buttons (Login/Register) -->
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
            <h2 class="section-title2">KAMI MELAYANI CUSTOM</h2>
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
        <h2 class="section-title2">KENAPA HARUS DEKA SPORT APPAREL?</h2>
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
            <a href="https://wa.me/6282119513872?text=Halo%20saya%20ingin%20konsiltasi%20terkait%20pemesanan%20dan%20pembuatan%20jersey" class="btn">Konsultasi Sekarang</a>
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
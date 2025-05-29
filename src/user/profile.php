<?php
session_start();
$loggedIn = isset($_SESSION['id_pelanggan']);
include 'config.php';

// Jika form disubmit
if(isset($_POST['submit'])) {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $username = $_POST['nama'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    // Cek password lama
    $check_password = $conn->prepare("SELECT password FROM login_dekas WHERE id_pelanggan = ?");
    $check_password->bind_param("i", $id_pelanggan);
    $check_password->execute();
    $result_password = $check_password->get_result();
    $user_data = $result_password->fetch_assoc();

    // Verifikasi password dengan password_verify
    
    // Pesan error
    $error = "";
    
    // Verifikasi password lama
    if($user_data && password_verify($password_lama, $user_data['password'])) {
        // Cek apakah password baru dan konfirmasi sama
        if($password_baru === $konfirmasi_password) {
            // Cek requirements password (min 8 karakter, mengandung huruf dan angka)
            // && preg_match('/[A-Za-z]/', $password_baru) && preg_match('/[0-9]/', $password_baru)
            if(strlen($password_baru) >= 1 ) {
                // Hash password baru sebelum disimpan
                $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
                
                // Update data user
                $update_query = $conn->prepare("UPDATE login_dekas SET nama = ?, password = ? WHERE id_pelanggan = ?");
                $update_query->bind_param("ssi", $username, $hashed_password, $id_pelanggan);
                
                if($update_query->execute()) {
                    // Redirect ke halaman profil dengan pesan sukses
                    header("Location: profile.php?id=$id_pelanggan&status=success");
                    exit();
                } else {
                    $error = "Gagal mengupdate data. Error: " . $conn->error;
                }
            } else {
                $error = "Password baru harus minimal 8 karakter dan mengandung huruf serta angka.";
            }
        } else {
            $error = "Password baru dan konfirmasi password tidak sama.";
        }
    } else {
        $error = "Password lama tidak sesuai.";
    }
}

// Ambil data user untuk ditampilkan di form
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Pastikan yang mengakses adalah pemilik akun
    if($_SESSION['id_pelanggan'] != $id) {
        header("Location: index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM login_dekas WHERE id_pelanggan = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
} else {
    // Kalau tidak ada parameter id di URL
    header("Location: index.php");
    exit();
}

if($result->num_rows == 0) {
    echo $conn->error;
    exit();
}

// Pesan sukses jika berhasil update
$success_message = "";
if(isset($_GET['status']) && $_GET['status'] == 'success') {
    $success_message = "Profile berhasil diupdate!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - DekaSport Apparel</title>
    <link rel="stylesheet" href="../../css/contact.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <style>
        /* CSS untuk section Update Profile */
        .contact {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px 0;
            background-color: #f8f9fa;
            min-height: 80vh;
        }
        
        .contact .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .user-info {
            margin-bottom: 25px;
            color: #2c3e50;
            background-color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .user-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        
        .contact-profile {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 35px;
            transition: transform 0.3s ease;
            width: 100%;
        }
        
        .contact-profile:hover {
            transform: translateY(-5px);
        }
        
        .contact-profile h2 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 15px;
            font-size: 24px;
        }
        
        .contact-profile form div {
            margin-bottom: 15px;
        }
        
        .contact-profile label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: #333;
            font-size: 16px;
        }
        
        .contact-profile input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .contact-profile input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .password-requirements {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .contact-profile button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 14px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            flex: 1;
        }
        
        .contact-profile button:hover {
            background-color: #2980b9;
        }
        
        .contact-profile a {
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 14px 25px;
            border-radius: 6px;
            display: inline-block;
            text-align: center;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            flex: 1;
        }
        
        .contact-profile a:hover {
            background-color: #c0392b;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    
    <script>
        // Validasi form
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const passwordLamaInput = document.getElementById('password_lama');
            const passwordBaruInput = document.getElementById('password_baru');
            const konfirmasiInput = document.getElementById('konfirmasi_password');
            const passwordInvalid = document.getElementById('password-invalid');
            
            form.addEventListener('submit', function(event) {
                let isValid = true;
                
                // Cek validasi password lama
                if (passwordLamaInput.value.trim() === '') {
                    passwordInvalid.style.display = 'block';
                    isValid = false;
                } else {
                    passwordInvalid.style.display = 'none';
                }
                
                // Cek konfirmasi password
                if (passwordBaruInput.value !== konfirmasiInput.value) {
                    document.getElementById('confirm-invalid').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('confirm-invalid').style.display = 'none';
                }
                
                // Cek panjang password
                if (passwordBaruInput.value.length < 1) {
                    document.getElementById('length-invalid').style.display = 'block';
                    isValid = false;
                } else {
                    document.getElementById('length-invalid').style.display = 'none';
                }
                
                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
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


    <!-- Contact Section -->
    <section class="contact">
        <div class="container">
            <?php if(isset($error) && !empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- <div class="user-info">
                <p>Username: <?= $row['nama'] ?></p>
                <p>Silakan edit data di bawah ini:</p>
            </div> -->

            <div class="contact-profile">
                <form action="" method="POST" id="profileForm">
                    <h2>Update Profile</h2>
                    <div>
                        <label for="nama">Username: </label>
                        <input type="text" id="nama" name="nama" value="<?php echo $row['nama'] ?>" required>
                    </div>
                    <div>
                        <label for="password_lama">Password Lama: </label>
                        <input type="password" id="password_lama" name="password_lama" required>
                        <div id="password-invalid" class="invalid-feedback">Password lama harus diisi</div>
                    </div>
                    <div>
                        <label for="password_baru">Password Baru: </label>
                        <input type="password" id="password_baru" name="password_baru" required>
                        <div class="password-requirements">
                        </div>
                        <div id="length-invalid" class="invalid-feedback">Password minimal 8 karakter</div>
                    </div>
                    <div>
                        <label for="konfirmasi_password">Konfirmasi Password Baru: </label>
                        <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
                        <div id="confirm-invalid" class="invalid-feedback">Password dan konfirmasi tidak sama</div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="submit">Update</button>
                        <a href="index.php">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <!-- <footer>
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
    </footer> -->
</body>
</html>
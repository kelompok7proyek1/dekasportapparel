<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM login_dekas WHERE nama = ?");
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $result = $stmt->get_result();

if ($result->num_rows === 1) { //Untuk menghitung jumlah data yang diambil dan memastikan hanya ada satu user yang cocok dengan nama tersebut.
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['id_pelanggan'] = $user['id_pelanggan'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        if($_SESSION['role'] === 'admin'){
            header("Location: ../admin/dashboard_coba2.php") ; 
        } else{
            header("Location: home.php") ;
            exit();
        }
    }

    // Jika gagal
    $error = "Username atau password salah.";
}
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>

    <!-- Gaya asli dan Bootstrap -->
    <link rel="stylesheet" href="../../css/gateaway.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="content-left">
            <h1>Hi, Welcome to</h1>
            <h2>Deka Sport Apparel</h2>
            <p>Pakaian custom premium dari Deka Sport Apparel untuk tim dan individu. Berkualitas, desain luar biasa, dan layanan terbaik.</p>
        </div>
        
        <div class="login-container">
            <div class="login-header">
                <h2>Log in</h2>
            </div>

            <!-- ALERT ERROR -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="text" name="nama" placeholder="Username" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="login-btn">Log in</button>
            </form>
            
            <div class="register-link">
                Belum Punya Akun? <a href="register.php">Daftar</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS agar alert bisa ditutup -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

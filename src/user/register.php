<?php
session_start();
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Cek apakah password dan konfirmasi cocok
    if ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Cek apakah username sudah terdaftar
        $stmt = $conn->prepare("SELECT * FROM login_dekas WHERE nama = ?");
        $stmt->bind_param("s", $nama);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            // Hash dan simpan password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO login_dekas (role, nama, password) VALUES ('pelanggan', ?, ?)");
            $insert->bind_param("ss", $nama, $hashed);
            if ($insert->execute()) {
                $success = 'Registrasi berhasil! <a href="login.php">Login sekarang</a>';
            } else {
                $error = 'Gagal mendaftar. Coba lagi.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
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
                <h2>Register</h2>
            </div>

            <!-- ALERT -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nama" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <button type="submit" class="login-btn">Register</button>
            </form>

            <div class="register-link">
                Sudah Punya Akun? <a href="login.php">Login</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

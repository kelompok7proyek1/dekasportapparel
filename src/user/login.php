<?php
session_start();
include 'config.php'; //koneksi ke file config yang sudah terhubung di database

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM login_dekas WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['email'] = $email;
        header("Location: dashboard.php") ;
        exit();
    }
}

echo "Login gagal! <a href='login.html'>Coba lagi</a>";
?>

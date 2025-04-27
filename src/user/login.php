<?php
session_start();
include 'config.php'; //koneksi ke file config yang sudah terhubung di database

$nama = $_POST['nama'];
$password = $_POST['password'];

// $email_admin = $_POST['email_admin'];
// $password_admin = $_POST['password_admin'];

$stmt = $conn->prepare("SELECT * FROM login_dekas WHERE nama = ?");
$stmt->bind_param("s", $nama);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['nama'] = $nama;
        header("Location: index.php") ;
        exit();
    }
    // else {
    //     $_SESSION['email_admin'] = $email_admin;
    //     header("Location: index.php") ;
    // }
}

echo "Login gagal! <a href='login.html'>Coba lagi</a>";
?>

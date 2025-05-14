<?php
session_start();
include 'config.php'; //koneksi ke file config yang sudah terhubung di database

$nama = $_POST['nama'];
$password = $_POST['password'];

// $email_admin = $_POST['email_admin'];
// $password_admin = $_POST['password_admin'];

$stmt = $conn->prepare("SELECT * FROM login_dekas WHERE nama = ?"); // prepared statement untuk mencegah SQL Injection.
$stmt->bind_param("s", $nama); //digunakan untuk mengisi parameter (?) di query.
$stmt->execute(); //Menjalankan query SQL setelah parameter dimasukkan.
$result = $stmt->get_result(); //Mengambil hasil dari query SELECT.


if ($result->num_rows === 1) { //Untuk menghitung jumlah data yang diambil dan memastikan hanya ada satu user yang cocok dengan nama tersebut.
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['id_pelanggan'] = $user['id_pelanggan'];
        $_SESSION['nama'] = $user['nama'];
        // $_SESSION['nama'] = $nama;
        header("Location: home.php") ;
        exit();
    }
    // else {
    //     $_SESSION['email_admin'] = $email_admin;
    //     header("Location: home.php") ;
    // }
}

echo "Login gagal! <a href='login.html'>Coba lagi</a>";
?>

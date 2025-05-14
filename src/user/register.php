<?php
include 'config.php';

$nama = $_POST['nama'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO login_dekas (nama, password) VALUES (?, ?)"); //Menyiapkan prepared statement untuk dieksekusi di database
$stmt->bind_param("ss", $nama, $password);

if ($stmt->execute()) {
    header("Location: login.html");
} else {
    header("Location: register.html");
}
?>

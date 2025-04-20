<?php
include 'config.php';

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO login_dekas (email, password) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $password);

if ($stmt->execute()) {
    header("Location: login.html");
} else {
    header("Location: registrasi.html");
}
?>

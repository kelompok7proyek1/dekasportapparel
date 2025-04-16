<?php
$conn = new mysqli("localhost", "root", "Hafiz123", "db_dekas");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

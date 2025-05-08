<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'db_dekas';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error){
    die("KONEKSI GAGAL, TOLONG UBAH KODE PROGRAMNYA DI FILE CONFIG YA: " .$conn->connect_error);
}

?>

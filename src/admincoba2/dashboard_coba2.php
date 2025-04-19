<?php
include "../user/config.php";
session_start();

// Proteksi halaman
// if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
//     header("Location: login.php");
//     exit;
// }
// $total_artikel_result = $conn->query("SELECT COUNT(*) AS total_artikel FROM artikel");
// $total_artikel = $total_artikel_result->fetch_assoc()['total_artikel'];

//Menghitung total agenda (misalnya dari tabel agenda)
// $total_agenda_result = $conn->query("SELECT COUNT(*) AS total_agenda FROM agenda");
// $total_agenda = $total_agenda_result->fetch_assoc()['total_agenda'];

// // Menghitung total pengguna
// $total_pengguna_result = $conn->query("SELECT COUNT(*) AS total_pengguna FROM masyarakat");
// $total_pengguna = $total_pengguna_result->fetch_assoc()['total_pengguna'];

// Simpan nama admin dari session
// $adminName = $_SESSION['username'];

// Query data pelanggan dan pesanan
$resultpelanggan = $conn->query("SELECT * FROM pelanggan_dekas");
$resultpesanan = $conn->query("SELECT * FROM pesanan_dekas");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }
        .sidebar {
            height: 100vh;
            width: 240px;
            position: fixed;
            background-color: #1e293b;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            border-bottom: 1px solid #475569;
            padding-bottom: 10px;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            padding: 12px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 10px;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background-color: #334155;
        }
        .btn btn-danger {
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .main {
            margin-left: 240px;
            padding: 30px;
        }

        .header {
            background-color: #22c55e;
            padding: 20px;
            color: white;
            font-size: 24px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
            border-radius: 10px;
        }

        .card {
            background-color: white;
            padding: 25px;
            margin-top: 25px;
            border-radius: 10px;
            box-shadow: 0px 3px 8px rgba(0,0,0,0.1);
        }

        .logout-btn {
            background-color: crimson;
            color: white;
            border: none;
            padding: 10px 25px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 6px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: darkred;
        }
        .logout{
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            margin-top: 20px;
        }

        .crud-links {
            margin-top: 30px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .crud-links a {
            background-color: #3b82f6;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .crud-links a:hover {
            background-color: #2563eb;
        }
        
        /* Additional styles for tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            margin-right: 5px;
            border-radius: 3px;
            display: inline-block;
            color: white;
        }
        
        .btn-edit {
            background-color: #4CAF50;
        }
        
        .btn-delete {
            background-color: #f44336;
        }
        
        .tambah-btn {
            display: inline-block;
            background-color: #2196F3;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Dashboard Admin</h2>
    <a href="dashboard_coba2.php">Dashboard</a>
    <a href="pelanggan_crud.php">Kelola Pelanggan</a>
    <a href="pesanan_crud.php">Kelola Pesanan</a>
    <a href="#" class="btn btn-danger">Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Selamat Datang!</h1>
    </div>

    <!-- <div class="card">
        <h3>Statistik</h3>
        <ul>
            <li>Total Artikel: <?= isset($total_artikel) ? $total_artikel : '0' ?></li>
            <li>Total Agenda: <?= isset($total_agenda) ? $total_agenda : '0' ?></li>
            <li>Total Pengguna: <?= isset($total_pengguna) ? $total_pengguna : '0' ?></li>
        </ul>
    </div> -->
    
    
    
    
</div>

</body>
</html>
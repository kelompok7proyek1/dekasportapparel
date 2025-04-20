<?php
    session_start();
    $loggedIn = isset($_SESSION['email']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>3D Jersey Configurator</title>
  <link rel="stylesheet" href="../../css/custom.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r148/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/three@0.148.0/examples/js/loaders/GLTFLoader.js"></script>
  <script src="../../js/script.js"></script>
</head>
<body>
<header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">DekaSport<span>Apparel</span></a>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="data_pelanggan.php" onclick="return confirm('Silakan isi data-data anda terlebih dahulu!')">Custom</a></li>
                    <?php if($loggedIn) : ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="logout.php">Logout</a></li>
                            <?php else : ?>
                                <li><a href="login.html" onclick="return confirm('Silakan login terlebih dahulu!')">Dashboard</a></li>
                                <li><a href="login.html">login</a></li>
                                <li><a href="registrasi.html">Register</a></li> 
                    <?php endif; ?>
                </ul>
                <!-- <div class="menu-toggle" id="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div> -->
            </nav>
        </div>
    </header>

  <main>
    <div id="viewer-container">
      <div id="jersey-viewer"></div>
      <div id="controls">
        <label for="colorPicker">Pilih Warna:</label>
        <input type="color" id="colorPicker" value="#ffffff" />
        <label for="textInput">Masukkan Teks:</label>
        <input type="text" id="textInput" placeholder="Nama Tim" />
        <button id="applyBtn">Terapkan</button>
      </div>
    </div>
  </main>
  <script src="script.js"></script>
</body>
</html>

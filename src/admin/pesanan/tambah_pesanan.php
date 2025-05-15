<?php
include '../../user/config.php';

$id = $_GET['id'] ?? null; // Ambil id dari URL jika ada



// if ($result->num_rows > 0) {
//     echo "<script>alert('Data pesanan sudah ada!'); window.location.href='dashboard_coba2.php';</script>";
// } else {

    if(isset($_POST['submit'])) {
        $id_pelanggan = $_POST['id_pelanggan'];
        $id_detail = $_POST['id_detail'];
        $tanggal_pemesanan = $_POST['tanggal_pemesanan'];
        $harga_satuan = $_POST['harga_satuan'];
        $total_harga = $_POST['total_harga'];
        $status_produksi = $_POST['status_produksi'];
        $dalam_proses = $_POST['dalam_proses'];
        $selesai = $_POST['selesai'];


        $stmt = $conn->prepare( "SELECT * FROM pesanan_dekas WHERE id_detail = ?");
        $stmt->bind_param("i", $id_detail); // Bind parameter id_detail
        $stmt->execute();
        // $result = $stmt->get_result();
        $result = $stmt->fetch();
        if($result > 0){
            echo "<script>alert('Data pesanan sudah ada!'); window.location.href='../dashboard_coba2.php';</script>";
        } else {
            // Jika tidak ada, lanjutkan dengan proses insert
            // Query untuk insert data
            $sql = "INSERT INTO pesanan_dekas 
            (id_pelanggan, id_detail, tanggal_pemesanan, harga_satuan, total_harga, status_produksi, dalam_proses, selesai) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; //Menggunakan placeholder ? untuk setiap nilai yang akan dimasukkan — ini bagian dari repared statements
            $stmt = $conn->prepare($sql);//menyiapkan perintah SQL yang aman.
            $stmt->bind_param("iissssii", //bind_param() digunakan untuk menggantikan setiap ? dengan nilai variabel.
            $id_pelanggan, $id_detail, $tanggal_pemesanan, $harga_satuan, $total_harga, $status_produksi, $dalam_proses, $selesai);
            
            if($stmt->execute()) {
                // Update jumlah pesanan di tabel pelanggan
                // $update_pelanggan = $conn->query("UPDATE pelanggan_dekas SET jumlah_pesanan = jumlah_pesanan + 1 WHERE id_detail = $id_detail");
                
                echo "<script>alert('Data pesanan berhasil ditambahkan'); window.location='../dashboard_coba2.php';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan data pesanan: " . $conn->error . "');</script>";
            }
            
            $stmt->close();
        }
    }

// Ambil data pelanggan untuk dropdown
$pelanggan_result = $conn->query("SELECT * FROM pelanggan_dekas ORDER BY nama");
$detail_result = $conn->query("
    SELECT dp.*, pl.nama AS nama_pelanggan
    FROM detail_pesanan dp
    JOIN pelanggan_dekas pl ON dp.id_pelanggan = pl.id_pelanggan
    ORDER BY dp.id_detail
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pesanan</title>
    <link rel="stylesheet" href="../../css/dashboard.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .btn-cancel {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">DekaSport<span>Apparel</span></a>
            </nav>
            <h2>Tambah Pesanan Baru</h2>
        </div>
    </header>

    
    
    <div class="form-container">
        <form method="POST" action="">

            <div class="form-group">
                <label for="id_pelanggan">Pelanggan:</label>
                <select id="id_pelanggan" name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while($row = $pelanggan_result->fetch_assoc()): ?>
                        <?php if ($row['id_pelanggan'] == $id): ?>
                            <option value="<?= $row['id_pelanggan'] ?>" selected>
                                <?= $row['id_pelanggan'] ?> (<?= $row['nama'] ?>)
                            </option>
                        <?php else: ?>
                            <option value="<?= $row['id_pelanggan'] ?>">
                                <?= $row['id_pelanggan'] ?> (<?= $row['nama'] ?>)
                            </option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_detail">Detail Pesanan:</label>
                <!-- <input type='text' id='id_detail' name='id_detail' value=<?= $id ?> readonly> -->
                <select id="id_detail" name="id_detail" required>
                    <option value="">-- Pilih Pesanan --</option>
                    <?php while($row = $detail_result->fetch_assoc()): ?>
                        <?php if ($row['id_detail'] == $id): ?>
                            <!-- Jika id_detail sudah ada, tampilkan sebagai selected -->
                            <option value="<?= $row['id_detail'] ?>" selected>
                                <?= $row['id_detail'] ?> (<?= $row['nama_pelanggan'] ?>)
                            </option>
                        <?php else: ?>
                            <option value="<?= $row['id_detail'] ?>">
                                <?= $row['id_detail'] ?> (<?= $row['nama_pelanggan'] ?>)
                            </option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </select>
            </div>

            



            
            <div class="form-group">
                <label for="tanggal_pemesanan">Tanggal Pemesanan:</label>
                <input type="date" id="tanggal_pemesanan" name="tanggal_pemesanan" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="harga_satuan">Harga satuan:</label>
                <input type="number" id="harga_satuan" name="harga_satuan" value="0" required>
            </div>

            
            <div class="form-group">
                <label for="status_produksi">Status Pemesanan:</label>
                <select id="status_produksi" name="status_produksi" required>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="selesai">selesai</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            
            <div class="form-group">
                <label for="dalam_proses">In Progress:</label>
                <input type="number" id="dalam_proses" name="dalam_proses" value="0" required>
            </div>
            
            <div class="form-group">
                <label for="selesai">selesai:</label>
                <input type="text" id="selesai" name="selesai" value="0" required>
            </div>
            
            <div class="form-group">
                <label for="total_harga">Total Harga:</label>
                <input type="number" id="total_harga" name="total_harga" required>
            </div>

            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Simpan</button>
                <a href="../dashboard_coba2.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
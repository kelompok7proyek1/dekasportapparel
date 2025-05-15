<?php
include '../../user/config.php';

$detail_result = $conn->query("SELECT * FROM pelanggan_dekas ORDER BY id_pelanggan");

if(isset($_POST['submit'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $jenis_jersey = $_POST['jenis_jersey'];
    $bahan_jersey = $_POST['bahan_jersey'];
    $paket_jersey = $_POST['paket_jersey'];
    $nama_pemain = $_POST['nama_pemain'];
    $nomor_punggung = $_POST['nomor_punggung'];
    $logo = $_POST['logo'];
    $ukuran = $_POST['ukuran'];
    $motif = $_POST['motif'];
    $total_jersey = $_POST['total_jersey'];
    $kode_jersey = $_POST['kode_jersey'];

    $stmt = $conn->prepare("SELECT * FROM detail_pesanan WHERE id_pelanggan = ?");
    $stmt->bind_param("i", $id_pelanggan);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo "<script>alert('Data pesanan sudah ada!'); window.location.href='../dashboard_coba2.php';</script>";
    } else {
        $sql = "INSERT INTO detail_pesanan(
            id_pelanggan, jenis_jersey, bahan_jersey, paket_jersey, 
            nama_pemain, nomor_punggung, logo, ukuran, motif, total_jersey, kode_jersey
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssssssis", $id_pelanggan, $jenis_jersey, $bahan_jersey, $paket_jersey,
            $nama_pemain, $nomor_punggung, $logo, $ukuran, $motif, $total_jersey, $kode_jersey);

        if($stmt->execute()) {
            echo "<script>alert('Data pesanan berhasil ditambahkan'); window.location='../dashboard_coba2.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data pesanan: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Detail Pesanan</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .form-container {
            max-width: 600px;
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
        .form-group input,
        .form-group select,
        .form-group textarea {
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
    <h2>Tambah Detail Pesanan</h2>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_pelanggan">Pesanan</label>
                <select id="id_pelanggan" name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while($row = $detail_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_pelanggan'] ?>"><?= $row['id_pelanggan'] ?> (<?= $row['nama'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="jenis_jersey">Jenis Jersey</label>
                <select id="jenis_jersey" name="jenis_jersey" required>
                    <option value="">-- Pilih Jenis Jersey --</option>
                    <option value="Jerser Bola">Jersey Bola</option>
                    <option value="Jersey Basket">Jersey Basket</option>

                   
                </select>
            </div>

            <div class="form-group">
                <label for="bahan_jersey">Bahan Jersey</label>
                <select id="bahan_jersey" name="bahan_jersey" required>
                    <option value="">-- Pilih Bahan Jersey --</option>
                    <option value="milano">Milano</option>
                    <option value="Dryfit Puma">Dryfit Puma</option>
                    <option value="Embos Batik">Embos Batik</option>
                    <option value="Dryfit Lite">Dryfit Lite</option>
                    <option value="Jacquard Camo">Jacquard Camo</option>

                   
                </select>
            </div>

            <div class="form-group">
                <label for="paket_jersey">Paket Jersey</label>
                <select id="paket_jersey" name="paket_jersey" required>
                    <option value="">-- Pilih Paket Jersey --</option>
                    <option value="Paket Basic">Paket Basic</option>
                    <option value="Paket Front Print">Paket Frontprint</option>
                    <option value="Paket Halfprint">Paket Halfprint</option>
                    <option value="Paket Fullprint">Paket Fullprint</option>

                   
                </select>
            </div>

            <div class="form-group">
                <label for="nama_pemain">Nama Pemain</label>
                <input type="text" id="nama_pemain" name="nama_pemain" required>
            </div>

            <div class="form-group">
                <label for="nomor_punggung">Nomor Punggung</label>
                <input type="text" id="nomor_punggung" name="nomor_punggung" required>
            </div>

            <div class="form-group">
                <label for="logo">Logo</label>
                <input type="file" id="logo" name="logo" required>
            </div>

            <div class="form-group">
                <label for="ukuran">Ukuran</label>
                <input type="text" id="ukuran" name="ukuran" required>
            </div>

            <div class="form-group">
                <label for="motif">Motif</label>
                <input type="file" id="motif" name="motif" required>
            </div>

            <div class="form-group">
                <label for="total_jersey">Total Jersey</label>
                <input type="number" id="total_jersey" name="total_jersey" required>
            </div>

            <div class="form-group">
                <label for="kode_jersey">Kode Jersey</label>
                <input type="text" id="kode_jersey" name="kode_jersey" required>
            </div>

            <div class="form-group">
                <button type="submit" name="submit" class="btn-submit">Simpan</button>
                <a href="../dashboard_coba2.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>

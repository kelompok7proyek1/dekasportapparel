<?php 
    include 'config.php';
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT nama FROM pelanggan_dekas WHERE id_pelanggan = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    
    if($result-> num_rows > 0) {
        echo $row["nama"];
    } else {
        echo $conn->error;
    }

    
    $result->close();
    
?>
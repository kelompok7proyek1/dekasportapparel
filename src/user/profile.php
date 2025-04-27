<?php 
    include 'config.php';
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM login_dekas WHERE nama = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();


    if($result-> num_rows > 0) {
        echo "Username: " . $row["nama"] . "<br>";
        echo "Password: " . $row["password"] . "<br>";
    } else {
        echo $conn->error;
    }


    $result->close();
    
?>
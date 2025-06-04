<?php
include '../../user/config.php';


if (isset($_GET['id'])) {
    $id_detail = $_GET['id'];
    
    // First, check if there are any related records in pesanan_dekas
    $check_query = "SELECT COUNT(*) as count FROM pesanan_dekas WHERE id_detail = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $id_detail);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        // Get the specific order IDs to show to user
        $order_query = "SELECT DISTINCT id_pesanan FROM pesanan_dekas WHERE id_detail = ?";
        $order_stmt = $conn->prepare($order_query);
        $order_stmt->bind_param("i", $id_detail);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        
        $order_ids = [];
        while ($order_row = $order_result->fetch_assoc()) {
            $order_ids[] = $order_row['id_pesanan'];
        }
        
        $order_list = implode(', ', $order_ids);
        
        echo "<script>
                alert('Tidak dapat menghapus detail pesanan! Hapus terlebih dahulu pesanan dengan ID: " . $order_list . "');
                window.history.back();
              </script>";
        
        $order_stmt->close();
        $check_stmt->close();
        exit;
    }
    
    // If no related records, proceed with deletion
    $delete_query = "DELETE FROM detail_pesanan WHERE id_detail = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $id_detail);
    
    if ($delete_stmt->execute()) {
        echo "<script>
                alert('Detail pesanan berhasil dihapus!');
                window.location.href = 'index.php'; // redirect to your list page
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus detail pesanan!');
                window.history.back();
              </script>";
    }
    
    $delete_stmt->close();
    $check_stmt->close();
    
} else {
    echo "<script>
            alert('ID detail pesanan tidak ditemukan!');
            window.history.back();
          </script>";
}

// Close database connection
if (isset($mysqli)) {
    $mysqli->close();
}
?>
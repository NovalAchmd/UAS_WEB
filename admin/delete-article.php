<?php
session_start();
require_once '../config/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    
    $sql = "SELECT images FROM artikel WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (!empty($row['images']) && file_exists($row['images'])) {
            unlink($row['images']);
        }
    }

    $sql = "DELETE FROM artikel WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Artikel Berhasil diHapus";
    } else {
        $_SESSION['message'] = "Error deleting article: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();

header("Location: dashboard.php#2");
exit();
?>

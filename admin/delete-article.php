<?php
session_start();
require_once '../config/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // First, get the image path to delete the file
    $sql = "SELECT images FROM artikel WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Delete the image file if it exists
        if (!empty($row['images']) && file_exists($row['images'])) {
            unlink($row['images']);
        }
    }

    // Delete the article from database
    $sql = "DELETE FROM artikel WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Article deleted successfully";
    } else {
        $_SESSION['message'] = "Error deleting article: " . $conn->error;
    }

    $stmt->close();
}

// Always close the connection
$conn->close();

// Redirect to the manage articles page after deletion
header("Location: manage-articles.php");
exit();
?>

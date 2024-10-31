<?php
session_start();
require_once '../config/config.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ID artikel tidak ditemukan.";
    header("Location: dashboard.php#2");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM artikel WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['message'] = "Artikel tidak ditemukan.";
    header("Location: dashboard.php#2");
    exit();
}

$article = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $conn->real_escape_string($_POST['judul']);
    $isi = $conn->real_escape_string($_POST['isi']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $author = $conn->real_escape_string($_POST['author']);
    $tanggal_publikasi = $conn->real_escape_string($_POST['tanggal_publikasi']);

    // File upload handling
    $uploadOk = 1;
    $newImage = false;

    if (!empty($_FILES["fileToUpload"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $_SESSION['message'] = "File bukan gambar.";
            $uploadOk = 0;
        }

        // Check file format
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['message'] = "Hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
            $uploadOk = 0;
        }

        // Move file and delete old image
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                if (!empty($article['images']) && file_exists($article['images'])) {
                    unlink($article['images']);
                }
                $newImage = true;
            } else {
                $_SESSION['message'] = "Gagal mengunggah gambar.";
                $uploadOk = 0;
            }
        }
    }

    // Update query
    if ($uploadOk == 1) {
        $sql = "UPDATE artikel SET judul = ?, isi = ?, kategori = ?, author = ?, tanggal_publikasi = ?";
        if ($newImage) {
            $sql .= ", images = ?";
        }
        $sql .= " WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        if ($newImage) {
            $stmt->bind_param("ssssssi", $judul, $isi, $kategori, $author, $tanggal_publikasi, $target_file, $id);
        } else {
            $stmt->bind_param("sssssi", $judul, $isi, $kategori, $author, $tanggal_publikasi, $id);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Artikel berhasil diperbarui.";
        } else {
            $_SESSION['message'] = "Error: " . $conn->error;
        }
        $stmt->close();

        header("Location: dashboard.php#2");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Artikel</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert">
            <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Judul:</label>
        <input type="text" name="judul" value="<?php echo htmlspecialchars($article['judul']); ?>" required>

        <label>Isi:</label>
        <textarea name="isi" required><?php echo htmlspecialchars($article['isi']); ?></textarea>

        <label>Kategori:</label>
        <select name="kategori">
            <option value="Technology" <?php echo $article['kategori'] == 'Technology' ? 'selected' : ''; ?>>Technology</option>
            <option value="Lifestyle" <?php echo $article['kategori'] == 'Lifestyle' ? 'selected' : ''; ?>>Lifestyle</option>
        </select>

        <label>Penulis:</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($article['author']); ?>" required>

        <label>Tanggal Publikasi:</label>
        <input type="date" name="tanggal_publikasi" value="<?php echo $article['tanggal_publikasi']; ?>" required>

        <label>Gambar Saat Ini:</label>
        <?php if (!empty($article['images'])): ?>
            <img src="<?php echo $article['images']; ?>" width="100" height="100">
        <?php endif; ?>

        <label>Gambar Baru (Opsional):</label>
        <input type="file" name="fileToUpload" accept="image/*">

        <button type="submit">Simpan Perubahan</button>
    </form>
</body>
</html>

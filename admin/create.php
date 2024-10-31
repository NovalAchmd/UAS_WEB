<?php  
session_start(); // Start the session

// Database connection
$conn = new mysqli('localhost', 'root', '', 'blog');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$target_dir = "uploads/";  
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);  
$uploadOk = 1;  
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));  

// Check if the file is an image  
if (isset($_POST["submit"])) { 
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);   
    if ($check !== false) {  
        $uploadOk = 1;  
    } else {  
        $_SESSION['message'] = "File bukan gambar."; // Set error message
        $uploadOk = 0;  
    }  
}  

// Check if the file already exists  
if (file_exists($target_file)) {  
    $_SESSION['message'] = "File sudah ada."; // Set error message
    $uploadOk = 0;  
}  

// Check file size  
if ($_FILES["fileToUpload"]["size"] > 500000) {  
    $_SESSION['message'] = "Ukuran file terlalu besar."; // Set error message
    $uploadOk = 0;  
}  

// Allow certain file formats  
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {  
    $_SESSION['message'] = "Hanya file JPG, JPEG, PNG & GIF yang diizinkan."; // Set error message
    $uploadOk = 0;  
}  

// Check if $uploadOk is set to 0 by an error  
if ($uploadOk == 0) {  
    $_SESSION['message'] = "File tidak berhasil diunggah."; // Set error message
} else {  
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {  
        // Prepare data for insertion
        $judul = $_POST['judul'];
        $isi = $_POST['isi'];
        $kategori = $_POST['kategori'];
        $author = $_POST['author'];
        $tanggal_publikasi = $_POST['tanggal_publikasi'];
        $dest_path = $target_file; // Path of the uploaded file

        // SQL Insert Statement
        $sql = "INSERT INTO artikel (judul, isi, kategori, author, tanggal_publikasi, images) 
                VALUES ('$judul', '$isi', '$kategori', '$author', '$tanggal_publikasi', '$dest_path')";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Artikel berhasil ditambahkan"; // Set success message
        } else {
            $_SESSION['message'] = "Error: " . $conn->error; // Set error message
        }
    } else {  
        $_SESSION['message'] = "Terjadi kesalahan saat mengunggah file."; // Set error message
    } 
}
$conn->close();

// Redirect back to the dashboard
header("Location: dashboard.php");
exit();
?>

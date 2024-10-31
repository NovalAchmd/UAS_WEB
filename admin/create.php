<?php  
session_start(); 


$conn = new mysqli('localhost', 'root', '', 'blog');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$target_dir = "uploads/";  
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);  
$uploadOk = 1;  
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));  
 
if (isset($_POST["submit"])) { 
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);   
    if ($check !== false) {  
        $uploadOk = 1;  
    } else {  
        $_SESSION['message'] = "File bukan gambar."; 
        $uploadOk = 0;  
    }  
}  

// Check if the file already exists  
if (file_exists($target_file)) {  
    $_SESSION['message'] = "File sudah ada."; 
    $uploadOk = 0;  
}  

// Check file size  
if ($_FILES["fileToUpload"]["size"] > 500000) {  
    $_SESSION['message'] = "Ukuran file terlalu besar."; 
    $uploadOk = 0;  
}  
 
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {  
    $_SESSION['message'] = "Hanya file JPG, JPEG, PNG & GIF yang diizinkan."; 
    $uploadOk = 0;  
}  

if ($uploadOk == 0) {  
    $_SESSION['message'] = "File tidak berhasil diunggah."; 
} else {  
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {  

        $judul = $_POST['judul'];
        $isi = $_POST['isi'];
        $kategori = $_POST['kategori'];
        $author = $_POST['author'];
        $tanggal_publikasi = $_POST['tanggal_publikasi'];
        $dest_path = $target_file; 

        $sql = "INSERT INTO artikel (judul, isi, kategori, author, tanggal_publikasi, images) 
                VALUES ('$judul', '$isi', '$kategori', '$author', '$tanggal_publikasi', '$dest_path')";


        if ($conn->query($sql) === TRUE) {
            $_SESSION['message1'] = "Artikel berhasil ditambahkan"; 
        } else {
            $_SESSION['message1'] = "Error: " . $conn->error; 
        }
    } else {  
        $_SESSION['message1'] = "Terjadi kesalahan saat mengunggah file."; 
    } 
}
$conn->close();

header("Location: dashboard.php#1");
exit();
?>

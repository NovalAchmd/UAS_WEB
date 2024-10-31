<?php  
session_start(); 

$conn = new mysqli('localhost', 'root', '', 'blog');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if(isset($_POST["submit"])) {
    $uploadOk = 1;
    $image_path = '';
    
    // Handle image upload if a file was selected
    if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["size"] > 0) {
        $target_dir = "../uploads/";  // Note the ../ to move up one directory
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check === false) {
            $_SESSION['message1'] = "File bukan gambar.";
            $uploadOk = 0;
        }
        
        // Check file size (500KB limit)
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $_SESSION['message1'] = "Ukuran file terlalu besar.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" ) {
            $_SESSION['message1'] = "Hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
            $uploadOk = 0;
        }
        
        // If everything is ok, try to upload file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $image_path = "uploads/" . $new_filename;  // Store relative path in database
            } else {
                $_SESSION['message1'] = "Terjadi kesalahan saat mengunggah file.";
                $uploadOk = 0;
            }
        }
    }
    
    // Continue with article creation if image upload was successful or no image was uploaded
    if($uploadOk != 0) {
        $judul = $conn->real_escape_string($_POST['judul']);
        $isi = $conn->real_escape_string($_POST['isi']);
        $kategori = $conn->real_escape_string($_POST['kategori']);
        $author = $conn->real_escape_string($_POST['author']);
        $tanggal_publikasi = $conn->real_escape_string($_POST['tanggal_publikasi']);
        
        $sql = "INSERT INTO artikel (judul, isi, kategori, author, tanggal_publikasi, images) 
                VALUES (?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $judul, $isi, $kategori, $author, $tanggal_publikasi, $image_path);
        
        if ($stmt->execute()) {
            $_SESSION['message1'] = "Artikel berhasil ditambahkan";
        } else {
            $_SESSION['message1'] = "Error: " . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
header("Location: dashboard.php#1");
exit();
?>
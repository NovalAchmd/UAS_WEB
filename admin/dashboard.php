=<?php
session_start();
require_once '../config/config.php'; 

$sql = "SELECT * FROM artikel ORDER BY tanggal_publikasi DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        .article-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .article-form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ddd;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin: 10px 0;
            display: none;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Blog Web</h2>
        <nav>
            <a href="#1" class="tab-button" onclick="showTab('tambah-artikel')">Tambah Artikel</a>
            <a href="#2" class="tab-button" onclick="showTab('melihat-artikel')">Melihat Artikel</a>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
        </header>
        
        <!-- Tambah Artikel Tab -->
        <div class="tab-content" id="tambah-artikel" style="display: none;">
            <h2>Tambah Artikel</h2>
            <?php if(isset($_SESSION['message1'])): ?>
                <div class="alert alert-info">
                    <?php 
                    echo $_SESSION['message1']; 
                    unset($_SESSION['message1']);
                    ?>
                </div>
            <?php endif; ?>
            <form class="article-form" action="create.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Judul:</label>
                    <input type="text" class="form-control" name="judul" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Isi:</label>
                    <textarea class="form-control" name="isi" rows="6" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori:</label>
                    <select class="form-control" name="kategori">
                        <option value="Technology">Technology</option>
                        <option value="Lifestyle">Lifestyle</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Author:</label>
                    <input type="text" class="form-control" name="author" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Publikasi:</label>
                    <input type="date" class="form-control" name="tanggal_publikasi" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image:</label>
                    <input type="file" class="form-control" name="fileToUpload" accept="image/*" onchange="previewImage(this)">
                    <img id="preview" class="preview-image">
                </div>

                <button type="submit" class="btn btn-primary" name="submit">Save Article</button>
            </form>
        </div>

        <!-- Manage Articles Tab -->
        <div class="tab-content" id="melihat-artikel" style="display: none;">
            <h2>Manage Articles</h2>
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-info">
                    <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <table class="table table-striped">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Author</th>
                    <th>Tanggal Publikasi</th>
                    <th>Foto</th>
                    <th>Views</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['tanggal_publikasi'])); ?></td>
                            <td>
                                <?php if (!empty($row['images'])): ?>
                                    <img src="../<?php echo htmlspecialchars($row['images']); ?>" alt="Article Image" class="article-image">
                                <?php else: ?>
                                    <img src="../assets/images/default.jpg" alt="Default Image" class="article-image">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['views'] ?? 0; ?></td>
                            <td>
                                <a href="edit-article.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <button onclick="deleteArticle(<?php echo $row['id']; ?>)" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No articles found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            document.getElementById(tabId).style.display = 'block';
        }

        window.onload = function() {
            const hash = window.location.hash;
            if (hash === '#1') {
                showTab('tambah-artikel');
            } else if (hash === '#2') {
                showTab('melihat-artikel');
            } else {
                showTab('tambah-artikel');
            }
        };

        function deleteArticle(id) {
            if(confirm('Are you sure you want to delete this article?')) {
                window.location.href = 'delete-article.php?id=' + id;
            }
        }

        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
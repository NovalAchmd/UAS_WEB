<?php
session_start();
require_once '../config/config.php';

// Fetch all articles from database
$sql = "SELECT * FROM artikel ORDER BY tanggal_publikasi DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="sidebar">
        <h2>Blog Web</h2>
        <nav>
            <a href="dashboard.php" class="tab-button">Tambah Artikel</a>
            <button class="tab-button" onclick="showTab('melihat-artikel')">Melihat Artikel</button>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
        </header>

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
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Publication Date</th>
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
                                <td><?php echo $row['views'] ?? 0; ?></td>
                                <td>
                                    <a href="edit-article.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <button onclick="deleteArticle(<?php echo $row['id']; ?>)" class="btn btn-sm btn-danger">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No articles found</td>
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

        function deleteArticle(id) {
            if(confirm('Are you sure you want to delete this article?')) {
                window.location.href = 'delete-article.php?id=' + id + '&showTab=melihat-artikel';
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>

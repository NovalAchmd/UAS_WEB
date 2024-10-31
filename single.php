<?php
// Start session and include database config
session_start();
require_once 'config/config.php';

// Check if the ID is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = intval($_GET['id']);

    // Fetch the article based on the ID
    $sql = "SELECT * FROM artikel WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the article exists
    if ($result->num_rows > 0) {
        $article = $result->fetch_assoc();
    } else {
        echo '<h2>Article not found</h2>';
        exit;
    }

    // Update views count
    $update_views_sql = "UPDATE artikel SET views = views + 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_views_sql);
    $update_stmt->bind_param("i", $article_id);
    $update_stmt->execute();
} else {
    echo '<h2>Invalid article ID</h2>';
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($article['judul']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cabin:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <style>
        .article-single {
            padding: 2rem;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 8px;
            margin-bottom: 2rem;
            max-width: 600px; /* Set a maximum width for the image */
            width: 100%;

        }
        .article-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
            max-width: 600px; /* Set a maximum width for the image */
            width: 100%;
        }
        .container {
        display: flex;
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically if needed */
        padding: 2rem; /* Add some padding */
    }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="article-single">
            <h2><?php echo htmlspecialchars($article['judul']); ?></h2>
            <img src="<?php echo !empty($article['images']) && file_exists($article['images']) ? htmlspecialchars($article['images']) : 'assets/images/default.jpg'; ?>" alt="<?php echo htmlspecialchars($article['judul']); ?>" class="article-image">
            <div class="author-info">
                <span class="author"><?php echo htmlspecialchars($article['author']); ?></span> | 
                <span class="date"><?php echo date('F j, Y', strtotime($article['tanggal_publikasi'])); ?></span> | 
                <span class="views"><?php echo $article['views']; ?> reads</span>
            </div>
            <div class="article-content">
                <p><?php echo nl2br(htmlspecialchars($article['isi'])); ?></p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

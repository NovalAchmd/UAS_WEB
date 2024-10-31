<?php
// Start session and include database config
session_start();
require_once 'config/config.php';

// Pagination setup
$limit = 5; // Number of articles per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$start = ($page - 1) * $limit; // Calculate the starting point for the query

// Count total number of articles
$count_sql = "SELECT COUNT(*) AS count FROM artikel";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_articles = $count_row['count'];

// Calculate total pages
$total_pages = ceil($total_articles / $limit);

// Fetch most recent posts with pagination
$sql = "SELECT * FROM artikel ORDER BY tanggal_publikasi DESC, id DESC LIMIT $start, $limit";
$result = $conn->query($sql);

// Fetch trending posts
$trending_sql = "SELECT * FROM artikel ORDER BY views DESC LIMIT 4";
$trending_result = $conn->query($trending_sql);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Web Programming - Final Semester Exam</title>
    <link href="https://fonts.googleapis.com/css2?family=Cabin:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <style>
        .article-container {
            display: flex;
            flex-direction: row;
            gap: 2rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }
        .article-content {
            flex: 1;
        }
        .article-image {
            width: 259.55px; 
            height: 216.8px;
            min-width: 300px;
            overflow: hidden;
            border-radius: 8px;
        }
        .article-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        @media (max-width: 768px) {
            .article-container {
                flex-direction: column;
            }
            .article-image {
                width: 100%;
                min-width: 100%;
                order: -1;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="w3l-homeblock1">
        <div class="container pt-lg-5 pt-md-4">
            <div class="row">
                <div class="col-lg-9 most-recent">
                    <h3 class="section-title-left">Most Recent posts</h3>
                    <div class="list-view">
                        <?php 
                        if($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $image_path = !empty($row['images']) && file_exists($row['images']) ? 
                                            htmlspecialchars($row['images']) : 
                                            'assets/images/default.jpg';
                        ?>
                        <div class="article-container">
                            <div class="article-content">
                                <span class="category"><?php echo htmlspecialchars($row['kategori']); ?></span>
                                <a href="single.php?id=<?php echo $row['id']; ?>" class="blog-desc mt-0">
                                    <?php echo htmlspecialchars($row['judul']); ?>
                                </a>
                                <p><?php echo substr(strip_tags($row['isi']), 0, 150) . '...'; ?></p>
                                <div class="author align-items-center mt-3 mb-1">
                                    <a href="#author"><?php echo htmlspecialchars($row['author']); ?></a> in 
                                    <a href="#url"><?php echo htmlspecialchars($row['kategori']); ?></a>
                                </div>
                                <ul class="blog-meta">
                                    <li class="meta-item blog-lesson">
                                        <span class="meta-value"><?php echo date('F j, Y', strtotime($row['tanggal_publikasi'])); ?></span>
                                    </li>
                                    <li class="meta-item blog-students">
                                        <span class="meta-value"><?php echo $row['views']; ?> read</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="article-image">
                                <a href="single.php?id=<?php echo $row['id']; ?>">
                                    <img src="<?php echo $image_path; ?>" alt="" class="radius-image">
                                </a>
                            </div>
                        </div>
                        <?php 
                            }
                        } else {
                            echo '<p>No posts found</p>';
                        }
                        ?>
                    </div>

                    <!-- Pagination Navigation -->
                    <div class="pagination-wrapper mt-5">
                        <ul class="page-pagination">
                            <?php 
                            // Previous page link
                            if ($page > 1) {
                                echo '<li><a href="?page=' . ($page - 1) . '"><span class="fa fa-angle-left"></span></a></li>';
                            }

                            // Page numbers
                            for ($i = 1; $i <= $total_pages; $i++) {
                                echo '<li><a href="?page=' . $i . '" ' . 
                                     ($page == $i ? 'class="current"' : '') . 
                                     '>' . $i . '</a></li>';
                            }

                            // Next page link
                            if ($page < $total_pages) {
                                echo '<li><a href="?page=' . ($page + 1) . '"><span class="fa fa-angle-right"></span></a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 trending mt-lg-0 mt-5 mb-lg-5">
                    <div class="pos-sticky">
                        <h3 class="section-title-left">Trending</h3>
                        <?php 
                        if($trending_result->num_rows > 0) {
                            $counter = 1;
                            while($trending = $trending_result->fetch_assoc()) {
                        ?>
                        <div class="grids5-info">
                            <h4><?php echo sprintf("%02d", $counter); ?>.</h4>
                            <div class="blog-info">
                                <a href="single.php?id=<?php echo $trending['id']; ?>" class="blog-desc1">
                                    <?php echo htmlspecialchars($trending['judul']); ?>
                                </a>
                                <div class="author align-items-center mt-2 mb-1">
                                    <a href="#author"><?php echo htmlspecialchars($trending['author']); ?></a> in 
                                    <a href="#url"><?php echo htmlspecialchars($trending['kategori']); ?></a>
                                </div>
                                <ul class="blog-meta">
                                    <li class="meta-item blog-lesson">
                                        <span class="meta-value"><?php echo date('F j, Y', strtotime($trending['tanggal_publikasi'])); ?></span>
                                    </li>
                                    <li class="meta-item blog-students">
                                        <span class="meta-value"><?php echo $trending['views']; ?> read</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php
                                $counter++;
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/theme-change.js"></script>
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('.navbar-toggler').click(function () {
                $('body').toggleClass('noscroll');
            })
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="sidebar">
        <h2>Blog Web</h2>
        <nav>
            <button class="tab-button" onclick="showTab('tambah-artikel')">Tambah Artikel</button>
        </nav>
    </div>

    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
        </header>
        <div class="tab-content" id="tambah-artikel">
            <h2>Tambah Artikel</h2>
            <form class="article-form">
                <label>Title:</label>
                <input type="text" name="title" required>

                <label>Content:</label>
                <textarea name="content" required></textarea>

                <label>Category:</label>
                <select name="category">
                    <option value="Technology">Technology</option>
                    <option value="Lifestyle">Lifestyle</option>
                </select>

                <label>Author:</label>
                <input type="text" name="author" required>

                <label>Publication Date:</label>
                <input type="date" name="publication_date" required>

                <label>Image:</label>
                <input type="file" name="image" accept="image/*">

                <button type="submit">Save Article</button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            document.getElementById(tabId).style.display = 'block';
        }
    </script>
</body>
</html>

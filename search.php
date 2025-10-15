<?php
// search.php
require_once 'db.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page_title = $search ? "Search Results for: " . htmlspecialchars($search) : "Search Articles";

if ($search) {
    $search_query = "%$search%";
    $stmt = $pdo->prepare("SELECT a.id, a.title, a.thumbnail, a.summary, c.slug AS category_slug 
                           FROM articles a 
                           JOIN categories c ON a.category_id = c.id 
                           WHERE a.title LIKE ? OR a.summary LIKE ? 
                           ORDER BY a.publish_date DESC");
    $stmt->execute([$search_query, $search_query]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $articles = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - <?php echo htmlspecialchars($page_title); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background: linear-gradient(90deg, #b91c1c, #dc2626);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            font-size: 2.5em;
            letter-spacing: 2px;
            cursor: pointer;
        }

        .search-bar {
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 25px;
            outline: none;
            transition: border-color 0.3s;
        }

        .search-bar input:focus {
            border-color: #b91c1c;
        }

        .search-results {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .search-results h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #b91c1c;
        }

        .article-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .article-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            cursor: pointer;
        }

        .article-card:hover {
            transform: translateY(-5px);
        }

        .article-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .article-card h3 {
            font-size: 1.2em;
            margin: 10px;
            color: #333;
        }

        .article-card p {
            font-size: 0.9em;
            margin: 0 10px 10px;
            color: #666;
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8em;
            }

            .article-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 onclick="goToHome()">News Website</h1>
    </header>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search articles..." value="<?php echo htmlspecialchars($search); ?>" oninput="searchArticles()">
    </div>

    <section class="search-results">
        <h2><?php echo htmlspecialchars($page_title); ?></h2>
        <div class="article-grid">
            <?php
            if (count($articles) > 0) {
                foreach ($articles as $article) {
                    echo "
                    <div class='article-card' onclick='goToArticle({$article['id']}, \"{$article['category_slug']}\")'>
                        <img src='{$article['thumbnail']}' alt='{$article['title']}'>
                        <h3>{$article['title']}</h3>
                        <p>{$article['summary']}</p>
                    </div>";
                }
            } else {
                echo "<p>No articles found.</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 News Website. All rights reserved.</p>
    </footer>

    <script>
        function goToHome() {
            window.location.href = 'index.php';
        }

        function goToArticle(id, categorySlug) {
            window.location.href = `article.php?id=${id}&category=${categorySlug}`;
        }

        function searchArticles() {
            const query = document.getElementById('searchInput').value;
            if (query.length > 2) {
                window.location.href = `search.php?search=${encodeURIComponent(query)}`;
            }
        }
    </script>
</body>
</html>

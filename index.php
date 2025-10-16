<?php
// index.php
require_once 'db.php';

// Fetch featured news (latest 4 articles)
$featured_stmt = $pdo->query("SELECT a.id, a.title, a.thumbnail, a.summary, c.slug AS category_slug 
                              FROM articles a 
                              JOIN categories c ON a.category_id = c.id 
                              ORDER BY a.publish_date DESC LIMIT 4");
$featured_articles = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch breaking news (latest 2 articles)
$breaking_stmt = $pdo->query("SELECT a.id, a.title, a.summary, c.slug AS category_slug 
                              FROM articles a 
                              JOIN categories c ON a.category_id = c.id 
                              ORDER BY a.publish_date DESC LIMIT 2");
$breaking_articles = $breaking_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch trending stories (e.g., latest 3 articles, could be based on views/comments if added later)
$trending_stmt = $pdo->query("SELECT a.id, a.title, a.thumbnail, a.summary, c.slug AS category_slug 
                              FROM articles a 
                              JOIN categories c ON a.category_id = c.id 
                              ORDER BY a.publish_date DESC LIMIT 3");
$trending_articles = $trending_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for sections
$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - Home</title>
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
            position: sticky;
            top: 0;
            z-index: 1000;
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
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 40px 12px 20px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 25px;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .search-bar input:focus {
            border-color: #b91c1c;
            box-shadow: 0 0 5px rgba(185, 28, 28, 0.3);
        }

        .search-bar::after {
            content: '🔍';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2em;
        }

        .section {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #b91c1c;
            border-left: 5px solid #b91c1c;
            padding-left: 10px;
        }

        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .breaking-news, .trending-news {
            margin-bottom: 20px;
        }

        .breaking-news-item, .trending-item {
            background: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .breaking-news-item:hover, .trending-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .breaking-news-item h3, .trending-item h3 {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 5px;
        }

        .breaking-news-item p, .trending-item p {
            font-size: 0.9em;
            color: #666;
        }

        .article-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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

        .category-section {
            margin-bottom: 30px;
        }

        .category-section h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #333;
            cursor: pointer;
            transition: color 0.3s;
        }

        .category-section h3:hover {
            color: #b91c1c;
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
            width: 100%;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8em;
            }

            .featured-grid {
                grid-template-columns: 1fr;
            }

            .section h2 {
                font-size: 1.5em;
            }
        }

        @media (max-width: 480px) {
            .search-bar input {
                padding: 10px 30px 10px 15px;
                font-size: 0.9em;
            }

            .article-card img {
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 onclick="goToHome()">News Website</h1>
    </header>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search articles..." oninput="searchArticles()">
    </div>

    <section class="section breaking-news">
        <h2>Breaking News</h2>
        <?php
        if (count($breaking_articles) > 0) {
            foreach ($breaking_articles as $article) {
                echo "
                <div class='breaking-news-item' onclick='goToArticle({$article['id']}, \"{$article['category_slug']}\")'>
                    <h3>" . htmlspecialchars($article['title']) . "</h3>
                    <p>" . htmlspecialchars($article['summary']) . "</p>
                </div>";
            }
        } else {
            echo "<p>No breaking news available.</p>";
        }
        ?>
    </section>

    <section class="section featured">
        <h2>Featured News</h2>
        <div class="featured-grid">
            <?php
            if (count($featured_articles) > 0) {
                foreach ($featured_articles as $article) {
                    echo "
                    <div class='article-card' onclick='goToArticle({$article['id']}, \"{$article['category_slug']}\")'>
                        <img src='" . htmlspecialchars($article['thumbnail']) . "' alt='" . htmlspecialchars($article['title']) . "'>
                        <h3>" . htmlspecialchars($article['title']) . "</h3>
                        <p>" . htmlspecialchars($article['summary']) . "</p>
                    </div>";
                }
            } else {
                echo "<p>No featured articles available.</p>";
            }
            ?>
        </div>
    </section>

    <section class="section trending-news">
        <h2>Trending Stories</h2>
        <?php
        if (count($trending_articles) > 0) {
            foreach ($trending_articles as $article) {
                echo "
                <div class='trending-item' onclick='goToArticle({$article['id']}, \"{$article['category_slug']}\")'>
                    <h3>" . htmlspecialchars($article['title']) . "</h3>
                    <p>" . htmlspecialchars($article['summary']) . "</p>
                </div>";
            }
        } else {
            echo "<p>No trending stories available.</p>";
        }
        ?>
    </section>

    <section class="section categories">
        <h2>Categories</h2>
        <?php
        foreach ($categories as $category) {
            echo "<div class='category-section'>";
            echo "<h3 onclick='goToCategory(\"{$category['slug']}\")'>" . htmlspecialchars($category['name']) . "</h3>";
            $articles_stmt = $pdo->prepare("SELECT a.id, a.title, a.thumbnail, a.summary, c.slug AS category_slug 
                                           FROM articles a 
                                           JOIN categories c ON a.category_id = c.id 
                                           WHERE c.id = ? 
                                           ORDER BY a.publish_date DESC LIMIT 3");
            $articles_stmt->execute([$category['id']]);
            $articles = $articles_stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<div class='featured-grid'>";
            if (count($articles) > 0) {
                foreach ($articles as $article) {
                    echo "
                    <div class='article-card' onclick='goToArticle({$article['id']}, \"{$article['category_slug']}\")'>
                        <img src='" . htmlspecialchars($article['thumbnail']) . "' alt='" . htmlspecialchars($article['title']) . "'>
                        <h3>" . htmlspecialchars($article['title']) . "</h3>
                        <p>" . htmlspecialchars($article['summary']) . "</p>
                    </div>";
                }
            } else {
                echo "<p>No articles in this category.</p>";
            }
            echo "</div></div>";
        }
        ?>
    </section>

    <footer>
        <p>&copy; 2025 News Website. All rights reserved.</p>
    </footer>

    <script>
        function goToHome() {
            window.location.href = 'index.php';
        }

        function goToCategory(slug) {
            window.location.href = `category.php?slug=${slug}`;
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

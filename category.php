<?php
// category.php
require_once 'db.php';

// Fetch all categories
$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);
category
World 
Sports 
Technology 
Entertainment 
Business
Health 
Science 

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$page_title = "All Categories";
$articles = [];

if ($slug) {
    // Fetch specific category
    $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($category) {
        $articles_stmt = $pdo->prepare("SELECT a.id, a.title, a.thumbnail, a.summary, c.slug AS category_slug 
                                       FROM articles a 
                                       JOIN categories c ON a.category_id = c.id 
                                       WHERE c.id = ? 
                                       ORDER BY a.publish_date DESC");
        $articles_stmt->execute([$category['id']]);
        $articles = $articles_stmt->fetchAll(PDO::FETCH_ASSOC);
        $page_title = $category['name'];
    } else {
        $page_title = "Category Not Found";
    }
} else {
    // Fetch articles for all categories (for display in sections)
    $articles = [];
    foreach ($categories as $category) {
        $articles_stmt = $pdo->prepare("SELECT a.id, a.title, a.thumbnail, a.summary, c.slug AS category_slug 
                                       FROM articles a 
                                       JOIN categories c ON a.category_id = c.id 
                                       WHERE c.id = ? 
                                       ORDER BY a.publish_date DESC LIMIT 3");
        $articles_stmt->execute([$category['id']]);
        $articles[$category['slug']] = $articles_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        header h1 {
            font-size: 2.5em;
            letter-spacing: 2px;
            cursor: pointer;
        }

        header nav {
            margin-top: 10px;
        }

        header nav a {
            color: white;
            text-decoration: none;
            font-size: 1em;
            margin: 0 15px;
            transition: color 0.3s;
        }

        header nav a:hover {
            color: #ffd700;
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

        .articles {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .articles h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
            color: #b91c1c;
            border-left: 5px solid #b91c1c;
            padding-left: 10px;
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

        .message {
            font-size: 1.1em;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            color: #dc2626;
            background: #ffe6e6;
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

            .articles h2 {
                font-size: 1.5em;
            }

            .article-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .search-bar input {
                font-size: 0.9em;
                padding: 10px 30px 10px 15px;
            }

            .article-card img {
                height: 150px;
            }

            header nav a {
                font-size: 0.9em;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 onclick="goToHome()">News Website</h1>
        <nav>
            <a href="#" onclick="goToAddArticle()">Add Article</a>
        </nav>
    </header>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search articles..." oninput="searchArticles()">
    </div>

    <section class="articles">
        <h2><?php echo htmlspecialchars($page_title); ?></h2>
        <?php if ($slug): ?>
            <?php if ($category): ?>
                <div class="article-grid">
                    <?php if (count($articles) > 0): ?>
                        <?php foreach ($articles as $article): ?>
                            <div class="article-card" onclick="goToArticle(<?php echo $article['id']; ?>, '<?php echo $article['category_slug']; ?>')">
                                <img src="<?php echo htmlspecialchars($article['thumbnail']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                                <p><?php echo htmlspecialchars($article['summary']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="message">No articles found in this category.</div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="message">Category not found.</div>
            <?php endif; ?>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <div class="category-section">
                    <h3 onclick="goToCategory('<?php echo $category['slug']; ?>')"><?php echo htmlspecialchars($category['name']); ?></h3>
                    <div class="article-grid">
                        <?php if (!empty($articles[$category['slug']])): ?>
                            <?php foreach ($articles[$category['slug']] as $article): ?>
                                <div class="article-card" onclick="goToArticle(<?php echo $article['id']; ?>, '<?php echo $article['category_slug']; ?>')">
                                    <img src="<?php echo htmlspecialchars($article['thumbnail']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                    <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($article['summary']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="message">No articles in this category.</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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

        function goToAddArticle() {
            window.location.href = 'add_article.php';
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

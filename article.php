<?php
// article.php
require_once 'db.php';

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category_slug = isset($_GET['category']) ? $_GET['category'] : '';

$stmt = $pdo->prepare("SELECT a.*, c.name AS category_name, c.slug AS category_slug 
                       FROM articles a 
                       JOIN categories c ON a.category_id = c.id 
                       WHERE a.id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Article not found.");
}

$related_stmt = $pdo->prepare("SELECT a.id, a.title, a.thumbnail, c.slug AS category_slug 
                              FROM articles a 
                              JOIN categories c ON a.category_id = c.id 
                              WHERE a.category_id = ? AND a.id != ? 
                              ORDER BY a.publish_date DESC LIMIT 3");
$related_stmt->execute([$article['category_id'], $article_id]);
$related_articles = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

$comments_stmt = $pdo->prepare("SELECT * FROM comments WHERE article_id = ? ORDER BY comment_date DESC");
$comments_stmt->execute([$article_id]);
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_name']) && isset($_POST['comment'])) {
    $user_name = htmlspecialchars($_POST['user_name']);
    $comment = htmlspecialchars($_POST['comment']);
    $insert_stmt = $pdo->prepare("INSERT INTO comments (article_id, user_name, comment, comment_date) VALUES (?, ?, ?, NOW())");
    $insert_stmt->execute([$article_id, $user_name, $comment]);
    header("Location: article.php?id=$article_id&category=$category_slug");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - <?php echo htmlspecialchars($article['title']); ?></title>
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

        .article-content {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .article-content h2 {
            font-size: 2em;
            margin-bottom: 10px;
            color: #b91c1c;
        }

        .article-meta {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 20px;
        }

        .article-content img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .article-content p {
            font-size: 1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .related-articles {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .related-articles h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #b91c1c;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .related-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            cursor: pointer;
        }

        .related-card:hover {
            transform: translateY(-5px);
        }

        .related-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .related-card h4 {
            font-size: 1em;
            margin: 10px;
            color: #333;
        }

        .comments-section {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .comments-section h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #b91c1c;
        }

        .comment-form {
            margin-bottom: 20px;
        }

        .comment-form input, .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        .comment-form input:focus, .comment-form textarea:focus {
            border-color: #b91c1c;
        }

        .comment-form button {
            padding: 10px 20px;
            background: #b91c1c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .comment-form button:hover {
            background: #dc2626;
        }

        .comment {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .comment p {
            font-size: 0.9em;
            color: #333;
        }

        .comment .meta {
            font-size: 0.8em;
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

            .related-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 onclick="goToHome()">News Website</h1>
    </header>

    <section class="article-content">
        <h2><?php echo htmlspecialchars($article['title']); ?></h2>
        <div class="article-meta">
            <span>By <?php echo htmlspecialchars($article['author']); ?> | <?php echo date('F j, Y', strtotime($article['publish_date'])); ?> | <?php echo htmlspecialchars($article['category_name']); ?></span>
        </div>
        <img src="<?php echo htmlspecialchars($article['thumbnail']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
        <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
    </section>

    <section class="related-articles">
        <h3>Related Articles</h3>
        <div class="related-grid">
            <?php
            foreach ($related_articles as $related) {
                echo "
                <div class='related-card' onclick='goToArticle({$related['id']}, \"{$related['category_slug']}\")'>
                    <img src='{$related['thumbnail']}' alt='{$related['title']}'>
                    <h4>{$related['title']}</h4>
                </div>";
            }
            ?>
        </div>
    </section>

    <section class="comments-section">
        <h3>Comments</h3>
        <form class="comment-form" method="POST">
            <input type="text" name="user_name" placeholder="Your Name" required>
            <textarea name="comment" placeholder="Your Comment" rows="4" required></textarea>
            <button type="submit">Post Comment</button>
        </form>
        <?php
        foreach ($comments as $comment) {
            echo "
            <div class='comment'>
                <p><strong>" . htmlspecialchars($comment['user_name']) . "</strong>: " . htmlspecialchars($comment['comment']) . "</p>
                <p class='meta'>" . date('F j, Y, H:i', strtotime($comment['comment_date'])) . "</p>
            </div>";
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

        function goToArticle(id, categorySlug) {
            window.location.href = `article.php?id=${id}&category=${categorySlug}`;
        }
    </script>
</body>
</html>

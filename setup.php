<?php
// setup.php
require_once 'db.php';

try {
    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        slug VARCHAR(50) NOT NULL UNIQUE
    )");

    // Create articles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        thumbnail VARCHAR(255) NOT NULL,
        summary TEXT NOT NULL,
        content TEXT NOT NULL,
        author VARCHAR(100) NOT NULL,
        publish_date DATETIME NOT NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )");

    // Create comments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        comment TEXT NOT NULL,
        comment_date DATETIME NOT NULL,
        FOREIGN KEY (article_id) REFERENCES articles(id)
    )");

    // Insert sample categories
    $categories = [
        ['name' => 'World', 'slug' => 'world'],
        ['name' => 'Sports', 'slug' => 'sports'],
        ['name' => 'Technology', 'slug' => 'technology'],
        ['name' => 'Entertainment', 'slug' => 'entertainment']
    ];
    $cat_stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    foreach ($categories as $cat) {
        $cat_stmt->execute([$cat['name'], $cat['slug']]);
    }

    // Insert sample articles
    $articles = [
        [
            'category_id' => 1,
            'title' => 'Global Summit Addresses Climate Crisis',
            'thumbnail' => 'https://via.placeholder.com/300x200',
            'summary' => 'World leaders meet to discuss urgent climate actions.',
            'content' => 'Full content about the global summit...',
            'author' => 'John Doe',
            'publish_date' => '2025-09-10 10:00:00'
        ],
        [
            'category_id' => 2,
            'title' => 'Team Wins Championship',
            'thumbnail' => 'https://via.placeholder.com/300x200',
            'summary' => 'Local team clinches victory in a thrilling match.',
            'content' => 'Full content about the championship...',
            'author' => 'Jane Smith',
            'publish_date' => '2025-09-09 15:00:00'
        ],
        [
            'category_id' => 3,
            'title' => 'New AI Breakthrough',
            'thumbnail' => 'https://via.placeholder.com/300x200',
            'summary' => 'AI technology advances with new algorithms.',
            'content' => 'Full content about AI breakthrough...',
            'author' => 'Alice Johnson',
            'publish_date' => '2025-09-08 12:00:00'
        ],
        [
            'category_id' => 4,
            'title' => 'Movie Premiere Breaks Records',
            'thumbnail' => 'https://via.placeholder.com/300x200',
            'summary' => 'Latest blockbuster sets new box office records.',
            'content' => 'Full content about the movie premiere...',
            'author' => 'Bob Wilson',
            'publish_date' => '2025-09-07 18:00:00'
        ]
    ];
    $article_stmt = $pdo->prepare("INSERT INTO articles (category_id, title, thumbnail, summary, content, author, publish_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($articles as $art) {
        $article_stmt->execute([$art['category_id'], $art['title'], $art['thumbnail'], $art['summary'], $art['content'], $art['author'], $art['publish_date']]);
    }

    // Insert sample comments
    $comments = [
        [
            'article_id' => 1,
            'user_name' => 'User1',
            'comment' => 'Great article, very informative!',
            'comment_date' => '2025-09-10 11:00:00'
        ],
        [
            'article_id' => 1,
            'user_name' => 'User2',
            'comment' => 'I hope they take action soon!',
            'comment_date' => '2025-09-10 11:30:00'
        ]
    ];
    $comment_stmt = $pdo->prepare("INSERT INTO comments (article_id, user_name, comment, comment_date) VALUES (?, ?, ?, ?)");
    foreach ($comments as $cmt) {
        $comment_stmt->execute([$cmt['article_id'], $cmt['user_name'], $cmt['comment'], $cmt['comment_date']]);
    }

    $message = "Database setup completed successfully!";
} catch (PDOException $e) {
    $message = "Setup failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - Setup</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }

        h1 {
            font-size: 2em;
            color: #b91c1c;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1em;
            margin-bottom: 20px;
            color: <?php echo strpos($message, 'successfully') !== false ? '#28a745' : '#dc2626'; ?>;
        }

        .btn {
            padding: 10px 20px;
            background: #b91c1c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #dc2626;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.5em;
            }

            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Setup</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <button class="btn" onclick="goToHome()">Go to Homepage</button>
    </div>

    <script>
        function goToHome() {
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>

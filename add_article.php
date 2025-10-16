<?php
// add_article.php
require_once 'db.php';

// Fetch categories for the dropdown
$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $thumbnail = isset($_POST['thumbnail']) ? trim($_POST['thumbnail']) : '';
    $summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';

    // Basic validation
    if ($category_id <= 0 || empty($title) || empty($thumbnail) || empty($summary) || empty($content) || empty($author)) {
        $message = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (category_id, title, thumbnail, summary, content, author, publish_date) 
                                   VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$category_id, $title, $thumbnail, $summary, $content, $author]);
            $article_id = $pdo->lastInsertId();
            
            // Fetch category slug for redirection
            $cat_stmt = $pdo->prepare("SELECT slug FROM categories WHERE id = ?");
            $cat_stmt->execute([$category_id]);
            $category = $cat_stmt->fetch(PDO::FETCH_ASSOC);
            $category_slug = $category ? $category['slug'] : 'world';
            
            // Redirect using JavaScript (set in hidden input for client-side use)
            $redirect_url = "article.php?id=$article_id&category=$category_slug";
            $message = "Article added successfully! Redirecting...";
        } catch (PDOException $e) {
            $message = "Error adding article: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - Add Article</title>
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

        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 1.8em;
            color: #b91c1c;
            margin-bottom: 20px;
            border-left: 5px solid #b91c1c;
            padding-left: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #b91c1c;
            box-shadow: 0 0 5px rgba(185, 28, 28, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group button {
            padding: 10px 20px;
            background: #b91c1c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s;
        }

        .form-group button:hover {
            background: #dc2626;
        }

        .message {
            font-size: 1.1em;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            color: <?php echo strpos($message, 'successfully') !== false ? '#28a745' : '#dc2626'; ?>;
            background: <?php echo strpos($message, 'successfully') !== false ? '#e6ffed' : '#ffe6e6'; ?>;
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

            h2 {
                font-size: 1.5em;
            }

            .container {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 0.9em;
                padding: 8px;
            }

            .form-group button {
                padding: 8px 16px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1 onclick="goToHome()">News Website</h1>
    </header>

    <div class="container">
        <h2>Add New Article</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php if (strpos($message, 'successfully') !== false): ?>
                <input type="hidden" id="redirectUrl" value="<?php echo htmlspecialchars($redirect_url); ?>">
            <?php endif; ?>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="thumbnail">Thumbnail URL</label>
                <input type="url" id="thumbnail" name="thumbnail" placeholder="https://example.com/image.jpg" required>
            </div>
            <div class="form-group">
                <label for="summary">Summary</label>
                <textarea id="summary" name="summary" required></textarea>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" rows="8" required></textarea>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" required>
            </div>
            <div class="form-group">
                <button type="submit">Add Article</button>
            </div>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 News Website. All rights reserved.</p>
    </footer>

    <script>
        function goToHome() {
            window.location.href = 'index.php';
        }

        // Redirect to article page after successful submission
        document.addEventListener('DOMContentLoaded', function() {
            const redirectUrl = document.getElementById('redirectUrl');
            if (redirectUrl) {
                setTimeout(() => {
                    window.location.href = redirectUrl.value;
                }, 2000); // Redirect after 2 seconds
            }
        });
    </script>
</body>
</html>

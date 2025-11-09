<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect if not logged in
    exit;
}
?>

<!DOCTYPE html><?php
/**
 * ========================================
 * CREATE BLOG POST PAGE
 * ========================================
 */

define('INCLUDED', true);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Require login to access this page
requireLogin();

$pageTitle = 'Create Blog Post';
$baseUrl = getBaseUrl();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = $_POST['content'] ?? ''; // Don't sanitize content (it's Markdown)
    
    $errors = [];
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    
    if (empty($content)) {
        $errors[] = 'Content is required';
    }
    
    if (empty($errors)) {
        try {
            // Insert blog post
            $stmt = $pdo->prepare("
                INSERT INTO posts (user_id, title, content, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            
            $stmt->execute([getUserId(), $title, $content]);
            
            // Get the new post ID
            $postId = $pdo->lastInsertId();
            
            setFlashMessage('success', 'Blog post created successfully!');
            redirect('/posts/view.php?id=' . $postId);
            
        } catch (PDOException $e) {
            error_log("Create post error: " . $e->getMessage());
            $errors[] = 'Failed to create post. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | Circle Blog</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/view_post.css">
    <link href='https://unpkg.com/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- SimpleMDE Markdown Editor -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
</head>
<body>
    
    <!-- Navbar -->
    <header>
        <a href="<?php echo $baseUrl; ?>/index.php" class="logo">
            <img src="<?php echo $baseUrl; ?>/assets/images/logo-2441841.svg" alt="logo">
        </a>
        <div class="blog-name-circle">Circle Blog</div>

        <div class="icons">
            <i class='bx bx-search' id="search-icon"></i>
            <i class='bx bx-menu' id="menu-icon"></i>
        </div>

        <ul class="navbar" id="navbar">
            <li><a href="<?php echo $baseUrl; ?>/index.php">Home</a></li>
            <li><a href="<?php echo $baseUrl; ?>/posts/create.php" class="active">Create Blog</a></li>
            <li><a href="<?php echo $baseUrl; ?>/posts/my_posts.php">My Blogs</a></li>
            <li><a href="<?php echo $baseUrl; ?>/auth/logout.php">Logout</a></li>
        </ul>
    </header>

    <!-- Main Content -->
    <main class="container" style="margin-top: 100px; padding: 20px;">
        <div class="create-post-container">
            <h1><i class='bx bx-edit'></i> Create New Blog Post</h1>
            
            <!-- Display Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Create Post Form -->
            <form method="POST" action="" class="post-form">
                <div class="form-group">
                    <label for="title">
                        <i class='bx bx-heading'></i> Blog Title
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        placeholder="Enter your blog title..." 
                        required
                        value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="content">
                        <i class='bx bx-file'></i> Blog Content (Markdown Supported)
                    </label>
                    <textarea 
                        id="content" 
                        name="content" 
                        required
                    ><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    
                    <small class="help-text">
                        <i class='bx bx-info-circle'></i> 
                        Tip: Use Markdown for formatting! 
                        <strong>**bold**</strong>, 
                        <em>*italic*</em>, 
                        # Headings, 
                        - Lists
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-check'></i> Publish Blog
                    </button>
                    <a href="<?php echo $baseUrl; ?>/posts/my_posts.php" class="btn btn-secondary">
                        <i class='bx bx-x'></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Circle Blog. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const BASE_URL = '<?php echo $baseUrl; ?>';
    </script>
    <script src="<?php echo $baseUrl; ?>/assets/js/main.js"></script>
    
    <!-- Initialize Markdown Editor -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const simplemde = new SimpleMDE({
                element: document.getElementById("content"),
                spellChecker: false,
                placeholder: "Write your amazing blog post here...",
                status: ["lines", "words"],
                toolbar: [
                    "bold", "italic", "heading", "|",
                    "quote", "unordered-list", "ordered-list", "|",
                    "link", "image", "|",
                    "preview", "side-by-side", "fullscreen", "|",
                    "guide"
                ]
            });
        });
    </script>
</body>
</html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post</title>
</head>
<body>
    <h1>Create a New Blog Post</h1>
    <form action="publish_blog.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="10" cols="50" required></textarea><br><br>

        <label for="image">Image (optional):</label><br>
        <input type="file" id="image" name="image"><br><br>

        <button type="submit">Publish Blog</button>
    </form>
</body>
</html>

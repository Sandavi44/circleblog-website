<?php
/**
 * ========================================
 * EDIT BLOG POST PAGE
 * ========================================
 */

define('INCLUDED', true);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Require login
requireLogin();

$pageTitle = 'Edit Blog Post';
$baseUrl = getBaseUrl();

// Get post ID
$postId = $_GET['id'] ?? 0;

if (!$postId) {
    setFlashMessage('error', 'Post not found');
    redirect('/posts/my_posts.php');
}

// Fetch post
try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, getUserId()]);
    $post = $stmt->fetch();
    
    if (!$post) {
        setFlashMessage('error', 'Post not found or you do not have permission to edit it');
        redirect('/posts/my_posts.php');
    }
} catch (PDOException $e) {
    error_log("Fetch post error: " . $e->getMessage());
    setFlashMessage('error', 'Error loading post');
    redirect('/posts/my_posts.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    
    if (empty($content)) {
        $errors[] = 'Content is required';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE posts 
                SET title = ?, content = ? 
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([$title, $content, $postId, getUserId()]);
            
            setFlashMessage('success', 'Blog post updated successfully!');
            redirect('/posts/view.php?id=' . $postId);
            
        } catch (PDOException $e) {
            error_log("Update post error: " . $e->getMessage());
            $errors[] = 'Failed to update post. Please try again.';
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
            <li><a href="<?php echo $baseUrl; ?>/posts/create.php">Create Blog</a></li>
            <li><a href="<?php echo $baseUrl; ?>/posts/my_posts.php">My Blogs</a></li>
            <li><a href="<?php echo $baseUrl; ?>/auth/logout.php">Logout</a></li>
        </ul>
    </header>

    <!-- Main Content -->
    <main class="container" style="margin-top: 100px; padding: 20px;">
        <div class="create-post-container">
            <h1><i class='bx bx-edit'></i> Edit Blog Post</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="post-form">
                <div class="form-group">
                    <label for="title">
                        <i class='bx bx-heading'></i> Blog Title
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        required
                        value="<?php echo htmlspecialchars($_POST['title'] ?? $post['title']); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="content">
                        <i class='bx bx-file'></i> Blog Content
                    </label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($_POST['content'] ?? $post['content']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-check'></i> Update Blog
                    </button>
                    <a href="<?php echo $baseUrl; ?>/posts/view.php?id=<?php echo $postId; ?>" class="btn btn-secondary">
                        <i class='bx bx-x'></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Circle Blog. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const BASE_URL = '<?php echo $baseUrl; ?>';
    </script>
    <script src="<?php echo $baseUrl; ?>/assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new SimpleMDE({
                element: document.getElementById("content"),
                spellChecker: false,
                toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "link", "image", "|", "preview", "fullscreen"]
            });
        });
    </script>
</body>
</html>
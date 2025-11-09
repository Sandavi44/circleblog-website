<?php
/**
 * ========================================
 * MY BLOG POSTS PAGE
 * ========================================
 * Shows all posts by logged-in user
 */

define('INCLUDED', true);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Require login
requireLogin();

$pageTitle = 'My Blogs';
$baseUrl = getBaseUrl();

// Fetch user's posts
try {
    $stmt = $pdo->prepare("
        SELECT id, title, content, created_at 
        FROM posts 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    
    $stmt->execute([getUserId()]);
    $posts = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Fetch user posts error: " . $e->getMessage());
    $posts = [];
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
            <li><a href="<?php echo $baseUrl; ?>/posts/my_posts.php" class="active">My Blogs</a></li>
            <li><a href="<?php echo $baseUrl; ?>/auth/logout.php">Logout</a></li>
        </ul>
    </header>

    <!-- Main Content -->
    <main class="container" style="margin-top: 100px; padding: 20px;">
        <div class="my-posts-container">
            <div class="page-header">
                <h1><i class='bx bx-file-blank'></i> My Blog Posts</h1>
                <a href="<?php echo $baseUrl; ?>/posts/create.php" class="btn btn-primary">
                    <i class='bx bx-plus'></i> Create New Post
                </a>
            </div>
            
            <!-- Flash Messages -->
            <?php 
            $flash = getFlashMessage();
            if ($flash): 
            ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Posts List -->
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <i class='bx bx-book-open' style="font-size: 64px; opacity: 0.3;"></i>
                    <p>You haven't created any blog posts yet.</p>
                    <a href="<?php echo $baseUrl; ?>/posts/create.php" class="btn btn-primary">
                        <i class='bx bx-plus'></i> Create Your First Post
                    </a>
                </div>
            <?php else: ?>
                <div class="posts-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo $baseUrl; ?>/posts/view.php?id=<?php echo $post['id']; ?>" class="post-title-link">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                        <div class="post-excerpt">
                                            <?php echo excerpt($post['content'], 100); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo formatDate($post['created_at'], 'd M Y'); ?>
                                        <small class="text-muted"><?php echo timeAgo($post['created_at']); ?></small>
                                    </td>
                                    <td class="actions">
                                        <a href="<?php echo $baseUrl; ?>/posts/view.php?id=<?php echo $post['id']; ?>" 
                                           class="btn-icon" title="View">
                                            <i class='bx bx-show'></i>
                                        </a>
                                        <a href="<?php echo $baseUrl; ?>/posts/edit.php?id=<?php echo $post['id']; ?>" 
                                           class="btn-icon" title="Edit">
                                            <i class='bx bx-edit'></i>
                                        </a>
                                        <a href="<?php echo $baseUrl; ?>/posts/delete.php?id=<?php echo $post['id']; ?>" 
                                           class="btn-icon btn-delete" title="Delete"
                                           data-confirm-delete="Are you sure you want to delete '<?php echo htmlspecialchars($post['title']); ?>'?">
                                            <i class='bx bx-trash'></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="posts-summary">
                    <p>Total Posts: <strong><?php echo count($posts); ?></strong></p>
                </div>
            <?php endif; ?>
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
</body>
</html>
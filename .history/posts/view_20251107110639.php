<?php
// Start session to check user authentication
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit;<?php
/**
 * ========================================
 * VIEW SINGLE BLOG POST
 * ========================================
 */

define('INCLUDED', true);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

$baseUrl = getBaseUrl();

// Get post ID from URL
$postId = $_GET['id'] ?? 0;

if (!$postId) {
    setFlashMessage('error', 'Post not found');
    redirect('/index.php');
}

// Fetch post from database
try {
    $stmt = $pdo->prepare("
        SELECT 
            posts.id,
            posts.title,
            posts.content,
            posts.created_at,
            posts.user_id,
            users.username as author,
            users.email as author_email
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        WHERE posts.id = ?
    ");
    
    $stmt->execute([$postId]);
    $post = $stmt->fetch();
    
    if (!$post) {
        setFlashMessage('error', 'Post not found');
        redirect('/index.php');
    }
    
    $pageTitle = $post['title'];
    
} catch (PDOException $e) {
    error_log("View post error: " . $e->getMessage());
    setFlashMessage('error', 'Error loading post');
    redirect('/index.php');
}

// Simple Markdown to HTML converter
function markdownToHtml($text) {
    // Headers
    $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
    
    // Bold
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Italic
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
    
    // Links
    $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" target="_blank">$1</a>', $text);
    
    // Line breaks
    $text = nl2br($text);
    
    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Circle Blog</title>
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
            <?php if (isLoggedIn()): ?>
                <li><a href="<?php echo $baseUrl; ?>/posts/create.php">Create Blog</a></li>
                <li><a href="<?php echo $baseUrl; ?>/posts/my_posts.php">My Blogs</a></li>
                <li><a href="<?php echo $baseUrl; ?>/auth/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="#" id="nav-login">Log In</a></li>
                <li><a href="#" id="nav-signup">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </header>

    <!-- Main Content -->
    <main class="container" style="margin-top: 100px;">
        <article class="blog-post-single">
            <!-- Post Header -->
            <div class="post-header">
                <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="post-meta">
                    <span class="author">
                        <i class='bx bx-user'></i>
                        <?php echo htmlspecialchars($post['author']); ?>
                    </span>
                    <span class="date">
                        <i class='bx bx-time'></i>
                        <?php echo formatDate($post['created_at'], 'F j, Y'); ?>
                        (<?php echo timeAgo($post['created_at']); ?>)
                    </span>
                </div>
                
                <!-- Edit/Delete buttons (only for post owner) -->
                <?php if (isLoggedIn() && getUserId() == $post['user_id']): ?>
                    <div class="post-actions">
                        <a href="<?php echo $baseUrl; ?>/posts/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-edit">
                            <i class='bx bx-edit'></i> Edit
                        </a>
                        <a href="<?php echo $baseUrl; ?>/posts/delete.php?id=<?php echo $post['id']; ?>" 
                           class="btn btn-delete"
                           data-confirm-delete="Are you sure you want to delete this post?">
                            <i class='bx bx-trash'></i> Delete
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Post Content -->
            <div class="post-content">
                <?php echo markdownToHtml($post['content']); ?>
            </div>
            
            <!-- Back Button -->
            <div class="post-footer">
                <a href="<?php echo $baseUrl; ?>/index.php" class="btn btn-secondary">
                    <i class='bx bx-arrow-back'></i> Back to Home
                </a>
            </div>
        </article>
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
}

include('db.php');  // Include the database connection file

// Get the post ID from the URL (via GET)
$post_id = $_GET['id'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link rel="stylesheet" href="view_post.css">  
</head>
<body>

<?php
if ($post_id) {
    // Fetch the post details based on the ID
    $query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Wrap the post content in a container
        echo "<div class='post-container'>";
        
        // Display the full post
        echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";
        echo "<p><strong>Published on:</strong> " . $post['created_at'] . "</p>";
        
        // Display the content
        echo "<div class='content'>" . nl2br(htmlspecialchars($post['content'])) . "</div>";

        // Display image if it exists
        if ($post['image']) {
            echo "<img src='" . $post['image'] . "' alt='Post Image' />";
        }

        // Close the post container
        echo "</div>"; 
    } else {
        echo "<p>Post not found or you do not have permission to view this post.</p>";
    }
} else {
    echo "<p>Invalid post ID.</p>";
}
?>

</body>
</html>

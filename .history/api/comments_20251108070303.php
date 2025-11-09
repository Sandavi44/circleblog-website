<?php
/**
 * ========================================
 * MY BLOG POSTS PAGE
 * ========================================
 * 
 * FEATURES:
 * - Display all posts by current user
 * - Quick edit/delete buttons
 * - Show likes and comments count
 * - Empty state with "Create First Post" CTA
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// SECURITY: Require login
requireLogin();

// ========================================
// FETCH USER'S POSTS FROM DATABASE
// ========================================
$userId = getCurrentUserId();

try {
    $stmt = $pdo->prepare("
        SELECT 
            posts.id,
            posts.title,
            posts.content,
            posts.image,
            posts.created_at,
            posts.updated_at,
            (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count
        FROM posts
        WHERE posts.user_id = ?
        ORDER BY posts.created_at DESC
    ");
    
    $stmt->execute([$userId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $posts = [];
    error_log("Error fetching user posts: " . $e->getMessage());
}

// Get flash message
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Blogs | Circle Blog</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link href='https://unpkg.com/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

  <!-- ========================================
       NAVIGATION BAR
       ======================================== -->
  <header>
    <a href="../index.php" class="logo">
      <img src="../assets/images/logo-2441841.svg" alt="Circle Blog Logo">
    </a>
    <div class="blog-name-circle">Circle Blog</div>

    <div class="icons">
      <i class='bx bx-menu' id="menu-icon"></i>
    </div>

    <ul class="navbar" id="navbar">
      <li><a href="../index.php">Home</a></li>
      <li><a href="create.php">Create Blog</a></li>
      <li><a href="my_posts.php">My Blogs</a></li>
      <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
  </header>

  <!-- ========================================
       FLASH MESSAGE
       ======================================== -->
  <?php if ($flash): ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>">
      <?php echo sanitize($flash['message']); ?>
    </div>
  <?php endif; ?>

  <!-- ========================================
       MY POSTS SECTION
       ======================================== -->
  <section class="my-posts-section">
    <div class="container">
      
      <div class="section-header">
        <h1>My Blog Posts</h1>
        <a href="create.php" class="btn-primary">
          <i class='bx bx-plus'></i> Create New Post
        </a>
      </div>

      <?php if (count($posts) > 0): ?>
        <!-- Posts Table -->
        <div class="posts-table">
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th>Created</th>
                <th>Stats</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($posts as $post): ?>
                <tr>
                  <!-- Post Title -->
                  <td>
                    <div class="post-title-cell">
                      <?php if (!empty($post['image'])): ?>
                        <img src="../<?php echo sanitize($post['image']); ?>" alt="Thumbnail" class="post-thumbnail">
                      <?php endif; ?>
                      <div>
                        <a href="view.php?id=<?php echo $post['id']; ?>" class="post-link">
                          <?php echo sanitize($post['title']); ?>
                        </a>
                        <small class="post-excerpt">
                          <?php echo truncateText(sanitize($post['content']), 80); ?>
                        </small>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Created Date -->
                  <td>
                    <div class="date-info">
                      <?php echo timeAgo($post['created_at']); ?>
                      <?php if ($post['created_at'] !== $post['updated_at']): ?>
                        <small class="updated">Updated: <?php echo timeAgo($post['updated_at']); ?></small>
                      <?php endif; ?>
                    </div>
                  </td>
                  
                  <!-- Statistics -->
                  <td>
                    <div class="post-stats">
                      <span title="Likes">❤️ <?php echo $post['like_count']; ?></span>
                      <span title="Comments">💬 <?php echo $post['comment_count']; ?></span>
                    </div>
                  </td>
                  
                  <!-- Actions -->
                  <td>
                    <div class="post-actions">
                      <a href="view.php?id=<?php echo $post['id']; ?>" class="btn-icon" title="View">
                        <i class='bx bx-show'></i>
                      </a>
                      <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn-icon" title="Edit">
                        <i class='bx bx-edit'></i>
                      </a>
                      <button onclick="confirmDelete(<?php echo $post['id']; ?>)" class="btn-icon btn-delete" title="Delete">
                        <i class='bx bx-trash'></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
          <i class='bx bx-edit' style="font-size: 80px; color: #ccc;"></i>
          <h2>No Posts Yet</h2>
          <p>Start sharing your thoughts with the world!</p>
          <a href="create.php" class="btn-primary">Create Your First Post</a>
        </div>
      <?php endif; ?>

    </div>
  </section>

  <!-- ========================================
       JAVASCRIPT
       ======================================== -->
  <script>
    // Mobile menu toggle
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.getElementById('navbar');
    if (menuIcon && navbar) {
      menuIcon.addEventListener('click', () => {
        navbar.classList.toggle('active');
      });
    }

    // Delete confirmation
    function confirmDelete(postId) {
      if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        window.location.href = 'delete.php?id=' + postId;
      }
    }
  </script>

</body>
</html>
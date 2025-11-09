<?php
/**
 * ========================================
 * SEARCH RESULTS PAGE
 * ========================================
 * 
 * Searches blog posts by:
 * - Title
 * - Content
 * - Author username
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Get search query from URL
$searchQuery = trim($_GET['q'] ?? '');

$posts = [];
$searchPerformed = false;

// Only search if query is not empty
if (!empty($searchQuery)) {
    $searchPerformed = true;
    
    try {
        // Prepare search query with wildcards
        $searchTerm = "%$searchQuery%";
        
        // Search in title, content, and author username
        $stmt = $pdo->prepare("
            SELECT 
                posts.id,
                posts.title,
                posts.content,
                posts.image,
                posts.created_at,
                users.username AS author,
                (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
                (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
            FROM posts
            INNER JOIN users ON posts.user_id = users.id
            WHERE posts.title LIKE ? 
               OR posts.content LIKE ?
               OR users.username LIKE ?
            ORDER BY posts.created_at DESC
        ");
        
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Search error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Results | Circle Blog</title>
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
      <i class='bx bx-search' id="search-icon"></i>
      <i class='bx bx-menu' id="menu-icon"></i>
    </div>

    <ul class="navbar" id="navbar">
      <li><a href="../index.php">Home</a></li>
      <?php if (isLoggedIn()): ?>
        <li><a href="create.php">Create Blog</a></li>
        <li><a href="my_posts.php">My Blogs</a></li>
        <li><a href="../auth/logout.php">Logout</a></li>
      <?php else: ?>
        <li><a href="../index.php#login">Log In</a></li>
        <li><a href="../index.php#signup">Sign Up</a></li>
      <?php endif; ?>
    </ul>
  </header>

  <!-- ========================================
       SEARCH BOX
       ======================================== -->
  <div class="search-box active" id="search-box">
    <form action="search.php" method="GET">
      <input 
        type="search" 
        name="q" 
        placeholder="Search blogs..." 
        value="<?php echo sanitize($searchQuery); ?>"
        autofocus
      />
      <button type="submit"><i class='bx bx-search'></i></button>
    </form>
  </div>

  <!-- ========================================
       SEARCH RESULTS
       ======================================== -->
  <section class="featured-blogs">
    <div class="container">
      
      <?php if (!$searchPerformed): ?>
        <!-- No search performed yet -->
        <div class="search-prompt">
          <i class='bx bx-search-alt' style="font-size: 80px; color: #ccc;"></i>
          <h2>Search for Blog Posts</h2>
          <p>Enter keywords in the search box above to find posts.</p>
        </div>
      
      <?php elseif (count($posts) > 0): ?>
        <!-- Search results found -->
        <h2>Search Results for "<?php echo sanitize($searchQuery); ?>"</h2>
        <p class="search-count">Found <?php echo count($posts); ?> result(s)</p>
        
        <div class="blog-grid">
          <?php foreach ($posts as $post): ?>
            <div class="blog-card">
              
              <!-- Post Image -->
              <?php if (!empty($post['image'])): ?>
                <img src="../<?php echo sanitize($post['image']); ?>" alt="<?php echo sanitize($post['title']); ?>">
              <?php else: ?>
                <img src="../assets/images/default-blog.jpg" alt="Default Blog Image">
              <?php endif; ?>
              
              <!-- Post Title (highlight search term) -->
              <h3>
                <?php 
                  // Highlight search term in title
                  $highlightedTitle = str_ireplace(
                      $searchQuery, 
                      '<mark>' . $searchQuery . '</mark>', 
                      sanitize($post['title'])
                  );
                  echo $highlightedTitle;
                ?>
              </h3>
              
              <!-- Post Excerpt -->
              <p><?php echo truncateText(sanitize($post['content']), 150); ?></p>
              
              <!-- Post Meta -->
              <div class="post-meta">
                <span class="author">By <?php echo sanitize($post['author']); ?></span>
                <span class="date"><?php echo timeAgo($post['created_at']); ?></span>
                <span class="likes">❤️ <?php echo $post['like_count']; ?></span>
                <span class="comments">💬 <?php echo $post['comment_count']; ?></span>
              </div>
              
              <!-- Read More Link -->
              <a href="view.php?id=<?php echo $post['id']; ?>" class="read-more">Read More</a>
            </div>
          <?php endforeach; ?>
        </div>
      
      <?php else: ?>
        <!-- No results found -->
        <div class="no-results">
          <i class='bx bx-sad' style="font-size: 80px; color: #ccc;"></i>
          <h2>No Results Found</h2>
          <p>We couldn't find any posts matching "<strong><?php echo sanitize($searchQuery); ?></strong>"</p>
          <p>Try different keywords or <a href="../index.php">browse all posts</a></p>
        </div>
      <?php endif; ?>
      
    </div>
  </section>

  <!-- ========================================
       JAVASCRIPT
       ======================================== -->
  <script src="../assets/js/main.js"></script>

</body>
</html>
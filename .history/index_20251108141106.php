<?php
/**
 * ========================================
 * HOMEPAGE - Circle Blog
 * ========================================
 * 
 * FEATURES:
 * - Dynamic navbar (changes based on login status)
 * - Display all blog posts from database
 * - Login/Signup modal
 * - Responsive design
 * ========================================
 */

// Include required files
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';

// ========================================
// FETCH ALL BLOG POSTS FROM DATABASE
// ========================================
try {
    // SQL query to get posts with author info and like count
    $stmt = $pdo->query("
        SELECT 
            posts.id,
            posts.title,
            posts.content,
            posts.image,
            posts.created_at,
            users.username AS author,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC
    ");
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $posts = [];
    error_log("Error fetching posts: " . $e->getMessage());
}

// Get flash message if any
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Circle Blog | Home</title>
  
  <!-- CSS Files -->
  <link rel="stylesheet" href="assets/css/style.css" />
  
  <!-- Boxicons for icons -->
  <link href='https://unpkg.com/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

  <!-- ========================================
       NAVIGATION BAR (Dynamic based on login)
       ======================================== -->
  <header>
    <a href="index.php" class="logo">
      <img src="assets/images/logo-2441841.svg" alt="Circle Blog Logo">
    </a>
    <div class="blog-name-circle">Circle Blog</div>

    <!-- Icons for search and mobile menu -->
    <div class="icons">
      <i class='bx bx-search' id="search-icon"></i>
      <i class='bx bx-menu' id="menu-icon"></i>
    </div>

    <!-- Navigation Links -->
    <ul class="navbar" id="navbar">
      <li><a href="index.php">Home</a></li>
      
      <?php if (isLoggedIn()): ?>
        <!-- Links for LOGGED IN users -->
        <li><a href="posts/create.php">Create Blog</a></li>
        <li><a href="posts/my_posts.php">My Blogs</a></li>
        <li><a href="auth/logout.php">Logout</a></li>
        <li><span style="color: #fff;">Hello, <?php echo sanitize(getCurrentUsername()); ?>!</span></li>
      <?php else: ?>
        <!-- Links for LOGGED OUT users -->
        <li><a href="#" id="nav-login">Log In</a></li>
        <li><a href="#" id="nav-signup">Sign Up</a></li>
      <?php endif; ?>
    </ul>
  </header>

  <!-- ========================================
       SEARCH BOX
       ======================================== -->
  <div class="search-box" id="search-box">
    <form action="posts/search.php" method="GET">
      <input type="search" name="q" placeholder="Search blogs..." />
      <button type="submit"><i class='bx bx-search'></i></button>
    </form>
  </div>

  <!-- ========================================
       FLASH MESSAGE (Success/Error notifications)
       ======================================== -->
  <?php if ($flash): ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>">
      <?php echo sanitize($flash['message']); ?>
    </div>
  <?php endif; ?>

  <!-- ========================================
       HERO SECTION
       ======================================== -->
  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to Circle Blog</h1>
      <p>Where ideas melt into stories ❄️</p>
      <?php if (!isLoggedIn()): ?>
        <button class="cta-btn" id="cta-signup">Start Writing</button>
      <?php endif; ?>
    </div>
  </section>

  <!-- ========================================
       BLOG POSTS SECTION (Dynamic from database)
       ======================================== -->
  <section class="featured-blogs">
    <h2>Latest Stories</h2>
    
    <?php if (count($posts) > 0): ?>
      <div class="blog-grid">
        <?php foreach ($posts as $post): ?>
          <div class="blog-card">
            
            <!-- Post Image (if exists) -->
            <?php if (!empty($post['image'])): ?>
              <img src="<?php echo sanitize($post['image']); ?>" alt="<?php echo sanitize($post['title']); ?>">
            <?php else: ?>
              <img src="assets/images/default-blog.jpg" alt="Default Blog Image">
            <?php endif; ?>
            
            <!-- Post Title -->
            <h3><?php echo sanitize($post['title']); ?></h3>
            
            <!-- Post Excerpt (first 150 characters) -->
            <p><?php echo truncateText(sanitize($post['content']), 150); ?></p>
            
            <!-- Post Meta Info -->
            <div class="post-meta">
              <span class="author">By <?php echo sanitize($post['author']); ?></span>
              <span class="date"><?php echo timeAgo($post['created_at']); ?></span>
              <span class="likes">❤️ <?php echo $post['like_count']; ?></span>
            </div>
            
            <!-- Read More Link -->
            <a href="posts/view.php?id=<?php echo $post['id']; ?>" class="read-more">Read More</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <!-- No posts found -->
      <div class="no-posts">
        <p>No blog posts yet. Be the first to write one!</p>
        <?php if (isLoggedIn()): ?>
          <a href="posts/create.php" class="btn-primary">Create First Post</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- ========================================
       LOGIN/SIGNUP MODAL (Only for logged out users)
       ======================================== -->
  <?php if (!isLoggedIn()): ?>
  <div id="authModal" class="modal">
    <div class="modal-content auth-container">
      <span class="close">&times;</span>
      <h2 id="modalTitle">Welcome Back!</h2>
      <form id="authForm"></form>
    </div>
  </div>
  <?php endif; ?>

  <!-- ========================================
       JAVASCRIPT
       ======================================== -->
  <script src="assets/js/main.js"></script>

  <!-- ========================================
       FOOTER
       ======================================== -->
  <?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
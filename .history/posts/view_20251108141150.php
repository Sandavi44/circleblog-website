<?php
/**
 * ========================================
 * VIEW SINGLE BLOG POST
 * ========================================
 * 
 * FEATURES:
 * - Display full post content
 * - Show author and date
 * - Like button with count
 * - Comments section
 * - Edit/Delete buttons (only for owner)
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// ========================================
// GET POST ID FROM URL
// ========================================
$postId = $_GET['id'] ?? null;

if (!$postId || !is_numeric($postId)) {
    setFlashMessage('error', 'Invalid post ID');
    redirect('../index.php');
}

// ========================================
// FETCH POST FROM DATABASE
// ========================================
try {
    $stmt = $pdo->prepare("
        SELECT 
            posts.*,
            users.username AS author,
            (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        WHERE posts.id = ?
    ");
    
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        setFlashMessage('error', 'Post not found');
        redirect('../index.php');
    }
    
} catch (PDOException $e) {
    error_log("Error fetching post: " . $e->getMessage());
    setFlashMessage('error', 'Error loading post');
    redirect('../index.php');
}

// ========================================
// CHECK IF CURRENT USER LIKED THIS POST
// ========================================
$userLiked = false;
if (isLoggedIn()) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, getCurrentUserId()]);
        $userLiked = $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Error checking like status: " . $e->getMessage());
    }
}

// ========================================
// FETCH COMMENTS FOR THIS POST
// ========================================
try {
    $stmt = $pdo->prepare("
        SELECT 
            comments.*,
            users.username
        FROM comments
        INNER JOIN users ON comments.user_id = users.id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at DESC
    ");
    
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $comments = [];
    error_log("Error fetching comments: " . $e->getMessage());
}

// Get flash message
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo sanitize($post['title']); ?> | Circle Blog</title>
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
       FLASH MESSAGE
       ======================================== -->
  <?php if ($flash): ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>">
      <?php echo sanitize($flash['message']); ?>
    </div>
  <?php endif; ?>

  <!-- ========================================
       POST CONTENT
       ======================================== -->
  <article class="post-single">
    <div class="container">
      
      <!-- Post Header -->
      <header class="post-header">
        <h1><?php echo sanitize($post['title']); ?></h1>
        
        <div class="post-header-meta">
          <div class="post-meta">
            <span class="author">By <?php echo sanitize($post['author']); ?></span>
            <span class="date"><?php echo formatDate($post['created_at']); ?></span>
            <?php if ($post['created_at'] !== $post['updated_at']): ?>
              <span class="updated">(Updated: <?php echo timeAgo($post['updated_at']); ?>)</span>
            <?php endif; ?>
          </div>

          <!-- Edit/Delete buttons (only for owner) -->
          <?php if (isOwner($post['user_id'])): ?>
            <div class="post-actions">
              <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn-edit">
                <i class='bx bx-edit'></i> Edit
              </a>
              <button onclick="confirmDelete(<?php echo $post['id']; ?>)" class="btn-delete">
                <i class='bx bx-trash'></i> Delete
              </button>
            </div>
          <?php endif; ?>
        </div>
      </header>

      <!-- Featured Image -->
      <?php if (!empty($post['image'])): ?>
        <div class="post-image">
          <img src="../<?php echo sanitize($post['image']); ?>" alt="<?php echo sanitize($post['title']); ?>">
        </div>
      <?php endif; ?>

      <!-- Post Content (with Markdown support) -->
      <div class="post-content">
        <?php echo markdownToHtml(sanitize($post['content'])); ?>
      </div>

      <!-- Like and Comment Count -->
      <div class="post-engagement">
        <button id="like-btn" class="like-btn <?php echo $userLiked ? 'liked' : ''; ?>" 
                data-post-id="<?php echo $post['id']; ?>"
                <?php echo !isLoggedIn() ? 'disabled title="Login to like"' : ''; ?>>
          <i class='bx <?php echo $userLiked ? 'bxs-heart' : 'bx-heart'; ?>'></i>
          <span id="like-count"><?php echo $post['like_count']; ?></span>
        </button>
        
        <span class="comment-count">
          <i class='bx bx-comment'></i> <?php echo $post['comment_count']; ?> Comments
        </span>
      </div>

      <!-- ========================================
           COMMENTS SECTION
           ======================================== -->
      <section class="comments-section">
        <h2>Comments (<?php echo count($comments); ?>)</h2>

        <!-- Add Comment Form (only for logged-in users) -->
        <?php if (isLoggedIn()): ?>
          <form id="comment-form" class="comment-form">
            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
            <textarea 
              name="content" 
              placeholder="Write a comment..." 
              rows="3" 
              required
            ></textarea>
            <button type="submit" class="btn-primary">Post Comment</button>
          </form>
        <?php else: ?>
          <p class="login-prompt">Please <a href="../index.php#login">login</a> to comment.</p>
        <?php endif; ?>

        <!-- Display Comments -->
        <div id="comments-list" class="comments-list">
          <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
              <div class="comment" data-comment-id="<?php echo $comment['id']; ?>">
                <div class="comment-header">
                  <strong><?php echo sanitize($comment['username']); ?></strong>
                  <span class="comment-date"><?php echo timeAgo($comment['created_at']); ?></span>
                  
                  <!-- Delete button (only for comment owner) -->
                  <?php if (isOwner($comment['user_id'])): ?>
                    <button onclick="deleteComment(<?php echo $comment['id']; ?>)" class="btn-delete-small">
                      <i class='bx bx-trash'></i>
                    </button>
                  <?php endif; ?>
                </div>
                <p class="comment-content"><?php echo nl2br(sanitize($comment['content'])); ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="no-comments">No comments yet. Be the first to comment!</p>
          <?php endif; ?>
        </div>
      </section>

    </div>
  </article>

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

    // ========================================
    // LIKE/UNLIKE FUNCTIONALITY
    // ========================================
    const likeBtn = document.getElementById('like-btn');
    if (likeBtn && !likeBtn.disabled) {
      likeBtn.addEventListener('click', async function() {
        const postId = this.dataset.postId;
        
        try {
          const response = await fetch('../api/reactions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
          });
          
          const data = await response.json();
          
          if (data.success) {
            // Update UI
            this.classList.toggle('liked');
            const icon = this.querySelector('i');
            icon.className = data.liked ? 'bx bxs-heart' : 'bx bx-heart';
            document.getElementById('like-count').textContent = data.like_count;
          } else {
            alert(data.message);
          }
        } catch (error) {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
        }
      });
    }

    // ========================================
    // ADD COMMENT FUNCTIONALITY
    // ========================================
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
      commentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
          post_id: formData.get('post_id'),
          content: formData.get('content')
        };
        
        // Disable submit button
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Posting...';
        
        try {
          const response = await fetch('../api/comments.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
          });
          
          const result = await response.json();
          
          console.log('Comment result:', result); // Debug log
          
          if (result.success) {
            // Success: Reload page to show new comment
            alert('Comment posted successfully!');
            location.reload();
          } else {
            // Show specific error message
            alert('Error: ' + result.message);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Network error: ' + error.message);
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      });
    }

    // ========================================
    // DELETE COMMENT FUNCTIONALITY
    // ========================================
    function deleteComment(commentId) {
      if (!confirm('Are you sure you want to delete this comment?')) {
        return;
      }
      
      fetch('../api/comments.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ comment_id: commentId })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      });
    }

    // ========================================
    // DELETE POST FUNCTIONALITY
    // ========================================
    function confirmDelete(postId) {
      if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
        window.location.href = 'delete.php?id=' + postId;
      }
    }
  </script>

</body>
</html>
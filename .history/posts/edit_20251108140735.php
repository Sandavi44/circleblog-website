<?php
/**
 * ========================================
 * EDIT BLOG POST PAGE
 * ========================================
 * 
 * FEATURES:
 * - Pre-filled form with existing post data
 * - Only post owner can edit
 * - Can update title, content, and image
 * - Submits to update.php
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// SECURITY: Require login
requireLogin();

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
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        setFlashMessage('error', 'Post not found');
        redirect('../index.php');
    }
    
    // SECURITY: Check if current user owns this post
    if (!isOwner($post['user_id'])) {
        setFlashMessage('error', 'You do not have permission to edit this post');
        redirect("view.php?id=$postId");
    }
    
} catch (PDOException $e) {
    error_log("Error fetching post: " . $e->getMessage());
    setFlashMessage('error', 'Error loading post');
    redirect('../index.php');
}

// Get flash message
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Post | Circle Blog</title>
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
      <!-- Back button to previous page -->
      <a href="view.php?id=<?php echo $post['id']; ?>" class="back-btn" title="Back to Post">
        <i class='bx bx-arrow-back'></i>
      </a>
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
       EDIT POST FORM
       ======================================== -->
  <section class="create-post-section">
    <div class="container">
      <h1>Edit Blog Post</h1>
      <p class="subtitle">Update your story ✏️</p>

      <form action="update.php" method="POST" enctype="multipart/form-data" class="post-form">
        
        <!-- Hidden fields -->
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <input type="hidden" name="existing_image" value="<?php echo sanitize($post['image']); ?>">

        <!-- Title Field (pre-filled) -->
        <div class="form-group">
          <label for="title">Post Title <span class="required">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            value="<?php echo sanitize($post['title']); ?>"
            required 
            maxlength="255"
          >
        </div>

        <!-- Content Field (pre-filled) -->
        <div class="form-group">
          <label for="content">Content <span class="required">*</span></label>
          <textarea 
            id="content" 
            name="content" 
            rows="15" 
            required
          ><?php echo sanitize($post['content']); ?></textarea>
          <small>Markdown supported</small>
        </div>

        <!-- Current Image Display -->
        <?php if (!empty($post['image'])): ?>
          <div class="form-group">
            <label>Current Image:</label>
            <div class="current-image">
              <img src="../<?php echo sanitize($post['image']); ?>" alt="Current image" style="max-width: 300px;">
              <label>
                <input type="checkbox" name="remove_image" value="1">
                Remove this image
              </label>
            </div>
          </div>
        <?php endif; ?>

        <!-- New Image Upload -->
        <div class="form-group">
          <label for="image">Upload New Image (Optional)</label>
          <input 
            type="file" 
            id="image" 
            name="image" 
            accept="image/jpeg,image/png,image/gif,image/webp"
          >
          <small>Leave empty to keep current image</small>
          
          <!-- Image Preview -->
          <div id="image-preview" style="display: none;">
            <img id="preview-img" src="" alt="Preview" style="max-width: 300px; margin-top: 10px;">
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="form-actions">
          <button type="submit" class="btn-primary">
            <i class='bx bx-save'></i> Update Post
          </button>
          <a href="view.php?id=<?php echo $post['id']; ?>" class="btn-secondary">Cancel</a>
        </div>

      </form>
    </div>
  </section>

  <!-- ========================================
       JAVASCRIPT
       ======================================== -->
  <script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
      const file = e.target.files[0];
      
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('preview-img').src = e.target.result;
          document.getElementById('image-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        document.getElementById('image-preview').style.display = 'none';
      }
    });

    // Mobile menu toggle
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.getElementById('navbar');
    if (menuIcon && navbar) {
      menuIcon.addEventListener('click', () => {
        navbar.classList.toggle('active');
      });
    }
  </script>

</body>
</html>
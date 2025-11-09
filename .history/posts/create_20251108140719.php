<?php
/**
 * ========================================
 * CREATE BLOG POST PAGE
 * ========================================
 * 
 * FEATURES:
 * - Form to create new blog post
 * - Title, content (Markdown support), image upload
 * - Only logged-in users can access
 * - Form submits to publish.php
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// SECURITY: Require user to be logged in
requireLogin();

// Get flash message if any
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create New Post | Circle Blog</title>
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
      <!-- Back button for easy navigation -->
      <a href="../index.php" class="back-btn" title="Back to Home">
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
       CREATE POST FORM
       ======================================== -->
  <section class="create-post-section">
    <div class="container">
      <h1>Create New Blog Post</h1>
      <p class="subtitle">Share your thoughts with the world ✍️</p>

      <form action="publish.php" method="POST" enctype="multipart/form-data" class="post-form">
        
        <!-- CSRF Token for security -->
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <!-- Title Field -->
        <div class="form-group">
          <label for="title">Post Title <span class="required">*</span></label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            placeholder="Enter an engaging title..." 
            required 
            maxlength="255"
          >
          <small>Maximum 255 characters</small>
        </div>

        <!-- Content Field (Markdown support) -->
        <div class="form-group">
          <label for="content">Content <span class="required">*</span></label>
          <textarea 
            id="content" 
            name="content" 
            rows="15" 
            placeholder="Write your story here... 

You can use Markdown:
# Heading 1
## Heading 2
**bold text**
*italic text*
[link text](url)" 
            required
          ></textarea>
          <small>Markdown supported. Write naturally!</small>
        </div>

        <!-- Image Upload Field -->
        <div class="form-group">
          <label for="image">Featured Image (Optional)</label>
          <input 
            type="file" 
            id="image" 
            name="image" 
            accept="image/jpeg,image/png,image/gif,image/webp"
          >
          <small>Max size: 5MB. Formats: JPG, PNG, GIF, WebP</small>
          
          <!-- Image Preview -->
          <div id="image-preview" style="display: none;">
            <img id="preview-img" src="" alt="Preview" style="max-width: 300px; margin-top: 10px;">
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="form-actions">
          <button type="submit" class="btn-primary">
            <i class='bx bx-send'></i> Publish Post
          </button>
          <a href="../index.php" class="btn-secondary">Cancel</a>
        </div>

      </form>
    </div>
  </section>

  <!-- ========================================
       JAVASCRIPT: Image Preview
       ======================================== -->
  <script>
    // Show image preview when file is selected
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
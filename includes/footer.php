<!-- ========================================
     FOOTER
     ======================================== -->
<footer class="site-footer">
  <div class="footer-container">
    
    <!-- Column 1: About -->
    <div class="footer-column">
      <h3>About Circle Blog</h3>
      <p>A platform for sharing ideas, stories, and thoughts. Join our community of writers and readers!</p>
      <div class="social-links">
        <a href="#" title="Facebook"><i class='bx bxl-facebook-circle'></i></a>
        <a href="#" title="Twitter"><i class='bx bxl-twitter'></i></a>
        <a href="#" title="Instagram"><i class='bx bxl-instagram'></i></a>
        <a href="#" title="LinkedIn"><i class='bx bxl-linkedin-square'></i></a>
      </div>
    </div>

    <!-- Column 2: Quick Links -->
    <div class="footer-column">
      <h3>Quick Links</h3>
      <ul class="footer-links">
        <li><a href="<?php echo getBaseUrl(); ?>/index.php">Home</a></li>
        <?php if (isLoggedIn()): ?>
          <li><a href="<?php echo getBaseUrl(); ?>/posts/create.php">Create Blog</a></li>
          <li><a href="<?php echo getBaseUrl(); ?>/posts/my_posts.php">My Blogs</a></li>
        <?php else: ?>
          <li><a href="<?php echo getBaseUrl(); ?>/index.php#login">Login</a></li>
          <li><a href="<?php echo getBaseUrl(); ?>/index.php#signup">Sign Up</a></li>
        <?php endif; ?>
        <li><a href="<?php echo getBaseUrl(); ?>/posts/search.php">Search</a></li>
      </ul>
    </div>

    <!-- Column 3: Contact -->
    <div class="footer-column">
      <h3>Contact</h3>
      <ul class="footer-contact">
        <li><i class='bx bx-envelope'></i> info@circleblog.com</li>
        <li><i class='bx bx-phone'></i> +94 11 234 5678</li>
        <li><i class='bx bx-map'></i> Colombo, Sri Lanka</li>
      </ul>
    </div>

  </div>

  <!-- Copyright -->
  <div class="footer-bottom">
    <p>&copy; <?php echo date('Y'); ?> Circle Blog. All rights reserved. | Built for IN2120 Web Programming</p>
  </div>
</footer>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Circle|Home</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="view_post.css">

  <link href='https://unpkg.com/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <!-- Navbar -->
  <header>
    <a href="index.html" class="logo">
      <img src="images/logo-2441841.svg" alt="logo">
    </a>
    <div class="blog-name-circle">Circle Blog</div>

    <!-- ✅ Wrapped icons in a flex container for layout -->
    <div class="icons">
      <i class='bx bx-search' id="search-icon"></i>
      <i class='bx bx-menu' id="menu-icon"></i>
    </div>

    <!-- Navigation Links -->
    <ul class="navbar" id="navbar">
      <li><a href="index.html">Home</a></li>
      <li><a href="#" id="nav-login">Log In</a></li>
      <li><a href="#" id="nav-signup">Sign Up</a></li>
      <li><a href="create_blog.php">Create Blog</a></li>
      <li><a href="my_posts.php">My Blogs</a></li>
      <li><a href="view_blog.php">View Blog</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </header>

  <!-- Search bar -->
  <div class="search-box" id="search-box">
    <form>
      <input type="search" placeholder="Search here..." />
      <button type="submit"><i class='bx bx-search'></i></button>
    </form>
  </div>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to Circle Blog</h1>
      <p>Where ideas melt into stories ❄️</p>
    </div>
  </section>

  <!-- Featured Blogs -->
  <section class="featured-blogs">
    <h2>Trending Reads</h2>
    <div class="blog-grid">
      <!-- Blog cards -->
      <div class="blog-card">
        <img src="images/blog1.jpg" alt="Blog 1">
        <h3>How to Build a Blog in 7 Steps</h3>
        <p>Learn the essentials of setting up a blog that stands out.</p>
        <a href="blog1.html">Read More</a>
      </div>
      <div class="blog-card">
        <img src="images/blog2.jpg" alt="Blog 2">
        <h3>Design Tips for Better UX</h3>
        <p>Discover how small UI tweaks can boost user engagement.</p>
        <a href="blog2.html">Read More</a>
      </div>
      <div class="blog-card">
        <img src="images/blog3.jpg" alt="Blog 3">
        <h3>Why Blogging Still Matters</h3>
        <p>Explore the power of personal voice in the digital age.</p>
        <a href="blog3.html">Read More</a>
      </div>
    </div>
  </section>

 <!-- ✅ Login/Signup Modal (place this before </body>) -->
<div id="authModal" class="modal">
  <div class="modal-content auth-container">
    <!-- ❌ Close button -->
    <span class="close">&times;</span>

    <!-- 🔤 Modal Title (changes between Login and Signup) -->
    <h2 id="modalTitle">Welcome Back!</h2>

    <!-- 🧾 Form container (JS will inject login/signup fields here) -->
    <form id="authForm"></form>
  </div>
</div>

<!-- ✅ Link your external JS file -->
<script src="script.js"></script>

</body>
</html>


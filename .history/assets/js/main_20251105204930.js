document.addEventListener('DOMContentLoaded', function () {
  // Navbar toggle
  const menuIcon = document.getElementById('menu-icon');
  const navbar = document.getElementById('navbar');
  if (menuIcon && navbar) {
    menuIcon.addEventListener('click', () => {
      navbar.classList.toggle('active');
    });
  }

  // Search box toggle
  const searchIcon = document.getElementById('search-icon');
  const searchBox = document.getElementById('search-box');
  if (searchIcon && searchBox) {
    searchIcon.addEventListener('click', () => {
      searchBox.classList.toggle('active');
    });
  }

  // Modal elements
  const modal = document.getElementById("authModal");
  const closeBtn = document.querySelector(".close");
  const modalTitle = document.getElementById("modalTitle");
  const authForm = document.getElementById("authForm");

  // Show login modal
  const loginNav = document.getElementById('nav-login');
  if (loginNav) {
    loginNav.addEventListener('click', (e) => {
      e.preventDefault();
      showLoginForm();
    });
  }

  // Show signup modal
  const signupNav = document.getElementById('nav-signup');
  if (signupNav) {
    signupNav.addEventListener('click', (e) => {
      e.preventDefault();
      showSignupForm();
    });
  }

  // Blog card click triggers login modal
  document.querySelectorAll('.blog-card a').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      showLoginForm();
    });
  });

  // Close modal
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });
  }

  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });

  // Toggle between login and signup
  document.addEventListener("click", (e) => {
    if (e.target && e.target.id === "toggleLink") {
      e.preventDefault();
      const isLogin = modalTitle.textContent.includes("Welcome");
      if (isLogin) {
        showSignupForm();
      } else {
        showLoginForm();
      }
    }
  });

  // Show login form
  function showLoginForm() {
    modalTitle.textContent = "Welcome Back!";
    authForm.innerHTML = `
      <input type="text" id="login-username" placeholder="Username" required />
      <input type="password" id="login-password" placeholder="Password" required />
      <button type="button" id="login-btn">Login</button>
      <p class="toggle-link">Don't have an account? <a id="toggleLink">Sign up</a></p>
    `;
    modal.style.display = "block";
    attachLoginHandler();
  }

  // Show signup form
  function showSignupForm() {
    modalTitle.textContent = "Create Your Account";
    authForm.innerHTML = `
      <input type="text" id="signup-username" placeholder="Username" required />
      <input type="email" id="signup-email" placeholder="Email" required />
      <input type="password" id="signup-password" placeholder="Password" required />
      <button type="button" id="signup-btn">Sign Up</button>
      <p class="toggle-link">Already have an account? <a id="toggleLink">Login</a></p>
    `;
    modal.style.display = "block";
    attachSignupHandler();
  }

  // Login handler
  function attachLoginHandler() {
    const loginBtn = document.getElementById('login-btn');
    if (loginBtn) {
      loginBtn.addEventListener('click', function () {
        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value.trim();

        if (!username || !password) {
          alert('Please enter both username and password.');
          return;
        }

        fetch('login.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ username, password })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert(`Welcome back, ${username}!`);
            modal.style.display = "none";
            window.location.href = 'index.html';
          } else {
            alert("Login failed: " + data.message);
          }
        })
        .catch(error => {
          console.error("Login error:", error);
          alert("Something went wrong. Please try again.");
        });
      });
    }
  }

  // Signup handler
  function attachSignupHandler() {
    const signupBtn = document.getElementById('signup-btn');
    if (signupBtn) {
      signupBtn.addEventListener('click', function () {
        const username = document.getElementById('signup-username').value.trim();
        const email = document.getElementById('signup-email').value.trim();
        const password = document.getElementById('signup-password').value.trim();

        if (!username || !email || !password) {
          alert('Please fill in all fields to sign up.');
          return;
        }

        fetch('signup.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ username, email, password })
          
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert(`Account created for ${username}!`);
            modal.style.display = "none";
            window.location.href = 'index.html';
          } else {
            alert("Signup failed: " + data.message);
          }
        })
        .catch(error => {
          console.error("Signup error:", error);
          alert("Something went wrong. Please try again.");
        });
      });
    }
  }
});
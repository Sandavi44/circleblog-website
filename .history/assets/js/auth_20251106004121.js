document.addEventListener('DOMContentLoaded', function () {
  // Navbar Toggle
  const menuIcon = document.getElementById('menu-icon');
  const navbar = document.getElementById('navbar');
  const searchIcon = document.getElementById('search-icon');
  const searchBox = document.getElementById('search-box');

  if (menuIcon && navbar) {
    menuIcon.addEventListener('click', () => {
      navbar.classList.toggle('active');
    });
  }

  if (searchIcon && searchBox) {
    searchIcon.addEventListener('click', () => {
      searchBox.classList.toggle('active');
    });
  }

  // Show Login Form in Modal
  const loginNav = document.getElementById('nav-login');
  if (loginNav) {
    loginNav.addEventListener('click', function (e) {
      e.preventDefault();  // Prevent default behavior
      loadAuthModal('login');  // Show login form in the modal
    });
  }

  // Show Signup Form in Modal
  const signupNav = document.getElementById('nav-signup');
  if (signupNav) {
    signupNav.addEventListener('click', function (e) {
      e.preventDefault();  // Prevent default behavior
      loadAuthModal('signup');  // Show signup form in the modal
    });
  }

  // Modal close logic
  const closeBtn = document.querySelector('.close');
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      document.getElementById('authModal').style.display = 'none';
    });
  }

  window.addEventListener('click', function (e) {
    if (e.target === document.getElementById('authModal')) {
      document.getElementById('authModal').style.display = 'none';
    }
  });

  // Login Form Submission (AJAX)
  const loginBtn = document.getElementById('login-btn');
  if (loginBtn) {
    loginBtn.addEventListener('click', function () {
      const username = document.getElementById('login-username').value.trim();
      const password = document.getElementById('login-password').value.trim();
      
      if (!username || !password) {
        alert('Please enter both username and password.');
        return;  // Exit if validation fails
      }

      // AJAX call to login.php
      fetch('login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username: username, password: password }),  // Send JSON data
      })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(`Welcome back, ${username}!`);
          window.location.href = 'index.html';  // Redirect on successful login
        } else {
          alert(data.message);  // Show error message if login fails
        }
      })
      .catch((error) => {
        console.error('Error:', error);
        alert('An error occurred while logging in.');
      });
    });
  }

  // Signup Form Handler (AJAX)
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

      // AJAX call to signup.php
      fetch('signup.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username: username, email: email, password: password }),
      })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(`Account created for ${username}!`);
          window.location.href = 'index.html';  // Redirect to home page after successful signup
        } else {
          alert(data.message);  // Show error message from server
        }
      })
      .catch((error) => {
        console.error('Error:', error);
        alert('An error occurred during signup.');
      });
    });
  }

  // Function to load login/signup modal dynamically
  function loadAuthModal(formType) {
    const modalContainer = document.getElementById('authModal');
    const authForm = document.getElementById('authForm');
    const modalTitle = document.getElementById('modalTitle');

    if (formType === 'login') {
      modalTitle.innerHTML = 'Welcome Back!';
      authForm.innerHTML = `
        <label for="login-username">Username:</label><br>
        <input type="text" id="login-username" name="username" required><br><br>
        <label for="login-password">Password:</label><br>
        <input type="password" id="login-password" name="password" required><br><br>
        <button type="button" id="login-btn">Log In</button> <!-- Changed submit to button -->
      `;
    } else if (formType === 'signup') {
      modalTitle.innerHTML = 'Create an Account!';
      authForm.innerHTML = `
        <label for="signup-username">Username:</label><br>
        <input type="text" id="signup-username" name="username" required><br><br>
        <label for="signup-email">Email:</label><br>
        <input type="email" id="signup-email" name="email" required><br><br>
        <label for="signup-password">Password:</label><br>
        <input type="password" id="signup-password" name="password" required><br><br>
        <button type="button" id="signup-btn">Sign Up</button> <!-- Changed submit to button -->
      `;
    }

    modalContainer.style.display = 'flex'; // Display the modal
  }
});

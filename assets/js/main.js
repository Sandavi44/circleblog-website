/**
 * ========================================
 * CIRCLE BLOG - MAIN JAVASCRIPT
 * ========================================
 * 
 * FEATURES:
 * 1. Mobile menu toggle
 * 2. Search box toggle
 * 3. Login/Signup modal handling
 * 4. AJAX form submissions
 * 5. Flash message auto-hide
 * ========================================
 */

document.addEventListener('DOMContentLoaded', function () {
  
  // ========================================
  // 1. MOBILE MENU TOGGLE
  // ========================================
  const menuIcon = document.getElementById('menu-icon');
  const navbar = document.getElementById('navbar');
  
  if (menuIcon && navbar) {
    menuIcon.addEventListener('click', () => {
      // Toggle 'active' class to show/hide mobile menu
      navbar.classList.toggle('active');
    });
  }

  // ========================================
  // 2. SEARCH BOX TOGGLE
  // ========================================
  const searchIcon = document.getElementById('search-icon');
  const searchBox = document.getElementById('search-box');
  
  if (searchIcon && searchBox) {
    // Remove any existing listeners
    searchIcon.replaceWith(searchIcon.cloneNode(true));
    const newSearchIcon = document.getElementById('search-icon');
    
    newSearchIcon.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      console.log('Search icon clicked!'); // Debug
      
      // Toggle search box visibility
      searchBox.classList.toggle('active');
      
      // Focus on input when opened
      if (searchBox.classList.contains('active')) {
        const searchInput = searchBox.querySelector('input[type="search"]');
        if (searchInput) {
          setTimeout(() => {
            searchInput.focus();
            console.log('Input focused'); // Debug
          }, 100);
        }
      }
    });
    
    // Close search box when clicking outside
    document.addEventListener('click', (e) => {
      if (!searchBox.contains(e.target) && e.target.id !== 'search-icon' && !e.target.closest('#search-icon')) {
        searchBox.classList.remove('active');
      }
    });
    
    // Close search box with ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && searchBox.classList.contains('active')) {
        searchBox.classList.remove('active');
      }
    });
  }

  // ========================================
  // 3. MODAL ELEMENTS (for login/signup)
  // ========================================
  const modal = document.getElementById("authModal");
  const closeBtn = document.querySelector(".close");
  const modalTitle = document.getElementById("modalTitle");
  const authForm = document.getElementById("authForm");

  // ========================================
  // 4. SHOW LOGIN MODAL
  // ========================================
  const loginNav = document.getElementById('nav-login');
  if (loginNav) {
    loginNav.addEventListener('click', (e) => {
      e.preventDefault();
      showLoginForm();
    });
  }

  // ========================================
  // 5. SHOW SIGNUP MODAL
  // ========================================
  const signupNav = document.getElementById('nav-signup');
  if (signupNav) {
    signupNav.addEventListener('click', (e) => {
      e.preventDefault();
      showSignupForm();
    });
  }

  // ========================================
  // 6. CTA BUTTON (Hero section signup)
  // ========================================
  const ctaSignup = document.getElementById('cta-signup');
  if (ctaSignup) {
    ctaSignup.addEventListener('click', () => {
      showSignupForm();
    });
  }

  // ========================================
  // 7. CLOSE MODAL HANDLERS
  // ========================================
  
  // Close button (X)
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });
  }

  // Click outside modal to close
  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });

  // ========================================
  // 8. TOGGLE BETWEEN LOGIN/SIGNUP
  // ========================================
  // Event delegation for dynamically created toggle links
  document.addEventListener("click", (e) => {
    if (e.target && e.target.id === "toggleLink") {
      e.preventDefault();
      
      // Check current form type by modal title
      const isLogin = modalTitle.textContent.includes("Welcome");
      
      if (isLogin) {
        showSignupForm();
      } else {
        showLoginForm();
      }
    }
  });

  // ========================================
  // FUNCTION: Show Login Form
  // ========================================
  function showLoginForm() {
    if (!modal || !modalTitle || !authForm) return;
    
    modalTitle.textContent = "Welcome Back!";
    
    // Inject login form HTML
    authForm.innerHTML = `
      <input type="text" id="login-username" placeholder="Username" required />
      <input type="password" id="login-password" placeholder="Password" required />
      <button type="button" id="login-btn">Login</button>
      <p class="toggle-link">Don't have an account? <a id="toggleLink">Sign up</a></p>
    `;
    
    modal.style.display = "flex"; // Changed from "block" to "flex"
    
    // Attach login handler
    attachLoginHandler();
  }

  // ========================================
  // FUNCTION: Show Signup Form
  // ========================================
  function showSignupForm() {
    if (!modal || !modalTitle || !authForm) return;
    
    modalTitle.textContent = "Create Your Account";
    
    // Inject signup form HTML
    authForm.innerHTML = `
      <input type="text" id="signup-username" placeholder="Username" required />
      <input type="email" id="signup-email" placeholder="Email" required />
      <input type="password" id="signup-password" placeholder="Password" required />
      <button type="button" id="signup-btn">Sign Up</button>
      <p class="toggle-link">Already have an account? <a id="toggleLink">Login</a></p>
    `;
    
    modal.style.display = "flex"; // Changed from "block" to "flex"
    
    // Attach signup handler
    attachSignupHandler();
  }

  // ========================================
  // FUNCTION: Attach Login Handler
  // ========================================
  function attachLoginHandler() {
    const loginBtn = document.getElementById('login-btn');
    
    if (loginBtn) {
      loginBtn.addEventListener('click', async function () {
        // Get form values
        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value.trim();

        // Validation
        if (!username || !password) {
          showToast('Error', 'Please enter both username and password', 'error');
          return;
        }

        // Disable button during request
        loginBtn.disabled = true;
        loginBtn.textContent = 'Logging in...';

        try {
          // Send AJAX request to login.php
          const response = await fetch('auth/login.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
          });

          const data = await response.json();

          if (data.success) {
            // SUCCESS: Show toast and redirect
            showToast('Success!', `Welcome back, ${username}!`, 'success');
            modal.style.display = "none";
            setTimeout(() => {
              window.location.href = 'index.php';
            }, 1000);
          } else {
            // FAILURE: Show error
            showToast('Login Failed', data.message, 'error');
            loginBtn.disabled = false;
            loginBtn.textContent = 'Login';
          }
        } catch (error) {
          console.error("Login error:", error);
          showToast('Error', 'Something went wrong. Please try again.', 'error');
          loginBtn.disabled = false;
          loginBtn.textContent = 'Login';
        }
      });
    }
  }

  // ========================================
  // FUNCTION: Attach Signup Handler
  // ========================================
  function attachSignupHandler() {
    const signupBtn = document.getElementById('signup-btn');
    
    if (signupBtn) {
      signupBtn.addEventListener('click', async function () {
        // Get form values
        const username = document.getElementById('signup-username').value.trim();
        const email = document.getElementById('signup-email').value.trim();
        const password = document.getElementById('signup-password').value.trim();

        // Validation
        if (!username || !email || !password) {
          showToast('Error', 'Please fill in all fields', 'error');
          return;
        }

        // Email validation
        if (!isValidEmail(email)) {
          showToast('Error', 'Please enter a valid email address', 'error');
          return;
        }

        // Disable button during request
        signupBtn.disabled = true;
        signupBtn.textContent = 'Creating account...';

        try {
          // Send AJAX request to signup.php
          const response = await fetch('auth/signup.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, email, password })
          });

          const data = await response.json();

          if (data.success) {
            // SUCCESS: Show success message and switch to login
            showToast('Success!', `Account created for ${username}! Please log in.`, 'success');
            setTimeout(() => {
              showLoginForm();
            }, 1500);
          } else {
            // FAILURE: Show error message
            showToast('Signup Failed', data.message, 'error');
            signupBtn.disabled = false;
            signupBtn.textContent = 'Sign Up';
          }
        } catch (error) {
          console.error("Signup error:", error);
          showToast('Error', 'Something went wrong. Please try again.', 'error');
          signupBtn.disabled = false;
          signupBtn.textContent = 'Sign Up';
        }
      });
    }
  }

  // ========================================
  // UTILITY: Email Validation
  // ========================================
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // ========================================
  // 9. FLASH MESSAGE AUTO-HIDE
  // ========================================
  const flashMessage = document.querySelector('.flash-message');
  
  if (flashMessage) {
    // Auto-hide after 5 seconds
    setTimeout(() => {
      flashMessage.style.transition = 'opacity 0.5s';
      flashMessage.style.opacity = '0';
      
      setTimeout(() => {
        flashMessage.remove();
      }, 500);
    }, 5000);
    
    // Click to dismiss
    flashMessage.addEventListener('click', () => {
      flashMessage.style.opacity = '0';
      setTimeout(() => {
        flashMessage.remove();
      }, 500);
    });
  }

  // ========================================
  // 10. BLOG CARD CLICK (for logged out users)
  // ========================================
  // If user is not logged in, clicking "Read More" shows login modal
  const readMoreLinks = document.querySelectorAll('.blog-card .read-more');
  
  // Only apply this if auth modal exists (meaning user is logged out)
  if (modal) {
    readMoreLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        // This will only trigger if the modal exists
        // When user is logged in, modal doesn't exist, so normal link works
      });
    });
  }

  // ========================================
  // 11. PREVENT ENTER KEY SUBMIT ON TEXT INPUTS
  // ========================================
  // Prevents accidental form submission when pressing Enter
  document.addEventListener('keypress', function(e) {
    if (e.target.tagName === 'INPUT' && e.target.type === 'text' && e.keyCode === 13) {
      e.preventDefault();
    }
  });

  // ========================================
  // 12. SCROLL TO TOP BUTTON (Optional)
  // ========================================
  const scrollToTopBtn = document.getElementById('scroll-to-top');
  
  if (scrollToTopBtn) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
        scrollToTopBtn.style.display = 'block';
      } else {
        scrollToTopBtn.style.display = 'none';
      }
    });
    
    scrollToTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // ========================================
  // CONSOLE LOG (for debugging)
  // ========================================
  console.log('🎉 Circle Blog - JavaScript Loaded Successfully!');
  console.log('✅ All event listeners attached');
});

/**
 * ========================================
 * GLOBAL HELPER FUNCTIONS
 * ========================================
 * These can be called from anywhere
 */

// Show toast notification (replaces alert())
function showToast(title, message, type = 'success') {
  // Remove existing toasts
  const existingToasts = document.querySelectorAll('.toast-notification');
  existingToasts.forEach(toast => toast.remove());

  // Create toast element
  const toast = document.createElement('div');
  toast.className = `toast-notification toast-${type}`;
  
  // Icon based on type
  let icon = '';
  if (type === 'success') icon = '<i class="bx bx-check-circle"></i>';
  if (type === 'error') icon = '<i class="bx bx-error-circle"></i>';
  if (type === 'info') icon = '<i class="bx bx-info-circle"></i>';
  
  toast.innerHTML = `
    ${icon}
    <div class="toast-content">
      <div class="toast-title">${title}</div>
      <div class="toast-message">${message}</div>
    </div>
    <span class="toast-close">&times;</span>
  `;
  
  document.body.appendChild(toast);
  
  // Close button
  const closeBtn = toast.querySelector('.toast-close');
  closeBtn.addEventListener('click', () => {
    toast.style.animation = 'toastSlideOut 0.3s ease-out';
    setTimeout(() => toast.remove(), 300);
  });
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    if (toast.parentElement) {
      toast.style.animation = 'toastSlideOut 0.3s ease-out';
      setTimeout(() => toast.remove(), 300);
    }
  }, 5000);
}

// Show loading spinner (if you add one later)
function showLoading() {
  const loader = document.getElementById('loader');
  if (loader) {
    loader.style.display = 'block';
  }
}

function hideLoading() {
  const loader = document.getElementById('loader');
  if (loader) {
    loader.style.display = 'none';
  }
}

// Show custom alert/notification (deprecated - use showToast instead)
function showNotification(message, type = 'info') {
  showToast('Notification', message, type);
}
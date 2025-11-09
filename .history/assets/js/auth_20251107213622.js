/**
 * ========================================
 * AUTHENTICATION JAVASCRIPT
 * ========================================
 * 
 * Handles:
 * - Login/Signup modal display
 * - Form switching (login ↔ signup)
 * - AJAX form submissions
 * - Form validation
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Get modal elements for login/signup
    const modal = document.getElementById('authModal');
    const modalTitle = document.getElementById('modalTitle');
    const authForm = document.getElementById('authForm');
    const closeBtn = document.querySelector('.close');
    
    // Get navigation buttons for login and signup
    const loginBtn = document.getElementById('nav-login');
    const signupBtn = document.getElementById('nav-signup');
    const heroSignupBtn = document.getElementById('hero-signup');
    
    if (!modal) return; // Exit if modal doesn't exist (user is logged in)
    
    // ========================================
    // MODAL OPEN/CLOSE
    // ========================================
    
    // Open login modal when login button is clicked
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default action
            showLoginForm(); // Show login form
            modal.style.display = 'block'; // Open modal
        });
    }
    
    // Open signup modal when signup button is clicked
    if (signupBtn) {
        signupBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showSignupForm(); // Show signup form
            modal.style.display = 'block'; // Open modal
        });
    }
    
    // Hero section signup button to open signup modal
    if (heroSignupBtn) {
        heroSignupBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showSignupForm(); // Show signup form
            modal.style.display = 'block'; // Open modal
        });
    }
    
    // Close modal on X click
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none'; // Close modal
            clearMessages(); // Clear any displayed messages
        });
    }
    
    // Close modal if clicked outside the modal content
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none'; // Close modal
            clearMessages(); // Clear messages
        }
    });
    
    // ========================================
    // LOGIN FORM
    // ========================================
    // Displays the login form
    function showLoginForm() {
        modalTitle.textContent = 'Welcome Back!'; // Update modal title
        
        // Update the form with login fields
        authForm.innerHTML = `
            <div class="form-group">
                <label for="loginEmail">
                    <i class='bx bx-envelope'></i> Email
                </label>
                <input type="email" id="loginEmail" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="loginPassword">
                    <i class='bx bx-lock'></i> Password
                </label>
                <input type="password" id="loginPassword" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-log-in'></i> Login
                </button>
            </div>
            
            <div class="form-footer">
                Don't have an account? 
                <a href="#" id="switchToSignup">Sign Up</a>
            </div>
            
            <div id="authMessage" class="message"></div>
        `;
        
        // Add form submit handler for login
        authForm.onsubmit = handleLogin;
        
        // Switch to signup form when clicking on 'Sign Up' link
        document.getElementById('switchToSignup').addEventListener('click', function(e) {
            e.preventDefault();
            showSignupForm(); // Show signup form
        });
    }
    
    // ========================================
    // SIGNUP FORM
    // ========================================
    // Displays the signup form
    function showSignupForm() {
        modalTitle.textContent = 'Create Account'; // Update modal title
        
        // Update the form with signup fields
        authForm.innerHTML = `
            <div class="form-group">
                <label for="signupName">
                    <i class='bx bx-user'></i> Full Name
                </label>
                <input type="text" id="signupName" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="signupEmail">
                    <i class='bx bx-envelope'></i> Email
                </label>
                <input type="email" id="signupEmail" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="signupPassword">
                    <i class='bx bx-lock'></i> Password
                </label>
                <input type="password" id="signupPassword" name="password" required minlength="6">
                <small>Minimum 6 characters</small>
            </div>
            
            <div class="form-group">
                <label for="signupConfirmPassword">
                    <i class='bx bx-lock'></i> Confirm Password
                </label>
                <input type="password" id="signupConfirmPassword" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-user-plus'></i> Sign Up
                </button>
            </div>
            
            <div class="form-footer">
                Already have an account? 
                <a href="#" id="switchToLogin">Login</a>
            </div>
            
            <div id="authMessage" class="message"></div>
        `;
        
        // Add form submit handler for signup
        authForm.onsubmit = handleSignup;
        
        // Switch to login form when clicking on 'Login' link
        document.getElementById('switchToLogin').addEventListener('click', function(e) {
            e.preventDefault();
            showLoginForm(); // Show login form
        });
    }
    
    // ========================================
    // HANDLE LOGIN SUBMISSION
    // ========================================
    // Handles login form submission
    function handleLogin(e) {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        const submitBtn = authForm.querySelector('button[type="submit"]');
        
        // Clear previous messages
        clearMessages();
        
        // Show loading spinner
        showLoading(submitBtn);
        
        // Send AJAX request to login API
        fetch(BASE_URL + '/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading(submitBtn);
            
            if (data.success) {
                showMessage('success', data.message);
                
                // Redirect after 1 second
                setTimeout(function() {
                    window.location.href = data.redirect || BASE_URL + '/index.php';
                }, 1000);
            } else {
                showMessage('error', data.message);
            }
        })
        .catch(error => {
            hideLoading(submitBtn);
            showMessage('error', 'An error occurred. Please try again.');
            console.error('Login error:', error);
        });
    }
    
    // ========================================
    // HANDLE SIGNUP SUBMISSION
    // ========================================
    // Handles signup form submission
    function handleSignup(e) {
        e.preventDefault();
        
        const name = document.getElementById('signupName').value;
        const email = document.getElementById('signupEmail').value;
        const password = document.getElementById('signupPassword').value;
        const confirmPassword = document.getElementById('signupConfirmPassword').value;
        const submitBtn = authForm.querySelector('button[type="submit"]');
        
        // Clear previous messages
        clearMessages();
        
        // Client-side validation: check if passwords match
        if (password !== confirmPassword) {
            showMessage('error', 'Passwords do not match');
            return;
        }
        
        // Client-side validation: check password length
        if (password.length < 6) {
            showMessage('error', 'Password must be at least 6 characters');
            return;
        }
        
        // Show loading spinner
        showLoading(submitBtn);
        
        // Send AJAX request to signup API
        fetch(BASE_URL + '/auth/signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&confirm_password=${encodeURIComponent(confirmPassword)}`
        })
        .then(response => response.json())
        .then(data => {
            hideLoading(submitBtn);
            
            if (data.success) {
                showMessage('success', data.message);
                
                // Redirect after 1 second
                setTimeout(function() {
                    window.location.href = data.redirect || BASE_URL + '/index.php';
                }, 1000);
            } else {
                showMessage('error', data.message);
            }
        })
        .catch(error => {
            hideLoading(submitBtn);
            showMessage('error', 'An error occurred. Please try again.');
            console.error('Signup error:', error);
        });
    }
    
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    
    // Display success or error messages
    function showMessage(type, message) {
        const messageDiv = document.getElementById('authMessage');
        if (!messageDiv) return;
        
        messageDiv.className = 'message message-' + type;
        messageDiv.textContent = message;
        messageDiv.style.display = 'block';
    }
    
    // Clear any existing messages
    function clearMessages() {
        const messageDiv = document.getElementById('authMessage');
        if (messageDiv) {
            messageDiv.style.display = 'none';
            messageDiv.textContent = '';
        }
    }
    
    // Initialize with login form by default
    showLoginForm();
    
    console.log('✅ Auth.js loaded successfully');
});

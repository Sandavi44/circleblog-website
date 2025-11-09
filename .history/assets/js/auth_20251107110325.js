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
    
    // Get modal elements
    const modal = document.getElementById('authModal');
    const modalTitle = document.getElementById('modalTitle');
    const authForm = document.getElementById('authForm');
    const closeBtn = document.querySelector('.close');
    
    // Get navigation buttons
    const loginBtn = document.getElementById('nav-login');
    const signupBtn = document.getElementById('nav-signup');
    const heroSignupBtn = document.getElementById('hero-signup');
    
    if (!modal) return; // Exit if modal doesn't exist (user is logged in)
    
    // ========================================
    // MODAL OPEN/CLOSE
    // ========================================
    
    // Open login modal
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showLoginForm();
            modal.style.display = 'block';
        });
    }
    
    // Open signup modal
    if (signupBtn) {
        signupBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showSignupForm();
            modal.style.display = 'block';
        });
    }
    
    // Hero signup button
    if (heroSignupBtn) {
        heroSignupBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showSignupForm();
            modal.style.display = 'block';
        });
    }
    
    // Close modal on X click
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
            clearMessages();
        });
    }
    
    // Close modal on outside click
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            clearMessages();
        }
    });
    
    // ========================================
    // LOGIN FORM
    // ========================================
    function showLoginForm() {
        modalTitle.textContent = 'Welcome Back!';
        
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
        
        // Add form submit handler
        authForm.onsubmit = handleLogin;
        
        // Add switch to signup handler
        document.getElementById('switchToSignup').addEventListener('click', function(e) {
            e.preventDefault();
            showSignupForm();
        });
    }
    
    // ========================================
    // SIGNUP FORM
    // ========================================
    function showSignupForm() {
        modalTitle.textContent = 'Create Account';
        
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
        
        // Add form submit handler
        authForm.onsubmit = handleSignup;
        
        // Add switch to login handler
        document.getElementById('switchToLogin').addEventListener('click', function(e) {
            e.preventDefault();
            showLoginForm();
        });
    }
    
    // ========================================
    // HANDLE LOGIN SUBMISSION
    // ========================================
    function handleLogin(e) {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        const submitBtn = authForm.querySelector('button[type="submit"]');
        
        // Clear previous messages
        clearMessages();
        
        // Show loading
        showLoading(submitBtn);
        
        // Send AJAX request
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
    function handleSignup(e) {
        e.preventDefault();
        
        const name = document.getElementById('signupName').value;
        const email = document.getElementById('signupEmail').value;
        const password = document.getElementById('signupPassword').value;
        const confirmPassword = document.getElementById('signupConfirmPassword').value;
        const submitBtn = authForm.querySelector('button[type="submit"]');
        
        // Clear previous messages
        clearMessages();
        
        // Client-side validation
        if (password !== confirmPassword) {
            showMessage('error', 'Passwords do not match');
            return;
        }
        
        if (password.length < 6) {
            showMessage('error', 'Password must be at least 6 characters');
            return;
        }
        
        // Show loading
        showLoading(submitBtn);
        
        // Send AJAX request
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
    
    function showMessage(type, message) {
        const messageDiv = document.getElementById('authMessage');
        if (!messageDiv) return;
        
        messageDiv.className = 'message message-' + type;
        messageDiv.textContent = message;
        messageDiv.style.display = 'block';
    }
    
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
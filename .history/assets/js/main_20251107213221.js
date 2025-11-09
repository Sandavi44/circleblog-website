/**
 * ========================================
 * MAIN JAVASCRIPT - Circle Blog
 * ========================================
 * 
 * Handles:
 * - Mobile menu toggle
 * - Search functionality
 * - Smooth scrolling
 * - Flash message dismissal
 * - Form validation and helpers
 * - User interaction helpers (e.g., loading spinner, confirmation dialogs)
 * ========================================

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // MOBILE MENU TOGGLE
    // ========================================
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.getElementById('navbar');
    
    if (menuIcon && navbar) {
        menuIcon.addEventListener('click', function() {
            navbar.classList.toggle('active');
            
            // Change icon between menu and close
            if (navbar.classList.contains('active')) {
                menuIcon.classList.remove('bx-menu');
                menuIcon.classList.add('bx-x');
            } else {
                menuIcon.classList.remove('bx-x');
                menuIcon.classList.add('bx-menu');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navbar.contains(e.target) && !menuIcon.contains(e.target)) {
                navbar.classList.remove('active');
                menuIcon.classList.remove('bx-x');
                menuIcon.classList.add('bx-menu');
            }
        });
    }
    
    // ========================================
    // SEARCH BOX TOGGLE
    // ========================================
    const searchIcon = document.getElementById('search-icon');
    const searchBox = document.getElementById('search-box');
    
    if (searchIcon && searchBox) {
        searchIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            searchBox.classList.toggle('active');
            
            // Focus on input when opened
            if (searchBox.classList.contains('active')) {
                const searchInput = searchBox.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
        
        // Close search when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchBox.contains(e.target) && !searchIcon.contains(e.target)) {
                searchBox.classList.remove('active');
            }
        });
    }
    
    // ========================================
    // FLASH MESSAGE AUTO-DISMISS
    // ========================================
    const flashMessages = document.querySelectorAll('.flash-message');
    
    flashMessages.forEach(function(message) {
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 300);
        }, 5000);
        
        // Manual dismiss on click
        message.addEventListener('click', function() {
            this.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 300);
        });
    });
    
    // ========================================
    // SMOOTH SCROLLING FOR ANCHOR LINKS
    // ========================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip if it's just "#" (used for modals)
            if (href === '#') return;
            
            e.preventDefault();
            
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // ========================================
    // ACTIVE NAVIGATION LINK HIGHLIGHTING
    // ========================================
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.navbar a');
    
    navLinks.forEach(function(link) {
        const linkHref = link.getAttribute('href');
        if (linkHref && linkHref.includes(currentPage)) {
            link.classList.add('active');
        }
    });
    
    // ========================================
    // FORM VALIDATION HELPER
    // ========================================
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(function(input) {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error');
                    
                    // Show error message
                    let errorMsg = input.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'This field is required';
                        input.parentNode.insertBefore(errorMsg, input.nextSibling);
                    }
                } else {
                    input.classList.remove('error');
                    const errorMsg = input.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
    
    // ========================================
    // LOADING SPINNER HELPER
    // ========================================
    window.showLoading = function(button) {
        if (!button) return;
        
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Loading...';
    };
    
    window.hideLoading = function(button) {
        if (!button) return;
        
        button.disabled = false;
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
        }
    };
    
    // ========================================
    // CONFIRM DELETE HELPER
    // ========================================
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm-delete') || 'Are you sure you want to delete this?';
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // ========================================
    // CHARACTER COUNTER FOR TEXTAREAS
    // ========================================
    const textareasWithCounter = document.querySelectorAll('textarea[data-max-length]');
    
    textareasWithCounter.forEach(function(textarea) {
        const maxLength = parseInt(textarea.getAttribute('data-max-length'));
        
        // Create counter element
        const counter = document.createElement('div');
        counter.className = 'character-counter';
        counter.textContent = `0 / ${maxLength}`;
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);
        
        // Update counter on input
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            counter.textContent = `${currentLength} / ${maxLength}`;
            
            if (currentLength > maxLength) {
                counter.classList.add('exceeded');
            } else {
                counter.classList.remove('exceeded');
            }
        });
    });
    
    // ========================================
    // IMAGE PREVIEW BEFORE UPLOAD
    // ========================================
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Check if preview container exists
            let preview = input.nextElementSibling;
            if (!preview || !preview.classList.contains('image-preview')) {
                preview = document.createElement('div');
                preview.className = 'image-preview';
                input.parentNode.insertBefore(preview, input.nextSibling);
            }
            
            // Create image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        });
    });
    
    // ========================================
    // AUTO-RESIZE TEXTAREA
    // ========================================
    const autoResizeTextareas = document.querySelectorAll('textarea[data-auto-resize]');
    
    autoResizeTextareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Trigger on page load
        textarea.dispatchEvent(new Event('input'));
    });
    
    // ========================================
    // COPY TO CLIPBOARD HELPER
    // ========================================
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Copied to clipboard!');
        }).catch(function(err) {
            console.error('Failed to copy:', err);
        });
    };
    
    console.log('✅ Main.js loaded successfully');
});

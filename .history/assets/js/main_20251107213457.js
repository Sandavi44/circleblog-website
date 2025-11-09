// Wait for DOM to load before executing the script
document.addEventListener('DOMContentLoaded', function() {
    
    // MOBILE MENU TOGGLE: Toggle the visibility of the navigation menu when the menu icon is clicked
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.getElementById('navbar');
    
    if (menuIcon && navbar) {
        menuIcon.addEventListener('click', function() {
            navbar.classList.toggle('active');
            
            // Change the icon between 'menu' and 'close' based on the state of the menu
            if (navbar.classList.contains('active')) {
                menuIcon.classList.remove('bx-menu');
                menuIcon.classList.add('bx-x');
            } else {
                menuIcon.classList.remove('bx-x');
                menuIcon.classList.add('bx-menu');
            }
        });
        
        // Close the menu when clicking outside of the menu and icon
        document.addEventListener('click', function(e) {
            if (!navbar.contains(e.target) && !menuIcon.contains(e.target)) {
                navbar.classList.remove('active');
                menuIcon.classList.remove('bx-x');
                menuIcon.classList.add('bx-menu');
            }
        });
    }
    
    // SEARCH BOX TOGGLE: Show or hide the search box when the search icon is clicked
    const searchIcon = document.getElementById('search-icon');
    const searchBox = document.getElementById('search-box');
    
    if (searchIcon && searchBox) {
        searchIcon.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            searchBox.classList.toggle('active');
            
            // Focus on the search input when the search box is opened
            if (searchBox.classList.contains('active')) {
                const searchInput = searchBox.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
        
        // Close the search box when clicking outside of it
        document.addEventListener('click', function(e) {
            if (!searchBox.contains(e.target) && !searchIcon.contains(e.target)) {
                searchBox.classList.remove('active');
            }
        });
    }
    
    // FLASH MESSAGE AUTO-DISMISS: Automatically dismiss flash messages after 5 seconds, or when clicked
    const flashMessages = document.querySelectorAll('.flash-message');
    
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 300); // Delay removal to allow opacity transition
        }, 5000);
        
        // Allow manual dismiss of flash messages on click
        message.addEventListener('click', function() {
            this.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 300);
        });
    });
    
    // SMOOTH SCROLLING: Enable smooth scrolling when clicking on anchor links (e.g., <a href="#section">)
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Skip if it's just "#" (used for modals)
            if (href === '#') return;
            
            e.preventDefault(); // Prevent the default link behavior
            
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth', // Enable smooth scrolling
                    block: 'start' // Align to the top of the viewport
                });
            }
        });
    });
    
    // ACTIVE NAVIGATION LINK HIGHLIGHTING: Highlight the current page in the navigation menu
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.navbar a');
    
    navLinks.forEach(function(link) {
        const linkHref = link.getAttribute('href');
        if (linkHref && linkHref.includes(currentPage)) {
            link.classList.add('active'); // Add 'active' class to the current page link
        }
    });
    
    // FORM VALIDATION HELPER: Validate required fields in forms before submitting
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(function(input) {
                if (!input.value.trim()) {
                    isValid = false; // Mark as invalid if required fields are empty
                    input.classList.add('error'); // Add error class to highlight invalid fields
                    
                    // Show an error message
                    let errorMsg = input.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'This field is required';
                        input.parentNode.insertBefore(errorMsg, input.nextSibling);
                    }
                } else {
                    input.classList.remove('error'); // Remove error class if the field is valid
                    const errorMsg = input.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove(); // Remove error message
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
    
    // LOADING SPINNER HELPER: Show and hide loading spinner in buttons
    window.showLoading = function(button) {
        if (!button) return;
        
        button.disabled = true; // Disable button to prevent multiple clicks
        button.dataset.originalText = button.innerHTML; // Store original text
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Loading...'; // Show spinner
    };
    
    window.hideLoading = function(button) {
        if (!button) return;
        
        button.disabled = false; // Re-enable button
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText; // Restore original button text
        }
    };
    
    // CONFIRM DELETE HELPER: Confirm deletion before proceeding with delete action
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm-delete') || 'Are you sure you want to delete this?';
            
            if (!confirm(message)) {
                e.preventDefault(); // Prevent deletion if user cancels
                return false;
            }
        });
    });
    
    // CHARACTER COUNTER FOR TEXTAREAS: Show character count for textareas with a 'data-max-length' attribute
    const textareasWithCounter = document.querySelectorAll('textarea[data-max-length]');
    
    textareasWithCounter.forEach(function(textarea) {
        const maxLength = parseInt(textarea.getAttribute('data-max-length'));
        
        // Create and insert character counter
        const counter = document.createElement('div');
        counter.className = 'character-counter';
        counter.textContent = `0 / ${maxLength}`;
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);
        
        // Update character counter as user types
        textarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            counter.textContent = `${currentLength} / ${maxLength}`;
            
            if (currentLength > maxLength) {
                counter.classList.add('exceeded'); // Highlight counter if max length is exceeded
            } else {
                counter.classList.remove('exceeded');
            }
        });
    });
    
    // IMAGE PREVIEW BEFORE UPLOAD: Show a preview of selected image before uploading
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Check if preview container exists, if not, create it
            let preview = input.nextElementSibling;
            if (!preview || !preview.classList.contains('image-preview')) {
                preview = document.createElement('div');
                preview.className = 'image-preview';
                input.parentNode.insertBefore(preview, input.nextSibling);
            }
            
            // Create and show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        });
    });
    
    // AUTO-RESIZE TEXTAREA: Automatically resize textareas as user types
    const autoResizeTextareas = document.querySelectorAll('textarea[data-auto-resize]');
    
    autoResizeTextareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px'; // Resize textarea based on content
        });
        
        // Trigger resize on page load
        textarea.dispatchEvent(new Event('input'));
    });
    
    // COPY TO CLIPBOARD HELPER: Copy text to clipboard
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Copied to clipboard!');
        }).catch(function(err) {
            console.error('Failed to copy:', err);
        });
    };
    
    console.log('✅ Main.js loaded successfully');
});

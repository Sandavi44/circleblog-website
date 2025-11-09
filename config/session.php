<?php
/**
 * ========================================
 * SESSION MANAGEMENT
 * ========================================
 * 
 * This file handles:
 * 1. Starting PHP sessions securely
 * 2. Checking if user is logged in
 * 3. Helper functions for session data
 * 
 * WHAT IS A SESSION?
 * - Sessions track user data across pages
 * - When you login, session stores your user_id
 * - Every page can check "is this user logged in?"
 * ========================================
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Configure session settings for security
    ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access to session cookie
    ini_set('session.use_only_cookies', 1); // Only use cookies (not URL params)
    
    session_start(); // Start the session
}

/**
 * ========================================
 * FUNCTION: Check if user is logged in
 * ========================================
 * 
 * Returns true if user_id exists in session
 * 
 * USAGE:
 * if (isLoggedIn()) {
 *     echo "Welcome back!";
 * } else {
 *     echo "Please login";
 * }
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * ========================================
 * FUNCTION: Get current user ID
 * ========================================
 * 
 * Returns the logged-in user's ID or null
 * 
 * USAGE:
 * $userId = getCurrentUserId();
 * if ($userId) {
 *     echo "Your ID is: $userId";
 * }
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * ========================================
 * FUNCTION: Get current username
 * ========================================
 * 
 * Returns the logged-in user's username or null
 * 
 * USAGE:
 * $username = getCurrentUsername();
 * echo "Hello, $username!";
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * ========================================
 * FUNCTION: Require login (redirect if not logged in)
 * ========================================
 * 
 * Use this at the top of pages that require authentication
 * 
 * USAGE:
 * requireLogin(); // Will redirect to login if not authenticated
 * // Rest of your page code here
 */
function requireLogin() {
    if (!isLoggedIn()) {
        // Store the page they tried to access
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to home page (they can login via modal)
        header('Location: /circle-blog/index.php');
        exit;
    }
}

/**
 * ========================================
 * FUNCTION: Check if user owns a resource
 * ========================================
 * 
 * Checks if current user is the owner of a post/comment
 * 
 * USAGE:
 * if (isOwner($post['user_id'])) {
 *     echo "You can edit this post";
 * }
 * 
 * @param int $resourceUserId - The user_id of the resource owner
 * @return bool - True if current user owns the resource
 */
function isOwner($resourceUserId) {
    return isLoggedIn() && getCurrentUserId() == $resourceUserId;
}

/**
 * ========================================
 * FUNCTION: Set flash message
 * ========================================
 * 
 * Flash messages are one-time notifications
 * They show once, then disappear
 * 
 * USAGE:
 * setFlashMessage('success', 'Post created successfully!');
 * 
 * @param string $type - 'success', 'error', 'warning', 'info'
 * @param string $message - The message to display
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * ========================================
 * FUNCTION: Get and clear flash message
 * ========================================
 * 
 * Retrieves the flash message and removes it from session
 * 
 * USAGE:
 * $flash = getFlashMessage();
 * if ($flash) {
 *     echo "<div class='alert-{$flash['type']}'>{$flash['message']}</div>";
 * }
 * 
 * @return array|null - Flash message array or null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); // Remove it after reading
        return $flash;
    }
    return null;
}

/**
 * ========================================
 * FUNCTION: Logout user
 * ========================================
 * 
 * Destroys session and clears all data
 * 
 * USAGE:
 * logoutUser();
 */
function logoutUser() {
    // Clear all session variables
    $_SESSION = [];
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * ========================================
 * SECURITY: Regenerate session ID
 * ========================================
 * 
 * Call this after login/logout to prevent session fixation attacks
 * 
 * EXPLANATION:
 * Session fixation is when attacker steals your session ID
 * Regenerating creates a NEW session ID, making old one useless
 */
function regenerateSession() {
    session_regenerate_id(true);
}

/**
 * ========================================
 * HOW TO USE THIS FILE
 * ========================================
 * 
 * At the top of ANY file that needs session access:
 * 
 * require_once __DIR__ . '/../config/session.php';
 * 
 * Then use the functions:
 * 
 * // Check if logged in
 * if (isLoggedIn()) { ... }
 * 
 * // Require login (redirect if not)
 * requireLogin();
 * 
 * // Get user info
 * $userId = getCurrentUserId();
 * $username = getCurrentUsername();
 * 
 * // Check ownership
 * if (isOwner($post['user_id'])) { ... }
 * 
 * // Flash messages
 * setFlashMessage('success', 'Post deleted!');
 * 
 * ========================================
 */
?>
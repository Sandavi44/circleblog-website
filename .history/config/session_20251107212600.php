<?php
/**
 * ========================================
 * SESSION MANAGEMENT
 * ========================================
 */

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
    ini_set('session.use_only_cookies', 1); // Use cookies only (no URL-based session)
    ini_set('session.cookie_secure', 1); // Use secure cookie if HTTPS is available (recommended for production)
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * @return mixed|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username
 * @return mixed|null
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Require login (redirect if not logged in)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Store the current page to redirect after login
        header('Location: /circle-blog/index.php'); // Redirect to login page or home page
        exit;
    }
}

/**
 * Check if user owns a resource (e.g., post, comment)
 * @param int $resourceUserId - User ID that owns the resource
 * @return bool
 */
function isOwner($resourceUserId) {
    return isLoggedIn() && getCurrentUserId() == $resourceUserId;
}

/**
 * Set flash message
 * @param string $type - Message type (e.g., 'success', 'error')
 * @param string $message - Flash message content
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return mixed|null - Flash message or null if not set
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); // Clear flash message after getting it
        return $flash;
    }
    return null;
}

/**
 * Logout user
 */
function logoutUser() {
    $_SESSION = []; // Clear session data
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/'); // Destroy session cookie
    }
    session_destroy(); // Destroy session
}

/**
 * Reg*

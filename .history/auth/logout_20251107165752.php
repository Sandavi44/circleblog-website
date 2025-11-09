<?php
/**
 * ========================================
 * LOGOUT HANDLER
 * ========================================
 * 
 * FLOW:
 * 1. Include session management
 * 2. Destroy all session data
 * 3. Delete session cookie
 * 4. Redirect to homepage
 * 
 * WHY destroy session completely?
 * - Security: Prevents session hijacking
 * - Clean logout: Removes all user data
 * ========================================
 */

// Include session management
require_once __DIR__ . '/../config/session.php';

// Call logout function (defined in session.php)
// This function:
// - Clears $_SESSION array
// - Deletes session cookie
// - Destroys the session
logoutUser();

// Redirect to homepage
header("Location: /circle-blog/index.php");
exit;
?>
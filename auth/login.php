<?php
/**
 * ========================================
 * LOGIN HANDLER
 * ========================================
 * 
 * FLOW:
 * 1. Receive JSON data from frontend (username, password)
 * 2. Validate input
 * 3. Check credentials in database
 * 4. Create session if valid
 * 5. Return JSON response
 * 
 * SECURITY:
 * - Uses password_verify() for hashed passwords
 * - Prepared statements prevent SQL injection
 * - Session regeneration prevents session fixation
 * ========================================
 */

// Set response type to JSON
header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// Get JSON data from frontend
$data = json_decode(file_get_contents("php://input"), true);

// Extract username and password
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// ========================================
// VALIDATION: Check if fields are filled
// ========================================
if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Both username and password are required'
    ]);
    exit;
}

// ========================================
// DATABASE QUERY: Check credentials
// ========================================
try {
    // Prepare SQL query to get user by username
    // WHY prepared statement? Prevents SQL injection attacks
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    // Fetch user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ========================================
    // VERIFY PASSWORD
    // ========================================
    // password_verify() compares plain password with hashed password
    // This is secure - never store plain passwords!
    if ($user && password_verify($password, $user['password'])) {
        
        // ========================================
        // LOGIN SUCCESS: Create session
        // ========================================
        
        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Regenerate session ID for security (prevents session fixation)
        regenerateSession();

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
    } else {
        
        // ========================================
        // LOGIN FAILED: Invalid credentials
        // ========================================
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid username or password'
        ]);
    }

} catch (PDOException $e) {
    
    // ========================================
    // DATABASE ERROR
    // ========================================
    // Log error for debugging (never show to user in production)
    error_log("Login error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again later.'
    ]);
}
?>
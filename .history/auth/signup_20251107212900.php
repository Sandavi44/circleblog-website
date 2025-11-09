<?php
/**
 * ========================================
 * SIGNUP HANDLER
 * ========================================
 * 
 * FLOW:
 * 1. Receive JSON data (username, email, password)
 * 2. Validate all inputs
 * 3. Hash password securely
 * 4. Insert new user into database
 * 5. Return JSON response
 * 
 * SECURITY:
 * - Password hashing with password_hash()
 * - Email validation
 * - Prepared statements
 * - Duplicate username/email check
 * ========================================
 */

// Set response type to JSON
header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Get JSON data from frontend
$data = json_decode(file_get_contents("php://input"), true);

// Extract fields
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// ========================================
// VALIDATION: Check if all fields are filled
// ========================================
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode([
        'success' => false, 
        'message' => 'All fields are required'
    ]);
    exit;
}

// ========================================
// VALIDATION: Check username length
// ========================================
if (strlen($username) < 3 || strlen($username) > 50) {
    echo json_encode([
        'success' => false,
        'message' => 'Username must be between 3 and 50 characters'
    ]);
    exit;
}

// ========================================
// VALIDATION: Check email format
// ========================================
if (!isValidEmail($email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// ========================================
// VALIDATION: Check password length
// ========================================
$minPasswordLength = (int)(getenv('MIN_PASSWORD_LENGTH') ?: 6);
if (strlen($password) < $minPasswordLength) {
    echo json_encode([
        'success' => false,
        'message' => "Password must be at least $minPasswordLength characters"
    ]);
    exit;
}

// ========================================
// SECURITY: Hash password
// ========================================
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ========================================
// DATABASE: Insert new user
// ========================================
try {
    // Prepare SQL insert statement to insert user data into the 'users' table
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, role, created_at) 
        VALUES (?, ?, ?, 'user', NOW())
    ");
    
    // Execute query with user data (username, email, hashed password)
    $stmt->execute([$username, $email, $hashedPassword]);

    // ========================================
    // SUCCESS: User created
    // ========================================
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully! You can now log in.'
    ]);

} catch (PDOException $e) {
    
    // ========================================
    // ERROR HANDLING
    // ========================================
    
    // Check if error is due to duplicate username/email
    // Error code 23000 = Integrity constraint violation (duplicate)
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'message' => 'Username or email already exists'
        ]);
    } else {
        // Other database error
        error_log("Signup error: " . $e->getMessage());  // Log error for debugging
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred. Please try again later.'
        ]);
    }
}
?>

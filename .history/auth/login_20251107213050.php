<?php
header('Content-Type: application/json');

// Start session to track logged-in user
session_start();

// Get JSON data from frontend
$data = json_decode(file_get_contents("php://input"), true);

// Include database connection
require 'db.php';

// Extract fields from incoming data
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

// Validate input: both fields must be filled
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Both username and password are required']);
    exit;
}

// Check credentials against the database
try {
    // Prepare and execute SQL query to get the user record based on username
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the user is found and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        // Store user session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }

} catch (PDOException $e) {
    // Handle any database connection or query errors
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
?>

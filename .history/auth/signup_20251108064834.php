<?php
// Log raw request data for debugging (optional, remove in production)
file_put_contents("debug.txt", file_get_contents("php://input"));

// Set response type to JSON
header('Content-Type: application/json');

// Read incoming JSON data from frontend
$data = json_decode(file_get_contents("php://input"), true);

// Include database connection from db.php
require 'db.php';

// Extract fields from incoming data (username, email, password)
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validate input: all fields must be filled
if (!$username || !$email || !$password) {
  echo json_encode(['success' => false, 'message' => 'All fields are required']);
  exit;
}

// Hash the password securely before saving
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Try inserting the user into the database
try {
  $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  $stmt->execute([$username, $email, $hashedPassword]);

  // Respond with success if insertion is successful
  echo json_encode(['success' => true]);

} catch (PDOException $e) {
  // Handle duplicate username/email or other database errors
  // You can customize the error message based on your needs
  echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
}
?>

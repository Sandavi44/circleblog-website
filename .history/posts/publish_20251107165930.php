<?php
/**
 * ========================================
 * PUBLISH POST HANDLER
 * ========================================
 * 
 * FLOW:
 * 1. Check if user is logged in
 * 2. Verify CSRF token
 * 3. Validate form data
 * 4. Handle image upload
 * 5. Insert post into database
 * 6. Redirect with success message
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// SECURITY: Require user to be logged in
requireLogin();

// ========================================
// CHECK REQUEST METHOD (must be POST)
// ========================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method');
    redirect('../index.php');
}

// ========================================
// VERIFY CSRF TOKEN
// ========================================
$csrfToken = $_POST['csrf_token'] ?? '';

if (!verifyCsrfToken($csrfToken)) {
    setFlashMessage('error', 'Invalid security token. Please try again.');
    redirect('create.php');
}

// ========================================
// GET FORM DATA
// ========================================
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$userId = getCurrentUserId();

// ========================================
// VALIDATION: Check required fields
// ========================================
if (empty($title) || empty($content)) {
    setFlashMessage('error', 'Title and content are required');
    redirect('create.php');
}

// Validate title length
if (strlen($title) > 255) {
    setFlashMessage('error', 'Title is too long (max 255 characters)');
    redirect('create.php');
}

// ========================================
// HANDLE IMAGE UPLOAD
// ========================================
$imagePath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = handleImageUpload($_FILES['image']);
    
    if ($uploadResult['success']) {
        $imagePath = $uploadResult['path'];
    } else {
        setFlashMessage('error', 'Image upload failed: ' . $uploadResult['message']);
        redirect('create.php');
    }
}

// ========================================
// INSERT POST INTO DATABASE
// ========================================
try {
    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, title, content, image, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([
        $userId,
        $title,
        $content,
        $imagePath
    ]);
    
    // Get the ID of the newly created post
    $postId = $pdo->lastInsertId();
    
    // ========================================
    // SUCCESS: Redirect to the new post
    // ========================================
    setFlashMessage('success', 'Blog post published successfully!');
    redirect("view.php?id=$postId");
    
} catch (PDOException $e) {
    
    // ========================================
    // ERROR: Database insertion failed
    // ========================================
    
    // Delete uploaded image if database insert fails
    if ($imagePath) {
        deleteImage($imagePath);
    }
    
    error_log("Post creation error: " . $e->getMessage());
    setFlashMessage('error', 'Failed to create post. Please try again.');
    redirect('create.php');
}
?>

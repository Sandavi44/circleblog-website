<?php
/**
 * ========================================
 * UPDATE POST HANDLER
 * ========================================
 * 
 * FLOW:
 * 1. Verify user is logged in and owns the post
 * 2. Validate CSRF token
 * 3. Validate input data
 * 4. Handle image upload/removal
 * 5. Update post in database
 * 6. Redirect with success message
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// SECURITY: Require login
requireLogin();

// ========================================
// CHECK REQUEST METHOD
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
    setFlashMessage('error', 'Invalid security token');
    redirect('../index.php');
}

// ========================================
// GET FORM DATA
// ========================================
$postId = $_POST['post_id'] ?? null;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$existingImage = $_POST['existing_image'] ?? null;
$removeImage = isset($_POST['remove_image']);

// Validate post ID
if (!$postId || !is_numeric($postId)) {
    setFlashMessage('error', 'Invalid post ID');
    redirect('../index.php');
}

// ========================================
// VERIFY OWNERSHIP
// ========================================
try {
    $stmt = $pdo->prepare("SELECT user_id, image FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        setFlashMessage('error', 'Post not found');
        redirect('../index.php');
    }
    
    if (!isOwner($post['user_id'])) {
        setFlashMessage('error', 'You do not have permission to edit this post');
        redirect("view.php?id=$postId");
    }
    
} catch (PDOException $e) {
    error_log("Error verifying ownership: " . $e->getMessage());
    setFlashMessage('error', 'An error occurred');
    redirect('../index.php');
}

// ========================================
// VALIDATE INPUT
// ========================================
if (empty($title) || empty($content)) {
    setFlashMessage('error', 'Title and content are required');
    redirect("edit.php?id=$postId");
}

if (strlen($title) > 255) {
    setFlashMessage('error', 'Title is too long (max 255 characters)');
    redirect("edit.php?id=$postId");
}

// ========================================
// HANDLE IMAGE LOGIC
// ========================================
$imagePath = $existingImage; // Keep existing by default

// Check if user wants to remove the image
if ($removeImage && !empty($existingImage)) {
    deleteImage($existingImage);
    $imagePath = null;
}

// Check if user uploaded a new image
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = handleImageUpload($_FILES['image']);
    
    if ($uploadResult['success']) {
        // Delete old image if exists
        if (!empty($existingImage)) {
            deleteImage($existingImage);
        }
        $imagePath = $uploadResult['path'];
    } else {
        setFlashMessage('error', 'Image upload failed: ' . $uploadResult['message']);
        redirect("edit.php?id=$postId");
    }
}

// ========================================
// UPDATE POST IN DATABASE
// ========================================
try {
    $stmt = $pdo->prepare("
        UPDATE posts 
        SET title = ?, content = ?, image = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([
        $title,
        $content,
        $imagePath,
        $postId
    ]);
    
    // ========================================
    // SUCCESS
    // ========================================
    setFlashMessage('success', 'Post updated successfully!');
    redirect("view.php?id=$postId");
    
} catch (PDOException $e) {
    
    // ========================================
    // ERROR
    // ========================================
    error_log("Post update error: " . $e->getMessage());
    setFlashMessage('error', 'Failed to update post');
    redirect("edit.php?id=$postId");
}
?>
<?php
/**
 * ========================================
 * DELETE POST HANDLER
 * ========================================
 * 
 * FLOW:
 * 1. Verify user is logged in
 * 2. Check ownership
 * 3. Delete post from database
 * 4. Delete associated image file
 * 5. Redirect with success message
 * 
 * NOTE: Comments and likes are automatically deleted
 * due to CASCADE foreign key constraints
 * ========================================
 */

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// SECURITY: Require login
requireLogin();

// ========================================
// GET POST ID FROM URL
// ========================================
$postId = $_GET['id'] ?? null;

if (!$postId || !is_numeric($postId)) {
    setFlashMessage('error', 'Invalid post ID');
    redirect('../index.php');
}

// ========================================
// FETCH POST AND VERIFY OWNERSHIP
// ========================================
try {
    $stmt = $pdo->prepare("SELECT user_id, image FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        setFlashMessage('error', 'Post not found');
        redirect('../index.php');
    }
    
    // SECURITY: Check ownership
    if (!isOwner($post['user_id'])) {
        setFlashMessage('error', 'You do not have permission to delete this post');
        redirect("view.php?id=$postId");
    }
    
} catch (PDOException $e) {
    error_log("Error fetching post: " . $e->getMessage());
    setFlashMessage('error', 'An error occurred');
    redirect('../index.php');
}

// ========================================
// DELETE POST FROM DATABASE
// ========================================
try {
    // Delete post (comments and likes are auto-deleted via CASCADE)
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    
    // Delete image file if exists
    if (!empty($post['image'])) {
        deleteImage($post['image']);
    }
    
    // ========================================
    // SUCCESS
    // ========================================
    setFlashMessage('success', 'Post deleted successfully');
    redirect('my_posts.php');
    
} catch (PDOException $e) {
    
    // ========================================
    // ERROR
    // ========================================
    error_log("Post deletion error: " . $e->getMessage());
    setFlashMessage('error', 'Failed to delete post');
    redirect("view.php?id=$postId");
}
?>
<?php
/**
 * ========================================
 * DELETE BLOG POST
 * ========================================
 */

define('INCLUDED', true);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Require login
requireLogin();

// Get post ID
$postId = $_GET['id'] ?? 0;

if (!$postId) {
    setFlashMessage('error', 'Post not found');
    redirect('/posts/my_posts.php');
}

// Verify ownership and delete
try {
    // First check if user owns this post
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, getUserId()]);
    $post = $stmt->fetch();
    
    if (!$post) {
        setFlashMessage('error', 'Post not found or you do not have permission to delete it');
        redirect('/posts/my_posts.php');
    }
    
    // Delete the post
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, getUserId()]);
    
    setFlashMessage('success', 'Blog post deleted successfully');
    redirect('/posts/my_posts.php');
    
} catch (PDOException $e) {
    error_log("Delete post error: " . $e->getMessage());
    setFlashMessage('error', 'Failed to delete post. Please try again.');
    redirect('/posts/my_posts.php');
}
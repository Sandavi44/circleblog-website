<?php
/**
 * ========================================
 * COMMENTS API ENDPOINT
 * ========================================
 * 
 * METHODS:
 * - POST: Create new comment
 * - DELETE: Delete existing comment
 * 
 * Returns JSON responses
 * ========================================
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// ========================================
// POST: CREATE COMMENT
// ========================================
if ($method === 'POST') {
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        echo json_encode([
            'success' => false,
            'message' => 'You must be logged in to comment'
        ]);
        exit;
    }
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    $postId = $data['post_id'] ?? null;
    $content = trim($data['content'] ?? '');
    $userId = getCurrentUserId();
    
    // Validate input
    if (!$postId || !is_numeric($postId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid post ID'
        ]);
        exit;
    }
    
    if (empty($content)) {
        echo json_encode([
            'success' => false,
            'message' => 'Comment cannot be empty'
        ]);
        exit;
    }
    
    if (strlen($content) > 1000) {
        echo json_encode([
            'success' => false,
            'message' => 'Comment is too long (max 1000 characters)'
        ]);
        exit;
    }
    
    // Check if post exists
    try {
        $stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        
        if (!$stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Post not found'
            ]);
            exit;
        }
    } catch (PDOException $e) {
        error_log("Error checking post: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred'
        ]);
        exit;
    }
    
    // Insert comment
    try {
        $stmt = $pdo->prepare("
            INSERT INTO comments (post_id, user_id, content, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        
        $stmt->execute([$postId, $userId, $content]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Comment posted successfully',
            'comment_id' => $pdo->lastInsertId()
        ]);
        
    } catch (PDOException $e) {
        error_log("Error creating comment: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Failed to post comment'
        ]);
    }
}

// ========================================
// DELETE: REMOVE COMMENT
// ========================================
elseif ($method === 'DELETE') {
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        echo json_encode([
            'success' => false,
            'message' => 'You must be logged in to delete comments'
        ]);
        exit;
    }
    
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    $commentId = $data['comment_id'] ?? null;
    $userId = getCurrentUserId();
    
    // Validate input
    if (!$commentId || !is_numeric($commentId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid comment ID'
        ]);
        exit;
    }
    
    // Check if comment exists and user owns it
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$comment) {
            echo json_encode([
                'success' => false,
                'message' => 'Comment not found'
            ]);
            exit;
        }
        
        // Check ownership
        if ($comment['user_id'] != $userId) {
            echo json_encode([
                'success' => false,
                'message' => 'You can only delete your own comments'
            ]);
            exit;
        }
        
    } catch (PDOException $e) {
        error_log("Error checking comment: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred'
        ]);
        exit;
    }
    
    // Delete comment
    try {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
        
    } catch (PDOException $e) {
        error_log("Error deleting comment: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete comment'
        ]);
    }
}

// ========================================
// UNSUPPORTED METHOD
// ========================================
else {
    echo json_encode([
        'success' => false,
        'message' => 'Unsupported request method'
    ]);
}
?>
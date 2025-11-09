<?php
/**
 * ========================================
 * REACTIONS (LIKES) API ENDPOINT
 * ========================================
 * 
 * FUNCTIONALITY:
 * - POST: Toggle like/unlike on a post
 * - Returns updated like count and status
 * 
 * LOGIC:
 * - If user hasn't liked: Add like
 * - If user has liked: Remove like
 * ========================================
 */

header('Content-Type: application/json');

// Include required files
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';

// ========================================
// CHECK IF USER IS LOGGED IN
// ========================================
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to like posts'
    ]);
    exit;
}

// ========================================
// GET REQUEST DATA
// ========================================
$data = json_decode(file_get_contents('php://input'), true);

$postId = $data['post_id'] ?? null;
$userId = getCurrentUserId();

// Validate post ID
if (!$postId || !is_numeric($postId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid post ID'
    ]);
    exit;
}

// ========================================
// CHECK IF POST EXISTS
// ========================================
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

// ========================================
// CHECK IF USER ALREADY LIKED THIS POST
// ========================================
try {
    $stmt = $pdo->prepare("
        SELECT id FROM likes 
        WHERE post_id = ? AND user_id = ?
    ");
    $stmt->execute([$postId, $userId]);
    $existingLike = $stmt->fetch();
    
} catch (PDOException $e) {
    error_log("Error checking like status: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
    exit;
}

// ========================================
// TOGGLE LIKE/UNLIKE
// ========================================
try {
    
    if ($existingLike) {
        // ========================================
        // UNLIKE: Remove existing like
        // ========================================
        $stmt = $pdo->prepare("
            DELETE FROM likes 
            WHERE post_id = ? AND user_id = ?
        ");
        $stmt->execute([$postId, $userId]);
        
        $liked = false;
        $action = 'unliked';
        
    } else {
        // ========================================
        // LIKE: Add new like
        // ========================================
        $stmt = $pdo->prepare("
            INSERT INTO likes (post_id, user_id, reaction_type, created_at)
            VALUES (?, ?, 'like', NOW())
        ");
        $stmt->execute([$postId, $userId]);
        
        $liked = true;
        $action = 'liked';
    }
    
    // ========================================
    // GET UPDATED LIKE COUNT
    // ========================================
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM likes 
        WHERE post_id = ?
    ");
    $stmt->execute([$postId]);
    $result = $stmt->fetch();
    $likeCount = $result['count'];
    
    // ========================================
    // RETURN SUCCESS RESPONSE
    // ========================================
    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'action' => $action,
        'like_count' => $likeCount,
        'message' => $liked ? 'Post liked!' : 'Post unliked'
    ]);
    
} catch (PDOException $e) {
    
    // ========================================
    // ERROR HANDLING
    // ========================================
    
    // Check if error is due to duplicate like attempt
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'message' => 'You have already liked this post'
        ]);
    } else {
        error_log("Reaction error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update reaction'
        ]);
    }
}
?>
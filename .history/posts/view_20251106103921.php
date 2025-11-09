<?php
// Start session to check user authentication
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit;
}

include('db.php');  // Include the database connection file

// Get the post ID from the URL (via GET)
$post_id = $_GET['id'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link rel="stylesheet" href="view_post.css">  
</head>
<body>

<?php
if ($post_id) {
    // Fetch the post details based on the ID
    $query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Wrap the post content in a container
        echo "<div class='post-container'>";
        
        // Display the full post
        echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";
        echo "<p><strong>Published on:</strong> " . $post['created_at'] . "</p>";
        
        // Display the content
        echo "<div class='content'>" . nl2br(htmlspecialchars($post['content'])) . "</div>";

        // Display image if it exists
        if ($post['image']) {
            echo "<img src='" . $post['image'] . "' alt='Post Image' />";
        }

        // Close the post container
        echo "</div>"; 
    } else {
        echo "<p>Post not found or you do not have permission to view this post.</p>";
    }
} else {
    echo "<p>Invalid post ID.</p>";
}
?>

</body>
</html>

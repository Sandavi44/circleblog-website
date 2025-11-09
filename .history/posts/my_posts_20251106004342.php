<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

include('db.php'); // Include the database connection file

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch the user's posts from the database
$query = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>My Blogs</h1>";

if (count($posts) > 0) {
    foreach ($posts as $post) {
        echo "<div class='post-preview'>";
        echo "<h2><a href='view_post.php?id=" . $post['id'] . "'>" . htmlspecialchars($post['title']) . "</a></h2>";
        echo "<p><strong>Published on:</strong> " . $post['created_at'] . "</p>";
        echo "<p>" . substr($post['content'], 0, 150) . "...</p>"; // Display preview of the content
        echo "</div>";
    }
} else {
    echo "<p>You haven't published any posts yet.</p>";
}
?>

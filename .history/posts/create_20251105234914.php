<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post</title>
</head>
<body>
    <h1>Create a New Blog Post</h1>
    <form action="publish_blog.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="10" cols="50" required></textarea><br><br>

        <label for="image">Image (optional):</label><br>
        <input type="file" id="image" name="image"><br><br>

        <button type="submit">Publish Blog</button>
    </form>
</body>
</html>

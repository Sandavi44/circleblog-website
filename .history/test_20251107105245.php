<?php
define('INCLUDED', true);
require_once 'config/db.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

echo "<h1>✅ Configuration Test (Updated Paths)</h1>";
echo "<p>✅ Database connected successfully!</p>";
echo "<p>✅ Session started!</p>";
echo "<p>✅ Base URL: " . getBaseUrl() . "</p>";

// Test query
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$result = $stmt->fetch();
echo "<p>✅ Database has {$result['count']} users</p>";

// Test posts query
$stmt = $pdo->query("SELECT COUNT(*) as count FROM posts");
$result = $stmt->fetch();
echo "<p>✅ Database has {$result['count']} blog posts</p>";

echo "<hr>";
echo "<h2>✅ All systems working!</h2>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";
?>
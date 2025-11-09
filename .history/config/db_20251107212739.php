<?php
/**
 * ========================================
 * DATABASE CONNECTION (Reads from .env)
 * ========================================
 */

// Prevent direct access
if (!defined('INCLUDED')) {
    define('INCLUDED', true);
}

// Start output buffering
ob_start();

/**
 * Load environment variables from .env file
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        die("❌ ERROR: .env file not found. Please create it in the root directory.");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');

            // Store environment variables in PHP's $_ENV and putenv
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Load .env from root directory
loadEnv(__DIR__ . '/../.env');

// Get database credentials
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'circle-blog';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

// Create PDO connection
try {
    // Ensure we're using secure UTF-8mb4 character set to handle multi-byte characters
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable error handling via exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Default fetch mode to associative arrays
            PDO::ATTR_EMULATE_PREPARES => false // Disable emulation for prepared statements
        ]
    );
} catch (PDOException $e) {
    $env = getenv('APP_ENV') ?: 'development';
    
    // Check if we're in development mode to provide detailed error messages
    if ($env === 'development') {
        die("❌ Database Connection Failed!<br><br>
             <strong>Error:</strong> " . $e->getMessage() . "<br><br>
             <strong>Check:</strong><br>
             1. XAMPP/MySQL is running<br>
             2. .env credentials are correct<br>
             3. Database 'circle-blog' exists");
    } else {
        // Log error for production (sensitive information not shown)
        error_log("Database Error: " . $e->getMessage());
        die("❌ Technical difficulties. Please try again later.");
    }
}

?>

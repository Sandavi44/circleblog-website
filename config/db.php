<?php
/**
 * ========================================
 * DATABASE CONNECTION (Reads from .env)
 * ========================================
 * 
 * This file:
 * 1. Loads environment variables from .env file
 * 2. Creates a PDO database connection
 * 3. Sets up error handling
 * 
 * WHY PDO?
 * - Secure (prevents SQL injection)
 * - Modern (better than mysqli)
 * - Supports prepared statements
 * ========================================
 */

// Start output buffering to prevent header issues
ob_start();

// ========================================
// LOAD ENVIRONMENT VARIABLES FROM .ENV
// ========================================

/**
 * This function reads the .env file and loads variables
 * into PHP's environment ($_ENV)
 * 
 * EXPLANATION:
 * - file() reads .env line by line
 * - We skip empty lines and comments (#)
 * - explode('=', $line, 2) splits "KEY=value" into array
 * - putenv() makes it available to the script
 */
function loadEnv($path) {
    // Check if .env file exists
    if (!file_exists($path)) {
        die("❌ ERROR: .env file not found at: $path<br>Please create .env file in the root directory.");
    }

    // Read .env file line by line
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments (lines starting with #)
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split line into KEY and VALUE
        // explode('=', $line, 2) means: split only at FIRST '=' sign
        // Example: "DB_PASSWORD=my=pass=word" becomes ["DB_PASSWORD", "my=pass=word"]
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            
            // Trim whitespace and remove quotes
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\''); // Remove surrounding quotes
            
            // Set environment variable
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Load the .env file from root directory
// __DIR__ is the current directory (config/)
// .. goes up one level to root
loadEnv(__DIR__ . '/../.env');

// ========================================
// GET DATABASE CREDENTIALS FROM ENVIRONMENT
// ========================================

// getenv() retrieves values we loaded from .env
$host = getenv('DB_HOST') ?: 'localhost';           // Database host
$dbname = getenv('DB_NAME') ?: 'circle-blog';       // Database name
$username = getenv('DB_USERNAME') ?: 'root';        // Database username
$password = getenv('DB_PASSWORD') ?: '';            // Database password

// ========================================
// CREATE PDO DATABASE CONNECTION
// ========================================

try {
    /**
     * PDO (PHP Data Objects) Connection
     * 
     * Syntax: new PDO("mysql:host=HOST;dbname=DBNAME", USERNAME, PASSWORD)
     * 
     * OPTIONS explained:
     * - ATTR_ERRMODE: Set error reporting mode to EXCEPTION (throws errors)
     * - ATTR_DEFAULT_FETCH_MODE: Return results as associative arrays
     * - ATTR_EMULATE_PREPARES: Use real prepared statements (more secure)
     */
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // ========================================
    // CONNECTION SUCCESSFUL
    // ========================================
    // Uncomment the line below for testing (remove in production)
    // echo "✅ Database connected successfully!<br>";

} catch (PDOException $e) {
    // ========================================
    // CONNECTION FAILED
    // ========================================
    
    /**
     * ERROR HANDLING
     * 
     * In development: Show detailed error
     * In production: Log error, show generic message
     */
    
    $env = getenv('APP_ENV') ?: 'development';
    
    if ($env === 'development') {
        // Development mode: Show detailed error
        die("❌ Database Connection Failed!<br><br>
             <strong>Error:</strong> " . $e->getMessage() . "<br><br>
             <strong>Troubleshooting:</strong><br>
             1. Check if XAMPP/MySQL is running<br>
             2. Verify .env file has correct credentials<br>
             3. Ensure database 'circle-blog' exists<br>");
    } else {
        // Production mode: Log error, show generic message
        error_log("Database Error: " . $e->getMessage());
        die("❌ Sorry, we're experiencing technical difficulties. Please try again later.");
    }
}

/**
 * ========================================
 * HOW TO USE THIS FILE
 * ========================================
 * 
 * In any PHP file that needs database access:
 * 
 * require_once __DIR__ . '/../config/db.php';
 * 
 * Then you can use $pdo to run queries:
 * 
 * Example 1: Fetch all posts
 * $stmt = $pdo->query("SELECT * FROM posts");
 * $posts = $stmt->fetchAll();
 * 
 * Example 2: Insert with prepared statement (secure!)
 * $stmt = $pdo->prepare("INSERT INTO users (username, email) VALUES (?, ?)");
 * $stmt->execute(['john', 'john@example.com']);
 * 
 * ========================================
 */
?>
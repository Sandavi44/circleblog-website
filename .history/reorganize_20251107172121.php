<?php
// Start the session to access session variables (if needed)
session_start();

// Define constant to prevent direct access
define('INCLUDED', true);

// Include the necessary files
require_once 'config/db.php';
require_once 'config/session.php';
require_once 'includes/functions.php';

echo "<h1>Circle Blog - File Reorganization</h1>";
echo "<pre>";

// Get the root directory
$rootDir = __DIR__;

// ========================================
// STEP 1: CREATE FOLDER STRUCTURE
// ========================================
echo "📁 Creating folder structure...\n\n";

$folders = [
    'config',
    'includes',
    'auth',
    'posts',
    'api',
    'assets',
    'assets/css',
    'assets/js',
    'assets/images',
    'uploads',
    'backup_old_files'  // Backup folder for safety
];

foreach ($folders as $folder) {
    $path = $rootDir . '/' . $folder;
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
        echo "✅ Created: $folder\n";
    } else {
        echo "⏭️  Exists: $folder\n";
    }
}

echo "\n";

// ========================================
// STEP 2: MOVE FILES TO CORRECT LOCATIONS
// ========================================
echo "📦 Moving files to correct locations...\n\n";

/**
 * File mapping: [old_location => new_location]
 */
$fileMap = [
    // CSS files
    'style.css' => 'assets/css/style.css',
    'view_post.css' => 'assets/css/view_post.css',
    
    // JS files
    'script.js' => 'assets/js/main.js',
    'auth.js' => 'assets/js/auth.js',
    
    // Auth files
    'login.php' => 'auth/login.php',
    'signup.php' => 'auth/signup.php',
    'logout.php' => 'auth/logout.php',
    
    // Post files
    'create_post.php' => 'posts/create.php',
    'publish_post.php' => 'posts/publish.php',
    'my_posts.php' => 'posts/my_posts.php',
    'view_post.php' => 'posts/view.php',
    
    // Old config files (backup only, we'll create new ones)
    'db.php' => 'backup_old_files/db.php',
    'config.php' => 'backup_old_files/config.php',
];

foreach ($fileMap as $oldPath => $newPath) {
    $oldFile = $rootDir . '/' . $oldPath;
    $newFile = $rootDir . '/' . $newPath;
    
    if (file_exists($oldFile)) {
        // Create directory if it doesn't exist
        $newDir = dirname($newFile);
        if (!file_exists($newDir)) {
            mkdir($newDir, 0755, true);
        }
        
        // Move file
        if (rename($oldFile, $newFile)) {
            echo "✅ Moved: $oldPath → $newPath\n";
        } else {
            echo "❌ Failed to move: $oldPath\n";
        }
    } else {
        echo "⏭️  Not found: $oldPath (skipping)\n";
    }
}

echo "\n";

// ========================================
// STEP 3: CREATE .htaccess FOR UPLOADS FOLDER
// ========================================
echo "🔒 Creating security files...\n\n";

$htaccessContent = <<<'HTACCESS'
# Prevent PHP execution in uploads folder
<FilesMatch "\.php$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Allow only image files
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>
HTACCESS;

$htaccessFile = $rootDir . '/uploads/.htaccess';
if (file_put_contents($htaccessFile, $htaccessContent)) {
    echo "✅ Created: uploads/.htaccess (security)\n";
} else {
    echo "❌ Failed to create .htaccess\n";
}

// Create README in uploads
$uploadsReadme = "# Uploads Folder\n\nThis folder contains user-uploaded images.\n\nDo NOT commit this folder to GitHub.\n";
file_put_contents($rootDir . '/uploads/README.md', $uploadsReadme);
echo "✅ Created: uploads/README.md\n";

echo "\n";

// ========================================
// STEP 4: UPDATE index.html TO index.php
// ========================================
echo "🔄 Converting index.html to index.php...\n\n";

if (file_exists($rootDir . '/index.html')) {
    rename($rootDir . '/index.html', $rootDir . '/index.php');
    echo "✅ Renamed: index.html → index.php\n";
}

echo "\n";

// ========================================
// SUMMARY
// ========================================
echo "========================================\n";
echo "✅ REORGANIZATION COMPLETE!\n";
echo "========================================\n\n";

echo "📋 Next Steps:\n";
echo "1. Create .env file in root folder\n";
echo "2. Create .gitignore file in root folder\n";
echo "3. Create config/db.php (I'll provide code)\n";
echo "4. Create config/session.php (I'll provide code)\n";
echo "5. Update file paths in your code\n\n";

echo "📁 New Structure:\n";
echo "circle-blog/\n";
echo "├── config/           (database, session)\n";
echo "├── includes/         (reusable components)\n";
echo "├── auth/             (login, signup, logout)\n";
echo "├── posts/            (create, edit, delete, view)\n";
echo "├── api/              (AJAX endpoints)\n";
echo "├── assets/           (css, js, images)\n";
echo "│   ├── css/\n";
echo "│   ├── js/\n";
echo "│   └── images/\n";
echo "├── uploads/          (user uploaded files)\n";
echo "├── .env              (YOU NEED TO CREATE THIS)\n";
echo "├── .gitignore        (YOU NEED TO CREATE THIS)\n";
echo "└── index.php         (home page)\n\n";

echo "⚠️  IMPORTANT:\n";
echo "- Your old files are backed up in: backup_old_files/\n";
echo "- Delete this reorganize.php file after you're done\n";
echo "- Continue with Claude's instructions to create remaining files\n\n";

echo "</pre>";
?>

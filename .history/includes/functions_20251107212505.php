<?php
/**
 * ========================================
 * HELPER FUNCTIONS
 * ========================================
 * Reusable utility functions for the entire application
 */

/**
 * Sanitize user input to prevent XSS attacks
 * 
 * @param string $data - Input data to sanitize
 * @return string - Sanitized data
 */
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 * 
 * @param string $email - Email to validate
 * @return bool - True if valid, false otherwise
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Handle file upload for blog post images
 * 
 * @param array $file - $_FILES['image']
 * @return array - ['success' => bool, 'message' => string, 'path' => string|null]
 */
function handleImageUpload($file) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'message' => 'No file uploaded', 'path' => null];
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error', 'path' => null];
    }

    // Get file info
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Allowed file types
    $allowedTypes = explode(',', getenv('ALLOWED_FILE_TYPES') ?: 'jpg,jpeg,png,gif,webp');
    $maxSize = (int)(getenv('MAX_UPLOAD_SIZE') ?: 5242880); // 5MB default

    // Validate file extension
    if (!in_array($fileExt, $allowedTypes)) {
        return [
            'success' => false, 
            'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes),
            'path' => null
        ];
    }

    // Validate file size
    if ($fileSize > $maxSize) {
        return [
            'success' => false,
            'message' => 'File too large. Max size: ' . ($maxSize / 1024 / 1024) . 'MB',
            'path' => null
        ];
    }

    // Generate unique filename
    $newFileName = uniqid('post_', true) . '.' . $fileExt;
    $uploadDir = __DIR__ . '/../uploads/';
    $uploadPath = $uploadDir . $newFileName;

    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        return [
            'success' => true,
            'message' => 'File uploaded successfully',
            'path' => 'uploads/' . $newFileName
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to move uploaded file',
            'path' => null
        ];
    }
}

/**
 * Delete uploaded image file
 * 
 * @param string $imagePath - Path to image file (e.g., 'uploads/image.jpg')
 * @return bool - True if deleted, false otherwise
 */
function deleteImage($imagePath) {
    if (empty($imagePath)) {
        return false;
    }

    $fullPath = __DIR__ . '/../' . $imagePath;
    
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    
    return false;
}

/**
 * Format date to human-readable format
 * 
 * @param string $datetime - MySQL datetime string
 * @return string - Formatted date
 */
function formatDate($datetime) {
    return date('F j, Y, g:i a', strtotime($datetime));
}

/**
 * Get time ago (e.g., "2 hours ago")
 * 
 * @param string $datetime - MySQL datetime string
 * @return string - Time ago string
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

/**
 * Truncate text to specified length
 * 
 * @param string $text - Text to truncate
 * @param int $length - Maximum length
 * @return string - Truncated text with ellipsis
 */
function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . '...';
}

/**
 * Redirect to a URL
 * 
 * @param string $url - URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Generate CSRF token for forms
 * 
 * @return string - CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token - Token to verify
 * @return bool - True if valid, false otherwise
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get base URL of the application
 * 
 * @return string - Base URL
 */
function getBaseUrl() {
    return getenv('APP_URL') ?: 'http://localhost/circle-blog';
}

/**
 * Convert Markdown to HTML (basic implementation)
 * For full Markdown support, use a library like Parsedown
 * 
 * @param string $markdown - Markdown text
 * @return string - HTML output
 */
function markdownToHtml($markdown) {
    // Basic conversions (you can add more or use a library)
    $html = $markdown;
    
    // Headers
    $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
    
    // Bold
    $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
    
    // Italic
    $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
    
    // Links
    $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $html);
    
    // Line breaks
    $html = nl2br($html);
    
    return $html;
}
?>

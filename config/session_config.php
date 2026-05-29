<?php
/**
 * Session Configuration Handler
 * Manages session lifecycle, cookie duration, and inactivity timeout
 */

// Set session configuration BEFORE session_start()
if (session_status() === PHP_SESSION_NONE) {
    // Cookie will last 7 days
    ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
    // Session garbage collection: 1 hour
    ini_set('session.gc_maxlifetime', 60 * 60);
    // Secure session settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
}

// ========================================
// INACTIVITY TIMEOUT CHECK (5-10 minutes)
// ========================================
define('INACTIVITY_TIMEOUT', 600); // 10 minutes in seconds

if (isset($_SESSION['current_user'])) {
    // Check if last_activity is set
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }
    
    // Check if user has been inactive
    $inactivityTime = time() - $_SESSION['last_activity'];
    
    if ($inactivityTime > INACTIVITY_TIMEOUT) {
        // User is inactive, destroy session
        session_destroy();
        $_SESSION = [];
        
        // Clear user session cookie
        setcookie('user_session', '', time() - 3600, '/');
        
        // Redirect to login with timeout message
        header("Location: " . getBaseUrl() . "index.php?session_expired=inactivity");
        exit();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

// ========================================
// COOKIE & SESSION EXPIRATION CHECK
// ========================================
if (isset($_SESSION['current_user']) && !isset($_COOKIE['user_session'])) {
    // User exists in session but cookie was cleared/expired
    session_destroy();
    $_SESSION = [];
    header("Location: " . getBaseUrl() . "index.php?session_expired=true");
    exit();
}

// ========================================
// CART INITIALIZATION
// ========================================
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ========================================
// HELPER FUNCTION: Get base URL
// ========================================
function getBaseUrl() {
    $path = dirname($_SERVER['PHP_SELF']);
    if ($path === '/' || $path === '\\') {
        return '/Tagpo/';
    }
    
    // Remove /config from path if present
    if (strpos($path, '/config') !== false) {
        $path = str_replace('/config', '', $path);
    }
    
    return rtrim($path, '/') . '/';
}

// ========================================
// HELPER FUNCTION: Check if user is logged in
// ========================================
function isLoggedIn() {
    return isset($_SESSION['current_user']) && !empty($_SESSION['current_user']);
}

// ========================================
// HELPER FUNCTION: Get current user
// ========================================
function getCurrentUser() {
    return $_SESSION['current_user'] ?? null;
}

// ========================================
// HELPER FUNCTION: Is admin
// ========================================
function isAdmin() {
    $user = getCurrentUser();
    return $user && isset($user['role']) && $user['role'] === 'admin';
}

// ========================================
// HELPER FUNCTION: Get cart count
// ========================================
function getCartCount() {
    return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}
?>

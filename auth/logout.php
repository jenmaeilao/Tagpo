<?php
session_start();

// Clear session array
$_SESSION = [];

// Destroy session
session_destroy();

// Remove session cookie (important)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Clear your custom cookie
setcookie('user_session', '', time() - 3600, '/');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
</head>
<body>
    <script>
        localStorage.removeItem('tagpo_wishlist');
        window.location.href = './login.php?status=logged_out';
    </script>
</body>
</html>
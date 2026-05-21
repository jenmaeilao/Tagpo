<?php
session_start();
$baseUrl = '../';
session_destroy();

// Clear the user_session cookie
setcookie('user_session', '', time() - 3600, '/');
unset($_COOKIE['user_session']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
</head>
<body>
    <script>
        // Clear wishlist from localStorage
        localStorage.removeItem('tagpo_wishlist');
        
        // Redirect to login
        window.location.href = './login.php?status=logged_out';
    </script>
</body>
</html>
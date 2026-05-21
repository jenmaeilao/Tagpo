<?php
session_start();

$baseUrl = '../';

// Check if user session expired (no cookie but had session)
if (!isset($_COOKIE['user_session']) && isset($_SESSION['current_user'])) {
    session_destroy();
    $_SESSION = [];
    header("Location: ../index.php?expired=true");
    exit();
}

if (isset($_GET['id'])) {
    $id_to_delete = $_GET['id'];

    if (isset($_SESSION['venues'])) {
        foreach ($_SESSION['venues'] as $key => $venue) {
            // Kung nag-match yung ID, tatanggalin sa array
            if ($venue['id'] == $id_to_delete) {
                unset($_SESSION['venues'][$key]);
            }
        }
        // Re-index ang array para walang butas na index
        $_SESSION['venues'] = array_values($_SESSION['venues']);
    }
}

// Balik agad sa index pagkatapos i-delete
header("Location: ../index.php");
exit;
?>
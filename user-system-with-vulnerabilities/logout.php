<?php
// logout.php - Secure logout
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION = [];

    session_destroy();

    header("Location: index.php");
    exit;
}

header("Location: index.php");
exit;
?>

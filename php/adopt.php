<?php
// Start the session to use session variables
session_start();

// Check if user is logging out
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header('Location: index.php'); // Redirect to the home page
    exit();
}

// Initialize variables
$logged_in = false;
$role = '';

// Check if user is logged in
if (isset($_SESSION['username'])) {
    $logged_in = true;
    $role = $_SESSION['role']; // Get user role from session
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>PetPals |Adopt</title>
    </head>
    <body>
        
    </body>
</html>
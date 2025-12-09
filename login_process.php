<?php
session_start();

// Get login form data
$username = $_POST['username']; // can be student ID or email
$password = $_POST['password'];

// Load users from JSON
$usersFile = 'users.json';
$usersJson = file_get_contents($usersFile);
$users = json_decode($usersJson, true);

// Look for matching user
foreach ($users as $user) {
    if ($user['student_id'] === $username || $user['email'] === $username) {
        
        // Verify password
        if (password_verify($password, $user['password'])) {

            // Store session info
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['student_name'] = $user['first_name'];
            $_SESSION['email'] = $user['email'];

            header("Location: dashboard.php");
            exit();
        }
    }
}

// If no match → login failed
header("Location: login_new.php?error=1");
exit();
?>
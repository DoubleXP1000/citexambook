<?php
// Load form data
$fname  = $_POST['fname'];
$lname  = $_POST['lname'];
$stdid  = $_POST['stdid'];
$email  = $_POST['email'];
$course = $_POST['course'];
$pass   = $_POST['password'];
$cpass  = $_POST['cpassword'];

// Password match check
if ($pass !== $cpass) {
    die("Passwords do not match.");
}

// Load existing JSON users
$userFile = 'users.json';

if (!file_exists($userFile)) {
    file_put_contents($userFile, "[]");
}

$users = json_decode(file_get_contents($userFile), true);

// CHECK IF STUDENT ID ALREADY EXISTS
foreach ($users as $user) {
    if ($user["student_id"] === $stdid) {
        header("Location: new_signup_page.php?error=duplicate");
        exit();
    }
}

// Hash password
$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

// Create new record
$newUser = [
    "first_name" => $fname,
    "last_name" => $lname,
    "student_id" => $stdid,
    "email" => $email,
    "course" => $course,
    "password" => $hashed_pass
];

// Add new user to array
$users[] = $newUser;

// Save back to JSON file
file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT));

// Redirect to login page with success message
header("Location: login_new.php?success=1");
exit();
?>
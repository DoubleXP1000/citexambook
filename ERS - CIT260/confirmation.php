<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login_new.php?error=notloggedin");
    exit();
}

// Read latest registration (last item in JSON file)
$regFile = "exam_reg.json";
$registrations = json_decode(file_get_contents($regFile), true);

// Get last record
$latest = end($registrations);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Complete</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="container">
    <main class="form-container">

        <h1>🎉Registration Successful!</h1>
        <h2>Exam Registration Summary</h2>

        <div class="summary-box">
            <p><strong>Student ID:</strong> <?= htmlspecialchars($latest["student_id"]) ?></p>
            <p><strong>Course Name:</strong> <?= htmlspecialchars($latest["course_name"]) ?></p>
            <p><strong>Course Code:</strong> <?= htmlspecialchars($latest["course_code"]) ?></p>
            <p><strong>Campus Location:</strong> <?= htmlspecialchars($latest["exam_location"]) ?></p>
            <p><strong>Test Room:</strong> <?= htmlspecialchars($latest["test_room"]) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($latest["exam_date"]) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($latest["exam_time"]) ?></p>
        </div>

        <div class="button-group">
            <button onclick="window.location.href='register_exam.php'" class="btn">Register Another Exam</button>
            <button onclick="window.location.href='dashboard.php'" class="btn">Dashboard</button>
            <button onclick="window.location.href='logout.php'" class="btn btn-secondary">Log Out</button>
        </div>

    </main>
</div>
</body>
</html>
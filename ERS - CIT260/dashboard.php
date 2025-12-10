<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login_new.php?error=notloggedin");
    exit();
}

$studentID = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'];

$regFile = "exam_reg.json";

if (!file_exists($regFile)) {
    file_put_contents($regFile, "[]");
}

$registrations = json_decode(file_get_contents($regFile), true);

if (!is_array($registrations)) {
    $registrations = [];
}

// Filter exams belonging to this student
$myExams = array_values(array_filter($registrations, function($exam) use ($studentID) {
    return $exam["student_id"] == $studentID;
}));

// Sort by date
usort($myExams, function($a, $b) {
    return strtotime($a["exam_date"]) - strtotime($b["exam_date"]);
});
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
<div class="container">
    <main class="form-container">

        <h1>📚 Student Dashboard</h1>

        <h2>Welcome, <?= htmlspecialchars($studentName) ?>!</h2>
        <p><strong>NSHE ID:</strong> <?= htmlspecialchars($studentID) ?></p>

        <?php if (isset($_GET['cancelled'])): ?>
            <p style="color: green; font-weight: bold;">✔ Exam cancelled successfully.</p>
        <?php endif; ?>

        <?php if (isset($_GET['rescheduled'])): ?>
            <p style="color: green; font-weight: bold;">✔ Exam rescheduled successfully.</p>
        <?php endif; ?>

        <hr>

        <h2>Your Registered Exams</h2>

        <?php if (empty($myExams)): ?>
            <p>You have no registered exams yet.</p>

        <?php else: ?>

            <table class="exam-table">
                <tr>
                    <th>Course</th>
                    <th>Code</th>
                    <th>Location</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reschedule</th>
                    <th>Cancel</th>
                </tr>

                <?php foreach ($myExams as $index => $exam): 
                    $examID = $index;
                ?>
                <tr>
                    <td><?= htmlspecialchars($exam["course_name"]) ?></td>
                    <td><?= htmlspecialchars($exam["course_code"]) ?></td>
                    <td><?= htmlspecialchars($exam["exam_location"]) ?></td>
                    <td><?= htmlspecialchars($exam["test_room"]) ?></td>
                    <td><?= htmlspecialchars($exam["exam_date"]) ?></td>
                    <td><?= htmlspecialchars($exam["exam_time"]) ?></td>

                    <td>
                        <a class="btn-small" href="reschedule.php?id=<?= $examID ?>">Reschedule</a>
                    </td>

                    <td>
                        <a class="btn-small btn-danger"
                           href="cancel.php?id=<?= $examID ?>"
                           onclick="return confirm('Are you sure you want to cancel this exam?');">
                           Cancel
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>

            </table>

        <?php endif; ?>

        <div class="button-group">
            <button onclick="window.location.href='register_exam.php'" class="btn">Register New Exam</button>
            <button onclick="window.location.href='logout.php'" class="btn btn-secondary">Log Out</button>
        </div>

    </main>
</div>

</body>
</html>
<?php
session_start();

// Require login
if (!isset($_SESSION['student_id'])) {
    header("Location: login_new.php?error=notloggedin");
    exit();
}

$studentID = $_SESSION["student_id"];

if (
    !isset($_POST["student_id"]) ||
    !isset($_POST["course_name"]) ||
    !isset($_POST["course_code"]) ||
    !isset($_POST["exam_location"]) ||
    !isset($_POST["test_room"]) ||
    !isset($_POST["exam_date"]) ||
    !isset($_POST["exam_time"])
) {
    header("Location: register_exam.php?error=invalid");
    exit();
}

$studentID = $_POST["student_id"];
$courseName = $_POST["course_name"];
$courseCode = $_POST["course_code"];
$location = $_POST["exam_location"];
$room = $_POST["test_room"];
$examDate = $_POST["exam_date"];
$examTime = $_POST["exam_time"];

$regFile = "exam_reg.json";

// Load existing registrations
if (!file_exists($regFile)) {
    file_put_contents($regFile, "[]");
}

$data = json_decode(file_get_contents($regFile), true);
if (!is_array($data)) {
    $data = [];
}

// ------------------------------------------------------------
// 1. Prevent duplicate exam (same student, same course)
// ------------------------------------------------------------
foreach ($data as $exam) {
    if ($exam["student_id"] == $studentID &&
        $exam["course_name"] == $courseName &&
        $exam["course_code"] == $courseCode
    ) {
        header("Location: register_exam.php?error=duplicate_exam");
        exit();
    }
}

// ------------------------------------------------------------
// 2. Enforce max 3 exam registrations per student
// ------------------------------------------------------------
$studentExamCount = 0;
foreach ($data as $exam) {
    if ($exam["student_id"] == $studentID) {
        $studentExamCount++;
    }
}

if ($studentExamCount >= 3) {
    header("Location: register_exam.php?error=max_reached");
    exit();
}

// ------------------------------------------------------------
// 3. Ensure capacity: max 20 students per time slot
// ------------------------------------------------------------
$capacity = 0;

foreach ($data as $exam) {
    if ($exam["exam_date"] == $examDate &&
        $exam["exam_time"] == $examTime &&
        $exam["exam_location"] == $location
    ) {
        $capacity++;
    }
}

if ($capacity >= 20) {
    header("Location: register_exam.php?error=full_capacity");
    exit();
}

// ------------------------------------------------------------
// 4. Save the new exam registration
// ------------------------------------------------------------
$newEntry = [
    "student_id" => $studentID,
    "course_name" => $courseName,
    "course_code" => $courseCode,
    "exam_location" => $location,
    "test_room" => $room,
    "exam_date" => $examDate,
    "exam_time" => $examTime
];

$data[] = $newEntry;

// Save file
file_put_contents($regFile, json_encode($data, JSON_PRETTY_PRINT));

// Redirect to dashboard
header("Location: dashboard.php?registered=1");
exit();
?>
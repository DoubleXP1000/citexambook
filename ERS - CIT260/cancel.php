<?php
session_start();

// Must be logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login_new.php?error=notloggedin");
    exit();
}

$studentID = $_SESSION["student_id"];

// Check incoming exam index
if (!isset($_GET["id"])) {
    header("Location: dashboard.php?error=invalid");
    exit();
}

$studentExamIndex = intval($_GET["id"]); // The index from dashboard

$regFile = "exam_reg.json";

// Ensure file exists
if (!file_exists($regFile)) {
    file_put_contents($regFile, "[]");
}

$data = json_decode(file_get_contents($regFile), true);

// Ensure valid array
if (!is_array($data)) {
    $data = [];
}

// Step 1: Build list of this student's exams WITH their global JSON indexes
$studentExams = [];
foreach ($data as $globalIndex => $exam) {
    if ($exam["student_id"] == $studentID) {
        $studentExams[] = $globalIndex;
    }
}

// Step 2: Check if the index exists for this student
if (!isset($studentExams[$studentExamIndex])) {
    header("Location: dashboard.php?error=notfound");
    exit();
}

// Step 3: Get the actual JSON index to remove
$globalIndexToDelete = $studentExams[$studentExamIndex];

// Remove that exam
unset($data[$globalIndexToDelete]);

// Re-index JSON array
$data = array_values($data);

// Save back to file
file_put_contents($regFile, json_encode($data, JSON_PRETTY_PRINT));

// Redirect to dashboard
header("Location: dashboard.php?cancelled=1");
exit();
?>
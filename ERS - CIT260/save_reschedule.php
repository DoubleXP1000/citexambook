<?php
session_start();

// Must be logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login_new.php?error=notloggedin");
    exit();
}

$studentID = $_SESSION["student_id"];

// Validate required POST fields
if (
    !isset($_POST["global_index"]) ||
    !isset($_POST["exam_date"]) ||
    !isset($_POST["exam_time"])
) {
    header("Location: dashboard.php?error=invalid");
    exit();
}

$globalIndex = intval($_POST["global_index"]);
$newDate = $_POST["exam_date"];
$newTime = $_POST["exam_time"];

$regFile = "exam_reg.json";

if (!file_exists($regFile)) {
    file_put_contents($regFile, "[]");
}

$data = json_decode(file_get_contents($regFile), true);

// Ensure valid array
if (!is_array($data)) {
    $data = [];
}

if (!isset($data[$globalIndex])) {
    header("Location: dashboard.php?error=notfound");
    exit();
}

// Current exam being updated
$exam = $data[$globalIndex];

// ------------------------------------------------------
// 1. Prevent duplicate registration of same exact session
// ------------------------------------------------------
foreach ($data as $i => $ex) {
    if (
        $i != $globalIndex &&
        $ex["student_id"] === $studentID &&
        $ex["exam_date"] === $newDate &&
        $ex["exam_time"] === $newTime
    ) {
        header("Location: dashboard.php?error=duplicate_exam");
        exit();
    }
}

// ------------------------------------------------------
// 2. Enforce capacity — max 20 per exam session
// ------------------------------------------------------
$capacityCount = 0;

foreach ($data as $ex) {
    if (
        $ex["exam_date"] === $newDate &&
        $ex["exam_time"] === $newTime
    ) {
        $capacityCount++;
    }
}

if ($capacityCount >= 20) {
    header("Location: dashboard.php?error=full_capacity");
    exit();
}

// ------------------------------------------------------
// 3. Update the record
// ------------------------------------------------------
$data[$globalIndex]["exam_date"] = $newDate;
$data[$globalIndex]["exam_time"] = $newTime;

// Save updated list
file_put_contents($regFile, json_encode($data, JSON_PRETTY_PRINT));

// Success
header("Location: dashboard.php?rescheduled=1");
exit();

?>
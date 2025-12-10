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

// Get exam index from URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php?error=invalid");
    exit();
}

$studentExamIndex = intval($_GET['id']);  // index in this student's list

// Step 1: Build list of this student's exams WITH global JSON indexes
$studentExams = [];
foreach ($registrations as $globalIndex => $exam) {
    if ($exam["student_id"] == $studentID) {
        $studentExams[] = $globalIndex;
    }
}

// Step 2: Validate index exists
if (!isset($studentExams[$studentExamIndex])) {
    header("Location: dashboard.php?error=notfound");
    exit();
}

// Step 3: The exam we need to modify
$globalIndexToEdit = $studentExams[$studentExamIndex];
$examData = $registrations[$globalIndexToEdit];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Reschedule Exam</title>
<link rel="stylesheet" href="stylesheet.css">

<style>
.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    margin-bottom: 10px;
    font-weight: bold;
    text-align: center;
}
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}
.calendar-day {
    padding: 12px;
    background: #ddd;
    border-radius: 6px;
    cursor: pointer;
    color: black;
}
.calendar-day:hover {
    background: #ccc;
    color: black;
}
.disabled-day {
    background: #eee;
    opacity: 0.4;
    cursor: not-allowed;
}
.selected-day {
    background: #007bff !important;
    color: white;
}
.calendar-container {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 8px;
}
.error { color: red; font-weight: bold; }
</style>

<script>
// Build calendar
function generateCalendar() {
    const grid = document.getElementById("calendarGrid");
    grid.innerHTML = "";

    const today = new Date();
    const maxDays = 14;
    const startDay = today.getDay();

    for (let i = 0; i < startDay; i++) {
        let blank = document.createElement("div");
        blank.classList.add("calendar-day");
        blank.style.visibility = "hidden";
        grid.appendChild(blank);
    }

    for (let i = 0; i < maxDays; i++) {
        let d = new Date();
        d.setDate(today.getDate() + i);

        let formatted = d.toISOString().split("T")[0];
        let weekday = d.getDay();

        let div = document.createElement("div");
        div.classList.add("calendar-day");
        div.textContent = d.getDate();
        div.dataset.date = formatted;

        if (weekday === 0 || weekday === 6) {
            div.classList.add("disabled-day");
            grid.appendChild(div);
            continue;
        }

        div.onclick = function() {
            document.querySelectorAll(".calendar-day").forEach(x => x.classList.remove("selected-day"));
            div.classList.add("selected-day");

            document.getElementById("exam_date").value = formatted;
            generateTimeSlots();
        };

        grid.appendChild(div);
    }
}

function generateTimeSlots() {
    const select = document.getElementById("exam_time");
    select.innerHTML = "<option value=''>Select Time</option>";

    let start = new Date("2024-01-01T10:00:00");
    let end = new Date("2024-01-01T16:00:00");

    while (start < end) {
        let endExam = new Date(start.getTime() + (2 * 60 * 60 * 1000));
        let nextStart = new Date(endExam.getTime() + (5 * 60 * 1000));

        let label = start.toTimeString().substring(0,5) +
                    " - " +
                    endExam.toTimeString().substring(0,5);

        let opt = document.createElement("option");
        opt.value = label;
        opt.textContent = label;
        select.appendChild(opt);

        start = nextStart;
    }
}

window.onload = generateCalendar;
</script>
</head>

<body>
<div class="container">
<main class="form-container">

<h1>🔄 Reschedule Exam</h1>

<p><strong>Student:</strong> <?= htmlspecialchars($studentName) ?> (NSHE <?= $studentID ?>)</p>

<p><strong>Course:</strong> <?= htmlspecialchars($examData["course_name"]) ?> (<?= htmlspecialchars($examData["course_code"]) ?>)</p>
<p><strong>Current Date:</strong> <?= htmlspecialchars($examData["exam_date"]) ?></p>
<p><strong>Current Time:</strong> <?= htmlspecialchars($examData["exam_time"]) ?></p>

<hr>

<form action="save_reschedule.php" method="post">

    <!-- Send global index -->
    <input type="hidden" name="global_index" value="<?= $globalIndexToEdit ?>">

    <label>Select New Date</label>
    <div class="calendar-container">

        <div class="calendar-weekdays">
            <div>Sun</div><div>Mon</div><div>Tue</div>
            <div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        <div id="calendarGrid" class="calendar-grid"></div>

        <input type="hidden" id="exam_date" name="exam_date" required>
    </div>

    <label>New Time</label>
    <select id="exam_time" name="exam_time" required>
        <option value="">Select Time</option>
    </select>

    <br><br>

    <div class="button-group">
        <button type="submit" class="btn">Save Changes</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">Cancel</button>
    </div>

</form>

</main>
</div>
</body>
</html>
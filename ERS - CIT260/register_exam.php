<?php
session_start();

// Require login
if (!isset($_SESSION['student_id'])) {
    header("Location: login_new.php?error=notloggedin");
    exit();
}

$studentID = $_SESSION["student_id"];
$studentName = $_SESSION["student_name"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Exam Registration</title>
<link rel="stylesheet" href="stylesheet.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
.error {
    color: red;
    font-weight: bold;
    margin-bottom: 12px;
}

/* ---- Calendar Styles ---- */
.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    margin-bottom: 10px;
    font-weight: bold;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

.calendar-day {
    padding: 12px;
    background: #eee;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
    user-select: none;
	color: black
}

.calendar-day:hover {
    background: #ccc;
}

.disabled-day {
    opacity: 0.3;
    background: #eee;
    cursor: not-allowed;
}

.selected-day {
    background: #007bff !important;
    color: black;
}

.calendar-container {
    background:rgb(12, 11, 11);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
}
</style>

<script>
// ------------------- Generate 14-Day Calendar (Weekdays Only) -------------------
function generateCalendar() {
    const calendarGrid = document.getElementById("calendarGrid");
    calendarGrid.innerHTML = "";

    const today = new Date();
    const maxDays = 14;

    // Add weekday labels automatically (Sun–Sat)
    const weekdays = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];

    let startDay = today.getDay();
    for (let i = 0; i < startDay; i++) {
        let blank = document.createElement("div");
        blank.classList.add("calendar-day", "disabled-day");
        blank.style.visibility = "hidden";
        calendarGrid.appendChild(blank);
    }

    for (let i = 0; i < maxDays; i++) {
        let d = new Date();
        d.setDate(today.getDate() + i);

        let formatted = d.toISOString().split("T")[0];
        let weekday = d.getDay();

        let div = document.createElement("div");
        div.classList.add("calendar-day");
        div.textContent = d.getDate();
        div.setAttribute("data-date", formatted);

        // Disable weekends
        if (weekday === 0 || weekday === 6) {
            div.classList.add("disabled-day");
            calendarGrid.appendChild(div);
            continue;
        }

        // On click → select date
        div.onclick = function() {
            document.querySelectorAll(".calendar-day").forEach(el => el.classList.remove("selected-day"));
            div.classList.add("selected-day");

            document.getElementById("exam_date").value = formatted;
            generateTimeSlots();
        };

        calendarGrid.appendChild(div);
    }
}

// ------------------- Time Slot Generator (10 AM → 4 PM, 2 hours + 5 min gap) -------------------
function generateTimeSlots() {
    const timeSelect = document.getElementById("exam_time");
    timeSelect.innerHTML = "<option value=''>Select Time</option>";

    let start = new Date("2024-01-01T10:00:00");
    let end = new Date("2024-01-01T16:00:00");

    while (start < end) {
        let endExam = new Date(start.getTime() + (2 * 60 * 60 * 1000));
        let nextStart = new Date(endExam.getTime() + (5 * 60 * 1000));

        let label =
            start.toTimeString().substring(0,5) +
            " - " +
            endExam.toTimeString().substring(0,5);

        let opt = document.createElement("option");
        opt.value = label;
        opt.textContent = label;
        timeSelect.appendChild(opt);

        start = nextStart;
    }
}

// ------------------- Course Codes -------------------
function updateCourseCodes() {
    const courseName = document.getElementById("course_name").value;
    const courseCodeSelect = document.getElementById("course_code");

    const options = {
        "MATH": ["MATH123", "MATH127", "MATH181"],
        "CIT":  ["CIT129", "CIT180", "CIT260"],
        "CS":   ["CS202", "CS135"]
    };

    courseCodeSelect.innerHTML = "<option value=''>Select Course Code</option>";

    if (options[courseName]) {
        options[courseName].forEach(code => {
            let opt = document.createElement("option");
            opt.value = code;
            opt.textContent = code;
            courseCodeSelect.appendChild(opt);
        });
    }
}

// ------------------- Test Room -------------------
function updateTestRoom() {
    const location = document.getElementById("exam_location").value;
    const testRoom = document.getElementById("test_room");

    const rooms = {
        "Charleston": "Test Center Building D Room 001",
        "NLV": "Test Center Building S Room 001",
        "Henderson": "Test Center Building A Room 011"
    };

    testRoom.value = rooms[location] || "";
}

window.onload = generateCalendar;
</script>

</head>

<body>
<div class="container">
<main class="form-container">

<h1>Register for an Exam</h1>

<p><strong>Student:</strong> <?= $studentName ?> (NSHE: <?= $studentID ?>)</p>

<!-- ------------ ERROR MESSAGES ------------ -->
<?php if (isset($_GET['error'])): ?>
    <p class="error">
        <?php
            if ($_GET['error'] === "duplicate_exam") echo "❗ You already registered for this course.";
            if ($_GET['error'] === "max_reached") echo "❗ You cannot register for more than 3 exams.";
            if ($_GET['error'] === "full_capacity") echo "❗ This exam time slot is full (20 seats). Choose another.";
        ?>
    </p>
<?php endif; ?>


<form action="save_exam.php" method="post">

    <!-- Student ID auto-filled & locked -->
    <div class="input-group">
        <label>NSHE ID</label>
        <input type="text" value="<?= $studentID ?>" name="student_id" readonly>
    </div>

    <!-- COURSE NAME -->
    <div class="input-group">
        <label>Course Name</label>
        <select id="course_name" name="course_name" onchange="updateCourseCodes()" required>
            <option value="">Select Course</option>
            <option value="MATH">MATH</option>
            <option value="CIT">CIT</option>
            <option value="CS">CS</option>
        </select>
    </div>

    <!-- COURSE CODE -->
    <div class="input-group">
        <label>Course Code</label>
        <select id="course_code" name="course_code" required>
            <option value="">Select Course Code</option>
        </select>
    </div>

    <!-- CAMPUS -->
    <div class="input-group">
        <label>Campus Location</label>
        <select id="exam_location" name="exam_location" onchange="updateTestRoom()" required>
            <option value="">Select Campus</option>
            <option value="Charleston">Charleston</option>
            <option value="NLV">North Las Vegas</option>
            <option value="Henderson">Henderson</option>
        </select>
    </div>

    <!-- TEST ROOM -->
    <div class="input-group">
        <label>Test Room</label>
        <input type="text" id="test_room" name="test_room" readonly required>
    </div>

    <!-- CALENDAR -->
    <label>Select Exam Date</label>
    <div class="calendar-container">
        <div class="calendar-weekdays">
            <div>Sun</div><div>Mon</div><div>Tue</div>
            <div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        <div id="calendarGrid" class="calendar-grid"></div>
        <input type="hidden" id="exam_date" name="exam_date" required>
    </div>

    <!-- TIME SLOT -->
    <div class="input-group">
        <label>Select Time</label>
        <select id="exam_time" name="exam_time" required>
            <option value="">Select a Time</option>
        </select>
    </div>


    <div class="button-group">
        <button type="submit" class="btn">Register</button>
        <button type="button" onclick="window.location.href='dashboard.php'">Back</button>
    </div>

</form>

</main>
</div>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    
    <link href="https://fonts.googleapis.com/css?family=Bacasime+Antique" rel="stylesheet">
    
    <script src="modernizr.custom.62074.js"></script>
    
    <style>
        /* Basic Styling for readability and visual appeal */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        /* 1. Form Container Styling */
        .signup-form {
            width: 450px; /* Set a fixed width for the form */
            margin: 0 auto; /* Center the form horizontally */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* 2. Flexbox for Alignment (The key to creating clean columns) */
        .form-group {
            display: flex; /* Activate flex layout for the row */
            align-items: center; /* Vertically center items */
            margin-bottom: 15px;
        }

        /* 3. Styling the Label Column */
        .form-group label {
            flex: 0 0 160px; /* Fixed width of 160px for the label (the first column) */
            text-align: right; /* Aligns label text to the right edge */
            padding-right: 15px;
            font-weight: bold;
        }

        /* 4. Styling the Input Column */
        .form-group input,
        .form-group select {
            flex: 1; /* Input takes up the rest of the available space (the second column) */
            padding: 8px;
            border: 1px solid #aaa;
            border-radius: 3px;
        }

        /* Styling for the buttons (placed under the inputs) */
        .button-group {
            display: flex;
            justify-content: flex-end; /* Align buttons to the right */
            padding-left: 175px; /* Offset buttons past the label width */
            gap: 10px;
        }
        
        .button-group input[type="reset"],
        .button-group input[type="submit"] {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            border-radius: 3px;
        }
        
        .button-group input[type="submit"] {
            background-color: #007bff;
            color: rgb(0, 0, 0);
        }
        .button-group button {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            border-radius: 3px;
            background-color: #6c757d; 
            color: rgb(0, 0, 0);
        }
    </style>
</head>
<body>

    <?php if (isset($_GET['error']) && $_GET['error'] === "duplicate"): ?>
    <p class="error-message" style="color: red; font-weight: bold;">
        ❗ This Student ID already exists. Please enter a different one.
    </p>

    <script>
        // Auto-reset the form when duplicate ID error appears
        window.onload = function() {
            document.getElementById("signup-form").reset();
        };
    </script>
<?php endif; ?>

    <form id="signup-form" class="signup-form" action="signup.php" method="POST">
        <h2>SIGN-UP FORM</h2>

        <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" name="fname" id="fname" placeholder="eg:James" required>
        </div>

        <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" name="lname" id="lname" placeholder="eg:Martinez" required>
        </div>

        <div class="form-group">
            <label for="stdid">Student ID</label>
            <input type="text" name="stdid" id="stdid" required>
        </div>

        <div class="form-group">
            <label for="email"> Student E-mail</label>
            <input type="email" name="email" id="email" placeholder="eg:5003425673@STUDENT.CSN.EDU" required>
        </div>

        <div class="form-group">
            <label for="course">Course</label>
            <select id="course" name="course">
                <option value="" disabled selected>Select a Course</option>
                <option value="CIT120">CIT120</option>
                <option value="CIT182">CIT182</option>
                <option value="CIT260">CIT260</option>
            </select>
        </div>

        <div class="form-group">
            <label for="pass">Password</label>
            <input type="password" id="pass" name="password" placeholder="Your Password.." required>
        </div>

        <div class="form-group">
            <label for="cpass">Confirm your Password</label>
            <input type="password" id="cpass" name="cpassword" placeholder="Re-enter password.." required>
        </div>
        
        <div class="button-group">
            <button type="button" onclick="window.location.href='login.html'">Go Back</button>  
            <input type="reset" value="Reset">
            <input type="submit" value="Register">
        </div>
    </form>
    <script>

    // RANDOM GENERATOR HELPERS
    function randomDigits(length) {
        let num = "";
        for (let i = 0; i < length; i++) {
            num += Math.floor(Math.random() * 10);
        }
        return num;
    }

    function randomPassword(length) {
        const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*";
        let pass = "";
        for (let i = 0; i < length; i++) {
            pass += chars[Math.floor(Math.random() * chars.length)];
        }
        return pass;
    }

    function randomItem(list) {
        return list[Math.floor(Math.random() * list.length)];
    }

    window.onload = function () {

        // ------ CASE 1: DUPLICATE STUDENT ID ERROR ------
        <?php if (isset($_GET['error']) && $_GET['error'] === "duplicate"): ?>
            // Reset the form ONLY
            document.getElementById("signupForm").reset();
            return; // Prevent test data autofill from running
        <?php endif; ?>

        // ------ CASE 2: AUTO-FILL TEST DATA ------
        const firstNames = ["Anthea", "Albert", "Sathya", "Paul"];
        const lastNames  = ["Dang", "Artiga", "Hewage", "Gabriel Paulino"];
        
        const i = Math.floor(Math.random() * firstNames.length);

        let fname = firstNames[i];
        let lname = lastNames[i];

        let studentID = randomDigits(10);
        let email = studentID + "@student.csn.edu";

        let courseList = ["CIT120", "CIT182", "CIT260"];
        let course = randomItem(courseList);

        let password = randomPassword(12);

        document.getElementById("fname").value = fname;
        document.getElementById("lname").value = lname;
        document.getElementById("stdid").value = studentID;
        document.getElementById("email").value = email;
        document.getElementById("course").value = course;
        document.getElementById("pass").value = password;
        document.getElementById("cpass").value = password;
    };
</script>
</body>
</html>
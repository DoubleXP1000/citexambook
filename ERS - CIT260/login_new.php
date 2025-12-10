<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="modernizr.custom.62074.js"></script>
    <link rel="stylesheet" href="stylesheet.css">
    <link href="https://fonts.googleapis.com/css?family=Bacasime+Antique" rel="stylesheet">
    <title>Please Login</title>

    <style>
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            width: 380px;
        }
    </style>
</head>

<body>
    <div class="container">

        <main class="form-container">

            <h1>Welcome to CSN Exam Registration</h1>
            <h2>Enter your Login Information or Sign-Up</h2>

            <!-- AUTOFILL VALUES FROM URL -->
            <?php
                $autoUser = isset($_GET['user']) ? $_GET['user'] : "";
                $autoPass = isset($_GET['pass']) ? $_GET['pass'] : "";
            ?>

            <!-- SUCCESS MESSAGE AFTER REGISTER -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="success-message">
                    Your Account Created, Please log in again!
                </div>
            <?php endif; ?>

            <form action="login_process.php" method="post">

                <div class="input-group">
                    <label for="user">Username</label>
                    <input 
                        type="text" 
                        id="user" 
                        name="username" 
                        placeholder="Your Username.."
                        value="<?php echo htmlspecialchars($autoUser); ?>"
                        required>
                </div>

                <div class="input-group">
                    <label for="pass">Password</label>
                    <input 
                        type="password" 
                        id="pass" 
                        name="password" 
                        placeholder="Your Password.."
                        value="<?php echo htmlspecialchars($autoPass); ?>"
                        required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">Login</button>

                    <button type="button" class="btn btn-secondary"
                            onclick="window.location.href='new_signup_page.php'">
                        Sign-Up
                    </button>
                </div>

            </form>
        </main>
    </div>
</body>
</html>